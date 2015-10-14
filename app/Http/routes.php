<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::post('/ping', 'MainController@ping');
Route::post('/settings', 'SettingsController@save');
Route::get('/settings', 'SettingsController@index');
Route::get('/settings/facebook/login', 'SettingsController@login');
Route::get('/settings/facebook/callback', 'SettingsController@callback');
Route::post('/', 'MainController@post');
Route::get('/', 'MainController@index');

