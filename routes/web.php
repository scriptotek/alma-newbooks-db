<?php

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

Route::resource('users', 'UsersController', ['only' => [
    'index', 'show', 'edit', 'update'
]]);

Route::get('my-orders', 'MyOrdersController@index');

Route::get('lists/{report}/rss', 'ReportsController@rss');
Route::get('lists/{report}/month/{month}', 'ReportsController@byMonth');
Route::get('lists/{report}/week/{week}', 'ReportsController@byWeek');
Route::get('lists/{report}/delete', 'ReportsController@delete');

Route::get('lists', 'ReportsController@index')->name('reports.index');
Route::get('lists/preview', 'ReportsController@preview');
Route::get('lists/create', 'ReportsController@create')->name('reports.create');
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

