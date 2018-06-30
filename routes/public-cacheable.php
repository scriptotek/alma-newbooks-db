<?php

use Carbon\Carbon;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public Cacheable Routes
|--------------------------------------------------------------------------
|
| These routes do not receive session state, CSRF protection, etc.
| See RouteServiceProvider.php and Http/Kernel.php for details.
*/

// Redirect old-style format (/lists/{route}/rss) to new-style format (/lists/{route}.rss)
Route::get('lists/{report}/rss', function($id) {
    return redirect()
        ->action('ReportsController@showData', ['id' => $id, 'format' => 'rss'])
        ->withInput()
        ->setPublic()
        ->setExpires(Carbon::now()->addMonth());
});

function redirectToDataOrView(Request $request, $dataRoute, $viewRoute) {
    $all = array_merge($request->route()->parameters(), $request->input());
    $format = $request->input('format');
    if ($format) {
        $response = redirect()->action($dataRoute, $all);
    } else {
        $response = redirect()->action($viewRoute, $all);
    }
    return $response->setPublic()
        ->setExpires(Carbon::now()->addMonth());
}

// Redirect old-style format (/lists/{route}?format=rss) to new-style format (/lists/{route}.rss)
Route::get('lists/{report}', function(Request $request) {
    return redirectToDataOrView($request, 'ReportsController@showData', 'ReportsController@show');
});

Route::get('lists/{report}.{format}', 'ReportsController@showData')->name('reports.data');


// Redirect old-style format (/lists/{route}?format=rss) to new-style format (/lists/{route}.rss)
Route::get('lists/{report}/month/{month}', function(Request $request) {
    return redirectToDataOrView($request, 'ReportsController@byMonthData', 'ReportsController@byMonth');
});

Route::get('lists/{report}/month/{month}.{format}', 'ReportsController@byMonthData')->name('reports.month.data');

// Redirect old-style format (/lists/{route}?format=rss) to new-style format (/lists/{route}.rss)
Route::get('lists/{report}/week/{week}', function(Request $request) {
    return redirectToDataOrView($request, 'ReportsController@byWeekData', 'ReportsController@byWeek');
});

Route::get('lists/{report}/week/{week}.{format}', 'ReportsController@byWeekData')->name('reports.week.data');
