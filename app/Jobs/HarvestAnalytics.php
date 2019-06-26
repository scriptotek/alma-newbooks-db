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

abstract class HarvestAnalytics implements ShouldQueue
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

        if ($key == 'process_type' && is_null($doc->ready_at)) {
            // Process type changed yesterday.
            // We use this as a measure of the ready date.
            $doc->ready_at = Carbon::now()->subDay();
        }

    }

    protected function saved(Document $doc)
    {
        // To be overriden
    }

    protected function complete()
    {
        // To be overriden
    }

    /**
     * @param Report $report
     */
    protected function importReport(Report $report)
    {
        $shortName = basename($report->path);
        $keyCache = [];
        $n = 0; $m = 0; $cn = 0; $cm = 0;
        foreach ($report->rows as $row) {
            $n++;
            $doc = AnalyticsReportImporter::docFromRow($row->toArray(), $this->createIfNotExists, $keyCache, $report);
            if (is_null($doc)) {
                continue;
            }

            if (!$doc->exists) {
                $cn++;
                \Log::debug("[$shortName] Imported new document.", [
                    'mms_id' => $doc->mms_id,
                    'sent' => $doc->{Document::SENT_DATE},
                    'received' => $doc->{Document::RECEIVING_OR_ACTIVATION_DATE},
                    'po_line' => $doc->{Document::PO_ID},
                ]);

            } else if ($doc->isDirty()) {
                $cm++;
                foreach ($doc->getDirty() as $k => $v) {
                    $this->handleChange($doc, $k, $doc->getOriginal($k), $v);

                    \Log::debug("[$shortName] Updated existing document.", [
                        'id' => $doc->id,
                        'attribute' => $k,
                        'old_value' => $doc->getOriginal($k),
                        'new_value' => $v
                    ]);
                }
            }

            $doc->save();
            $this->saved($doc);

            $m++;
        }
        \Log::info("[$shortName] Import completed.", [
            'docs_checked' => $n,
            'new' => $cn,
            'modified' => $cm,
        ]);
        $this->complete();
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
