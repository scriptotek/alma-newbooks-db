<?php

namespace App\Console\Commands;

use App\Document;
use App\Jobs\HarvestPrintBooksReport;
use App\Jobs\HarvestEBooksReport;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class HarvestCommand extends Command
{

    protected $reports = [
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        \Log::info("Starting harvest: Fetching last {$days} days of records");

        $job = (new HarvestPrintBooksReport($days)); // ->onConnection('sync');
        dispatch($job);

        $job = (new HarvestEBooksReport($days)); // ->onConnection('sync');
        dispatch($job);

        $this->info("Submitted harvest jobs for last {$days} days of records");
    }
}
