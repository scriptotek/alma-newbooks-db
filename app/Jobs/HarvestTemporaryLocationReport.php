<?php

namespace App\Jobs;

use App\Document;

class HarvestTemporaryLocationReport extends HarvestAnalytics
{
    protected $createIfNotExists = false;

    protected $path = '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/temporary_location';

    protected $headers = [
        Document::ITEM_ID,
        'temporary_library_name',
        'temporary_location_name',
    ];
}
