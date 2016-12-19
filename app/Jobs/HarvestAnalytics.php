<?php

namespace App\Jobs;

use App\Change;
use App\Document;
use App\Importers\AnalyticsReportImporter;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Scriptotek\Alma\Analytics\Report;
use Scriptotek\Alma\Client as AlmaClient;

class HarvestAnalytics implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $alma;
    protected $createIfNotExists = true;
    protected $path;
    protected $filter;
    protected $headers;

    /**
     * List of changes we're not interested in tracking.
     */
    protected $ignoredChanges = [
        'dewey_classification_top_line',
    ];

    protected function handleChange(Document $doc, $key, $old_value, $new_value)
    {
        if (in_array($key, $this->ignoredChanges)) {
            return;
        }

        Change::create([
            'document_id' => $doc->id,
            'key'         => $key,
            'old_value'   => $old_value,
            'new_value'   => $new_value,
        ]);

        if ($key == 'permanent_call_number' && is_null($old_value)) {
            // Permanent call number was assigned yesterday.
            // We use this as a measure of the cataloging date.
            $doc->cataloged_at = Carbon::now()->subDay();
        }
    }

    /**
     * @param Report $report
     */
    protected function importReport(Report $report)
    {
        $keyCache = [];
        $n = 0; $m = 0; $cn = 0; $cm = 0;
        foreach ($report->rows as $row) {
            $n++;
            $doc = AnalyticsReportImporter::docFromRow($row->toArray(), $this->createIfNotExists, $keyCache);
            if (is_null($doc)) {
                continue;
            }

            if (!$doc->exists) {
                $cn++;
                \Log::debug('New document.', [
                    'date' => $doc->receiving_or_activation_date,
                    'po_line' => $doc->po_line,
                    'barcode' => $doc->barcode,
                ]);

            } else if ($doc->isDirty()) {
                $cm++;
                foreach ($doc->getDirty() as $k => $v) {
                    $this->handleChange($doc, $k, $doc->getOriginal($k), $v);

                    \Log::debug('Updated existing document.', [
                        'id' => $doc->id,
                        'attribute' => $k,
                        'old_value' => $doc->getOriginal($k),
                        'new_value' => $v
                    ]);
                }
            }

            $doc->save();

            $m++;
        }
        \Log::info(get_class($this) . ' completed.', [
            'docs_checked' => $n,
            'new' => $cn,
            'modified' => $cm,
        ]);
    }

    /**
     * Execute the job.
     *
     * @param AlmaClient $alma
     */
    public function handle(AlmaClient $alma)
    {
        $report = $alma->analytics->get($this->path, $this->headers, $this->filter);
        $this->importReport($report);
    }
}
