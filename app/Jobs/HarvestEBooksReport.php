<?php

namespace App\Jobs;

use App\Document;

class HarvestEBooksReport extends HarvestAnalytics
{
    protected $createIfNotExists = true;

    protected $path = '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_electronic';

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
        'edition',
        'isbn',
        Document::MMS_ID,
        'bib_modification_date',
        'publication_date',
        'publisher',
        'series',
        'title',
        'dewey_classification',
        'collection_name',
        'activation_date',
        'portfolio_creation_date',
        'library_name',
        Document::PO_ID,
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
        $this->filter = sprintf($this->filter, $days);
    }
}
