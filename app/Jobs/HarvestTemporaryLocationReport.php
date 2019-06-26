<?php

namespace App\Jobs;

use App\Document;

class HarvestTemporaryLocationReport extends HarvestAnalytics
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    protected $createIfNotExists = false;

    protected $path;

    protected $headers = [
        Document::ITEM_ID,
        'temporary_library_name',
        'temporary_location_name',
    ];

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->path = config('alma.reports.temporary_location');
    }
}
