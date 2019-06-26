<?php

namespace App\Jobs;

use App\Document;

class HarvestEBooksReport extends HarvestAnalytics
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
           <sawx:expr xsi:type="sawx:sqlExpression">"E-Inventory"."Portfolio Activation Date"."Portfolio Activation Date"</sawx:expr>
           <sawx:expr xsi:type="sawx:sqlExpression">TIMESTAMPADD(SQL_TSI_DAY, -%d, CURRENT_DATE)</sawx:expr>
        </sawx:expr>';

    protected $headers = [
        'author',
        'bibliographic_level',
        'bib_creation_date',
        'dewey_classification',
        'edition',
        'isbn',
        Document::MMS_ID,
        'bib_modification_date',
        'publication_date',
        'publisher',
        'series',
        'title',
        'collection_name',
        'activation_date',
        'portfolio_creation_date',
        'library_name',
        'additional_order_reference',
        Document::PO_ID,
        Document::REPORTING_CODE_SECONDARY,
        Document::REPORTING_CODE_TERTIARY,
        Document::REPORTING_CODE,
        'po_creator',
        'material_type',
        Document::PORTFOLIO_ID,
    ];

    /**
     * Create a new job instance.
     *
     * @param int $days Number of past days to get records for
     */
    public function __construct($days)
    {
        $this->path = config('alma.reports.new_electronic');
        $this->filter = sprintf($this->filter, $days);
    }
}
