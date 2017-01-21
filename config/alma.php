<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | Region code
    |--------------------------------------------------------------------------
    */
    'region' => env('ALMA_REGION', 'eu'),

    /*
    |--------------------------------------------------------------------------
    | Institution zone settings
    |--------------------------------------------------------------------------
    */
    'iz' => [
        'key' => env('ALMA_IZ_KEY'),
        'sru' => env('ALMA_IZ_SRU_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Network zone settings (not used for anything yet)
    |--------------------------------------------------------------------------
    */
    'nz' => [
        'key' => env('ALMA_NZ_KEY'),
        'sru' => env('ALMA_NZ_SRU_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Employee group name
    |--------------------------------------------------------------------------
    |
    | Only allow members of this Alma group to login. Use the group *name*,
    | not code.
    |
    */
    'employee_group' => env('ALMA_EMPLOYEE_GROUP'),

    /*
    |--------------------------------------------------------------------------
    | Analytics reports
    |--------------------------------------------------------------------------
    */
    'reports' => [
        'new_electronic' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_electronic',
        'new_print' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/new_physical',
        'po_lines' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/po_lines',
        'temporary_location' => '/shared/UIO,Universitetsbiblioteket/Reports/Nyhetslister/temporary_location',
    ],

);
