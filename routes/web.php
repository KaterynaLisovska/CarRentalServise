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

Route::get('/', "\App\Http\Controllers\Front\HomeCop@index");
Route::post('/', "\App\Http\Controllers\Front\HomeCop@filter");
Route::get('/test', "\App\Http\Controllers\Front\Home@test");

//Route::get('/', "\App\Http\Controllers\Front\HomeRefactoringWorkingFile@index");
//Route::post('/', "\App\Http\Controllers\Front\HomeRefactoringWorkingFile@filter");

//Route::post('/bookings', "\App\Http\Controllers\Front\BookingController@filter");
//Route::get('/bookings', "\App\Http\Controllers\Front\BookingController@filter");
//Route::get('/bookings', "\App\Http\Controllers\Front\BookingController@index");
