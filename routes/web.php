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

Route::get('reports/{report}/rss', 'ReportsController@rss');
Route::get('reports/preview', 'ReportsController@preview');
Route::get('reports/{report}/delete', 'ReportsController@delete');
Route::resource('reports', 'ReportsController');


Route::get('saml2/error', 'Auth\LoginController@error');

Auth::routes();
Route::post('logout', 'Auth\LoginController@samlLogout');  // override POST route
