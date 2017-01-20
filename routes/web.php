<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('documents', 'DocumentsController', ['only' => [
    'index', 'show'
]]);

Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');
Route::resource('users', 'UsersController', ['only' => [
    'index', 'show', 'edit', 'update'
]]);

Route::get('my-orders', 'MyOrdersController@index');

Route::get('lists/{report}/rss', 'ReportsController@rss');

Route::get('lists/{report}/month/{month}.rss', function (Request $request, $report, $month) {
	$input = $request->all();
	$input['format'] = 'rss';
	$input['report'] = $report;
	$input['month'] = $month;
	return redirect()->route('reports.month', $input);
})->name('reports.month.rss');
Route::get('lists/{report}/month/{month}', 'ReportsController@byMonth')->name('reports.month');

Route::get('lists/{report}/week/{week}.rss', function (Request $request, $report, $week) {
	$input = $request->all();
	$input['format'] = 'rss';
	$input['report'] = $report;
	$input['week'] = $week;
	return redirect()->route('reports.week', $input);
})->name('reports.week.rss');
Route::get('lists/{report}/week/{week}', 'ReportsController@byWeek')->name('reports.week');

Route::get('lists/{report}/delete', 'ReportsController@delete');
Route::get('lists', 'ReportsController@index')->name('reports.index');
Route::get('lists/preview', 'ReportsController@preview');
Route::get('lists/create', 'ReportsController@create')->name('reports.create');

Route::get('lists/{report}.unreceived.rss', function (Request $request, $report) {
	$input = $request->all();
	$input['format'] = 'rss';
	$input['report'] = $report;
	$input['received'] = 'false';
	return redirect()->route('reports.show', $input);
})->name('reports.show.unreceived.rss');
Route::get('lists/{report}.rss', function (Request $request, $report) {
	$input = $request->all();
	$input['format'] = 'rss';
	$input['report'] = $report;
	return redirect()->route('reports.show', $input);
})->name('reports.show.rss');
Route::get('lists/{report}', 'ReportsController@show')->name('reports.show');

Route::get('lists/{report}/edit', 'ReportsController@edit')->name('reports.edit');
Route::post('lists', 'ReportsController@store')->name('reports.store');
Route::put('lists/{report}', 'ReportsController@update')->name('reports.update');
// Route::resource('lists', 'ReportsController');

Route::get('templates/preview', 'TemplatesController@preview');
Route::resource('templates', 'TemplatesController');

Route::get('saml2/error', 'Auth\LoginController@error');

Auth::routes();
Route::post('logout', 'Auth\LoginController@samlLogout');  // override POST route

