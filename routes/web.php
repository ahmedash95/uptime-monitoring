<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
	Route::resource('/websites','WebsiteController');

	Route::get('/','RequestsMonitoring@home');
	Route::get('/requests','RequestsMonitoring@index');
	Route::post('/requests','RequestsMonitoring@store');


	Route::get('/home', 'HomeController@index');
});
