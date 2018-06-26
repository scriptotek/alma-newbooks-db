<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Max number of elements
    |--------------------------------------------------------------------------
    |
    | Max number of elements to include in RSS feeds.
    */

    'limit' => intval(env('RSS_LIMIT', '200')),

];
