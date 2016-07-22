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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', 'HomeController@index');
Route::post('/login', 'UAuth@login');

Route::group(['middleware' => 'auth:api'], function () {
Route::post('/logout','UAuth@logout');
Route::post('/change-password','UAuth@changpasswd');
Route::get('/crud','Crud@index');
Route::post('/crud','Crud@newu');
Route::get('/crud/{id}','Crud@detail');
Route::delete('/crud/{id}','Crud@del');
Route::put('/crud/{id}','Crud@update');

});