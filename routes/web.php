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

Route::get('/healthcheck', 'HomeController@healthCheck')
    ->name('healthcheck');

// lastly, the vue route
Route::get('/{vue?}', 'HomeController@home')
    ->where('vue', '[\/\w\.-]*')
    ->name('home');

// add common name route for use on server-side
Route::get('/login', 'HomeController@home')
    ->name('login');
