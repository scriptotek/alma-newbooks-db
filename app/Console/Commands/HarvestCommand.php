<?php

namespace App\Console\Commands;

use App\Document;
use App\Importers\AnalyticsReportImporter;
use Illuminate\Console\Command;
use Scriptotek\Alma\Analytics\Report;
use Scriptotek\Alma\Client as AlmaClient;

class HarvestCommand extends Command
{

    protected $reports = [
        [
            'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_physical',
            'headers' => [
                'author',
                'bibliographic_level',
                'dewey_classification_top_line',
                'dewey_classification',
                'edition',
                Document::MMS_ID,
                'publication_date',
                'publication_place',
                'publisher',
                'series',
                'title',
                'holding_id',
                'permanent_call_number',
                'library_name',
                'location_name',
                'barcode',
                'base_status',
                'item_creation_date',
                'item_creator',
                'item_id',
                'item_policy',
                'last_loan_date',
                'material_type',
                'process_type',
                'receiving_date_only',  // TODO: Delete from report
                'receiving_date',
                'temporary_physical_location',
                'acquisition_method',
                Document::PO_ID,
                'receiving_note',
                'receiving_status',
                'sent_date',
                // 'temporary_library_code',
                // 'temporary_library_name',
                // 'temporary_location_name',
                'isbn',
            ],
        ],
        [
            'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_electronic',
            'headers' => [
                'author',
                'bibliographic_level',
                'dewey_classification',
                'edition',
                'isbn',
                Document::MMS_ID,
                'publication_date',
                'publisher',
                'series',
                'title',
                'dewey_number',
                'public_name',
                'institution_name',
                'activation_date',
                'portfolio_creation_date',
                'library_code',
                'library_name',
                Document::PO_ID,
                'status',
                'availability',
                'creator',
                'is_free',
                'life_cycle',
                'material_type',
                'portfolio_id',
            ]
        ],
        [
            'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/modified_po_lines',
            'create' => false,
            'headers' => [
                Document::MMS_ID,
                'fund_ledger_code',
                'fund_ledger_name',
                'fund_type',
                'acquisition_method',
                'additional_order_reference',
                'cancellation_reason',
                'order_line_type_code',
                'order_line_type',
                'po_creation_date',
                'po_creator',
                'po_creation_date',
                'po_creator',
                'po_modification_date',
                'po_modified_by',
                Document::PO_ID,
                'receiving_note',
                'receiving_status',
                'reporting_code',
                'sent_date',
                'source_type',
                'vendor_code',
            ]
        ],
        [
            'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/modified_bibliographic',
            'create' => false,
            'headers' => [
                Document::MMS_ID,
                'biblio_modified',
                'biblio_modifiedby',
            ]
        ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest Analytics report data';

    protected $alma;

    /**
     * Create a new command instance.
     * @param AlmaClient $alma
     */
    public function __construct(AlmaClient $alma)
    {
        $this->alma = $alma;
        parent::__construct();
    }

    /**
     * @param Report $report
     * @param string[] $headers
     * @param boolean $create
     */
    public function importReport(Report $report, $headers, $create)
    {
        $this->info('Fetching ' . $report->path);
        $report->setHeaders($headers);

        $n = 0; $m = 0;
        foreach ($report->rows as $row) {
            if (AnalyticsReportImporter::importRowFromApi($row->toArray(), $create)) {
                $m++;
            }
            $n++;
            if ($n % 1000 == 0) {
                $this->info('Imported ' . $m . ' of ' . $n . ' rows');
            }
        }
        $this->info('Imported ' . $m . ' of ' . $n . ' rows');
    }

    /**
     * @param string $path
     * @param string[] $headers
     * @param boolean $create
     */
    public function fetch($path, $headers, $create)
    {
        $report = $this->alma->analytics[$path];
        $this->importReport($report, $headers, $create);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $n = 0;
        foreach ($this->reports as $report) {
            //if ($n > 2)
                $this->fetch($report['path'], $report['headers'], array_get($report, 'create', true));
            $n++;
        }

        $this->info('DONE');
    }
}
