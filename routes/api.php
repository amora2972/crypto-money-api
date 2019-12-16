<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['json.response']], function () {

    Route::group(['namespace' => 'API'], function(){
        Route::post('/login', 'UserController@login')->name('login.api');
        Route::post('/register', 'UserController@register')->name('register.api');

        Route::apiResource('currency', 'CurrencyController');

        // private routes
        Route::middleware('auth:api')->group(function () {
            Route::get('/user', 'UserController@getCurrentUser');
            Route::get('/logout', 'UserController@logout')->name('logout');
            Route::apiResource('transaction', 'TransactionController');
        });
    });

});
