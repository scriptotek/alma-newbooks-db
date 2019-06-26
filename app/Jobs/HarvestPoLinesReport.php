<?php

namespace App\Jobs;

use App\Document;

class HarvestPoLinesReport extends HarvestAnalytics
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    protected $createIfNotExists = true;

    protected $path;

    protected $filter = '';

    protected $po_line_ids = [];

    protected $headers = [
        'author',
        'edition',
        'isbn',
        Document::MMS_ID,
        'bib_modification_date',
        'publication_date',
        'publication_place',
        'publisher',
        'series',
        'title',
        'dewey_classification',
        'fund_ledger_code',
        'fund_ledger_name',
        'fund_type',
        'dewey_classification_top_line',
        'library_name',
        'acquisition_method',
        'additional_order_reference',
        'expected_activation_date',
        'expected_receiving_date',
        'note_to_vendor',
        'order_line_type_code',
        'order_line_type',
        'po_creation_date',
        'po_creator',
        'po_modification_date',
        Document::PO_ID,
        'receiving_note',
        'receiving_status',
        'sent_date',
        'source_type',
        Document::REPORTING_CODE,
        Document::REPORTING_CODE_SECONDARY,
        Document::REPORTING_CODE_TERTIARY,
        'vendor_code',

        // 'bibliographic_level',
        // 'holding_id',
        // 'permanent_call_number',
        // 'location_name',
        // 'barcode',
        // 'base_status',
        // 'item_creation_date',
        // 'item_creator',
        // Document::ITEM_ID,
        // 'item_policy',
        // 'material_type',
        // 'process_type',
        // 'receiving_date',
        // 'po_modified_by',
    ];

    /**
     * Create a new job instance.
     *
     * @param int $days Number of past days to get records for
     */
    public function __construct($days)
    {
        $this->path = config('alma.reports.po_lines');
        $this->filter = sprintf($this->filter, $days);
        $this->po_line_ids = [];
    }

    protected function saved(Document $doc)
    {
        $this->po_line_ids[] = $doc->{Document::PO_ID};
    }

    protected function complete()
    {
        // When an item is received, it will be imported from
        // HarvestPrintBooksReport, but won't be matched with the corresponding
        // row from this report since we don't have item IDs here. So to avoid
        // duplicates we need to purge the rows that are no longer part of
        // the po lines report.

        $to_delete = [];
        foreach (Document::whereNull(Document::RECEIVING_OR_ACTIVATION_DATE)->get() as $doc) {
            if (!in_array($doc->{Document::PO_ID}, $this->po_line_ids)) {
                $to_delete[] = $doc->id;
            }
        }
        $shortName = basename($this->path);
        \Log::info("[$shortName] Purging " . count($to_delete) . " rows from DB");

        Document::destroy($to_delete);
    }

}
