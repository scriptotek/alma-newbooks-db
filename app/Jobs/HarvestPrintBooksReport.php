<?php

namespace App\Jobs;

use App\Document;

class HarvestPrintBooksReport extends HarvestAnalytics
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    protected $createIfNotExists = true;

    protected $path;

    protected $filter = '
        <sawx:expr op="greaterOrEqual" xsi:type="sawx:comparison"
               xmlns:sawx="com.siebel.analytics.web/expression/v1.1"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
           <sawx:expr xsi:type="sawx:sqlExpression">"Physical Item Details"."Receiving   Date"</sawx:expr>
           <sawx:expr xsi:type="sawx:sqlExpression">TIMESTAMPADD(SQL_TSI_DAY, -%d, CURRENT_DATE)</sawx:expr>
        </sawx:expr>';

    protected $headers = [
        'author',
        'bibliographic_level',
        'bib_creation_date',
        'dewey_classification_top_line',
        'dewey_classification',
        'edition',
        Document::MMS_ID,
        'bib_modification_date',
        'publication_date',
        'publication_place',
        'publisher',
        'series',
        'title',
        'fund_ledger_code',
        'fund_ledger_name',
        'fund_type',
        'holding_id',
        'permanent_call_number_type',
        'permanent_call_number',
        'library_name',
        'location_name',
        'barcode',
        'base_status',
        'item_creation_date',
        'item_creator',
        Document::ITEM_ID,
        'item_policy',
        'material_type',
        'process_type',
        'receiving_date',
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
        Document::REPORTING_CODE_SECONDARY,
        Document::REPORTING_CODE_TERTIARY,
        Document::REPORTING_CODE,
        'sent_date',
        'source_type',
        'vendor_code',
        'isbn',
    ];

    /**
     * Create a new job instance.
     *
     * @param int $days Number of past days to get records for
     */
    public function __construct($days)
    {
        $this->path = config('alma.reports.new_print');
        $this->filter = sprintf($this->filter, $days);
    }
}
