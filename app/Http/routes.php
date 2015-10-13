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



Route::get('/test', 'MainController@test');
Route::post('/settings', 'SettingsController@save');
Route::get('/settings', 'SettingsController@index');
Route::get('/settings/facebook/login', 'SettingsController@login');
Route::get('/settings/facebook/callback', 'SettingsController@callback');
Route::get('/', 'MainController@index');
