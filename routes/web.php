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

Route::get('documents/reset', 'DocumentsController@resetForm');
Route::resource('documents', 'DocumentsController', ['only' => [
    'index', 'show'
]]);

Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');
Route::resource('users', 'UsersController', ['only' => [
    'index', 'show', 'edit', 'update'
]]);

Route::get('my-orders', 'MyOrdersController@index');

Route::get('lists/{report}/month/{month}/view', 'ReportsController@byMonth')->name('reports.month');
Route::get('lists/{report}/week/{week}/view', 'ReportsController@byWeek')->name('reports.week');

Route::get('lists/{report}/delete', 'ReportsController@delete');
Route::delete('lists/{report}', 'ReportsController@destroy');
Route::get('lists', 'ReportsController@index')->name('reports.index');
Route::get('lists/preview', 'ReportsController@preview');
Route::get('lists/create', 'ReportsController@create')->name('reports.create');

Route::get('lists/{report}/view', 'ReportsController@show')->name('reports.show');
Route::get('lists/{report}/edit', 'ReportsController@edit')->name('reports.edit');
Route::post('lists', 'ReportsController@store')->name('reports.store');
Route::put('lists/{report}', 'ReportsController@update')->name('reports.update');

// Route::resource('lists', 'ReportsController');

Route::get('templates/preview', 'TemplatesController@preview');
Route::get('templates/{report}/delete', 'TemplatesController@delete');
Route::resource('templates', 'TemplatesController');

Route::get('saml2/error', 'Auth\LoginController@error');

Auth::routes();
Route::post('logout', 'Auth\LoginController@samlLogout');  // override POST route

