<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', "\App\Http\Controllers\Front\Home@index");
Route::post('/', "\App\Http\Controllers\Front\Home@filter");
//Route::post('/bookings', "\App\Http\Controllers\Front\BookingController@filter");
//Route::get('/bookings', "\App\Http\Controllers\Front\BookingController@filter");
//Route::get('/bookings', "\App\Http\Controllers\Front\BookingController@index");
