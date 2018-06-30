<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public Cacheable Routes
|--------------------------------------------------------------------------
|
| These routes do not receive session state, CSRF protection, etc.
| See RouteServiceProvider.php and Http/Kernel.php for details.
*/

Route::get('lists/{report}/rss', 'ReportsController@rss');

Route::get('lists/{report}.{format}', 'ReportsController@showData')->name('reports.data');
Route::get('lists/{report}/month/{month}.{format}', 'ReportsController@byMonthData')->name('reports.month.data');
Route::get('lists/{report}/week/{week}.{format}', 'ReportsController@byWeekData')->name('reports.week.data');
