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

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['namespace' => 'API'], function(){
        Route::post('/login', 'UserController@login')->name('login.api');
        Route::post('/register', 'UserController@register')->name('register.api');

        // private routes
        Route::middleware('auth:api')->group(function () {
            Route::get('/logout', 'UserController@logout')->name('logout');
            Route::apiResource('transaction', 'TransactionController');
        });
    });

});
