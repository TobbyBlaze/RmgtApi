<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\User;
use App\Admin;

use Illuminate\Support\Facades\Input;

use Stevebauman\Location\Facades\Location;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('trades', 'TradeController@index');

Route::group([ 'prefix' => 'auth'], function (){
    Route::group(['middleware' => ['guest:api']], function () {
        Route::post('login', 'API\AuthController@login');
        Route::post('signup', 'API\AuthController@signup');

        Route::post('seller-login', 'API\AuthController@seller_login');
        Route::post('seller-signup', 'API\AuthController@seller_signup');

        Route::post('admin-login', 'API\AuthController@admin_login');
        Route::post('admin-signup', 'API\AuthController@admin_signup');

        // Route::get('/', 'JournalController@index');

    });
    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('logout', 'API\AuthController@logout');

        Route::get('getuser', 'API\AuthController@getUser');
        Route::get('profile', 'UserController@index');
        Route::post('updateUser', 'UserController@updateUser');
        Route::post('updateUserPassword', 'UserController@updateUserPassword');

        //Journals
        Route::get('/', 'JournalController@index');
        Route::get('journals', 'JournalController@index');
        Route::get('journals/{id}', 'JournalController@show');
        Route::resource('show', 'JournalController');
        Route::post('storejournal', 'JournalController@store');
        Route::any('updatejournal/{id}', 'JournalController@update');
        Route::any('deletejournal/{id}', 'JournalController@destroy');



        //Reset password
        // Route::group(['prefix' => 'password'], function () {
        //     Route::post('create', 'PasswordResetController@create');
        //     Route::get('find/{token}', 'PasswordResetController@find');
        //     Route::post('reset', 'PasswordResetController@reset');
        // });

        //Checkout
        Route::post('/charge', 'CheckoutController@charge');

    });

    //Admin
    Route::group(['middleware' => ['auth:admin-api', 'auth.admin']], function() {
        Route::get('a-logout', 'API\AuthController@admin_logout');
        Route::get('getadmin', 'API\AuthController@getAdmin');

        //Trades
        Route::post('storetrade', 'TradeController@store');
        Route::any('updatetrade/{id}', 'TradeController@update');
        Route::any('deletetrade/{id}', 'TradeController@destroy');
    });

});

//User Reset password
Route::group([
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

//Admin Reset password
Route::group([
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('adminCreate', 'AdminPasswordResetController@create');
    Route::get('adminFind/{token}', 'AdminPasswordResetController@find');
    Route::post('adminReset', 'AdminPasswordResetController@reset');
});

// Account activation
Route::group([
    'prefix' => 'auth'
], function () {
    // Route::post('login', 'AuthController@login');
    // Route::post('signup', 'AuthController@signup');
    Route::get('signup/activate/{token}', 'API\AuthController@signupActivate');
    Route::get('sellerSignup/activate/{token}', 'API\AuthController@sellerSignupActivate');
    Route::get('adminSignup/activate/{token}', 'API\AuthController@adminSignupActivate');
    Route::get('courierSignup/activate/{token}', 'API\AuthController@courierSignupActivate');

    // Route::group([
    //   'middleware' => 'auth:api'
    // ], function() {
    //     Route::get('logout', 'AuthController@logout');
    //     Route::get('user', 'AuthController@user');
    // });
});

//Search
Route::any ( 'searchGoods', 'FindController@goods');
Route::any ( 'searchAds', 'FindController@ads');
Route::any ( 'searchSellers', 'FindController@sellers');

//Checkout test
Route::post('/charge', 'CheckoutController@charge');


Route::get('location', function () {

    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = request()->ip();

    // $ip = '50.90.0.1';
    // $ip = \Request::ip();
    // $ip = request()->ip();
    $data = \Location::get($ipaddress);
    // dd($data);
    return response()->json($data);

});
