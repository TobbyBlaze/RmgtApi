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
//     return view('welcome');
// });

Route::view('/{path?}', 'app')->middleware('cors');

// Route::view('/', 'apphome')->middleware('cors');

// Auth::routes();
// Route::view('/login', 'signin')->middleware('cors');
// Route::get('/login', 'Api\AuthController@login')->name('login');
Route::group([], function (){ 
Route::group(['middleware' => ['guest:api']], function () {
    // Route::view('/login/{path?}', 'login')->middleware('cors')->name('login');
    // Route::view('/login', 'login')->middleware('cors')->name('login');
    // Route::get('/login', 'Api\AuthController@login')->name('login');
    // Route::view('/signup/{path?}', 'signup')->middleware('cors');
    // Route::view('/', 'apphome')->middleware('cors');
    // Route::view('/{path?}', 'app')->middleware('cors');
});

Route::group(['middleware' => 'auth:api'], function() {
    // Route::view('/{path?}', 'app')->middleware('cors');
    // Route::view('/prdetails/{path?}', 'prdetails')->middleware('cors');
    // Route::view('/shcart/{path?}', 'shcart')->middleware('cors');
    // Route::view('/checkout/{path?}', 'checkout')->middleware('cors');
});
});

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
