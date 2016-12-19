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

Route::get('/', 'RateController@index');
Route::post('rate/uploadfile', 'RateController@uploadfile');
Route::post('rate/add', 'RateController@add');
Route::post('rate/checkBand', 'RateController@checkBand');
Route::post('rate/rate', 'RateController@rate');
Route::post('rate/rateBand', 'RateController@rateBand');
Route::get('rate/login', 'RateController@login')->name('login');;
Route::get('rate/logout', 'RateController@logout')->name('logout');;
Route::get('rate/callback', 'RateController@callback');


