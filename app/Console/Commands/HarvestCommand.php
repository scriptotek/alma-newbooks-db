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
            'filter' => '
                <sawx:expr op="greaterOrEqual" xsi:type="sawx:comparison"
                       xmlns:sawx="com.siebel.analytics.web/expression/v1.1"
                       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                   <sawx:expr xsi:type="sawx:sqlExpression">"Physical Item Details"."Receiving   Date"</sawx:expr>
                   <sawx:expr xsi:type="sawx:sqlExpression">TIMESTAMPADD(SQL_TSI_DAY, -%d, CURRENT_DATE)</sawx:expr>
                </sawx:expr>',
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
                'fund_ledger_code',
                'fund_ledger_name',
                'fund_type',
                'holding_id',
                'permanent_call_number',
                'library_name',
                'location_name',
                'barcode',
                'base_status',
                'item_creation_date',
                'item_creator',
                Document::ITEM_ID,
                'item_policy',
                'last_loan_date',
                'material_type',
                'process_type',
                'receiving_date_only',  // TODO: Delete from report
                'receiving_date',
                'temporary_physical_location',
                'acquisition_method',
                'additional_order_reference',
                'cancellation_reason',
                'order_line_type_code',
                'order_line_type',
                'po_creation_date',
                'po_creator',
                'po_modified_by',
                Document::PO_ID,
                'po_modification_date',
                'receiving_note',
                'receiving_status',
                'reporting_code',
                'sent_date',
                'source_type',
                'vendor_code',
                'isbn',
            ],
        ],
        [
            'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_electronic',
            'filter' => '
                <sawx:expr op="greaterOrEqual" xsi:type="sawx:comparison"
                       xmlns:sawx="com.siebel.analytics.web/expression/v1.1"
                       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                   <sawx:expr xsi:type="sawx:sqlExpression">"E-Inventory"."Portfolio Activation Date"."Portfolio Activation Date"</sawx:expr>
                   <sawx:expr xsi:type="sawx:sqlExpression">TIMESTAMPADD(SQL_TSI_DAY, -%d, CURRENT_DATE)</sawx:expr>
                </sawx:expr>',
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
                'po_creator',
                Document::PO_ID,
                'status',
                'availability',
                'creator',
                'is_free',
                'material_type',
                Document::PORTFOLIO_ID,
            ]
        ],

        /* @TODO: Additional reports:

             - modified_bibliographic: use to get notified about updates to the
               bibliographic record after the document is no longer new
               (catalogued or classified long after it was received)
             - modifed_items or holdings: use to get notified about call number
               changes in the same way.
        */

        // [
        //     'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/modified_po_lines',
        //     'create' => false,
        //     'headers' => [
        //         Document::MMS_ID,
        //         'fund_ledger_code',
        //         'fund_ledger_name',
        //         'fund_type',
        //         'acquisition_method',
        //         'additional_order_reference',
        //         'cancellation_reason',
        //         'order_line_type_code',
        //         'order_line_type',
        //         'po_creation_date',
        //         'po_creator',
        //         'po_creation_date',
        //         'po_creator',
        //         'po_modification_date',
        //         'po_modified_by',
        //         Document::PO_ID,
        //         'receiving_note',
        //         'receiving_status',
        //         'reporting_code',
        //         'sent_date',
        //         'source_type',
        //         'vendor_code',
        //     ]
        // ],
        // [
        //     'path' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/modified_bibliographic',
        //     'create' => false,
        //     'headers' => [
        //         Document::MMS_ID,
        //         'biblio_modified',
        //         'biblio_modifiedby',
        //     ]
        // ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest {days=60}';

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
    public function importReport(Report $report, $create)
    {
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
     * @param string $filter
     * @param boolean $create
     */
    public function fetch($path, $headers, $filter, $create)
    {
        $report = $this->alma->analytics->get($path, $headers, $filter);
        $this->importReport($report, $create);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');

        $n = 0;
        foreach ($this->reports as $report) {
            $path = $report['path'];

            $this->info("Fetching last {$days} days of records from {$path}");

            $filter = sprintf($report['filter'], $days);

            //if ($n > 2)
                $this->fetch($path, $report['headers'], $filter, array_get($report, 'create', true));
            $n++;
        }

        $this->info('DONE');
    }
}
