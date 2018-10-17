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

Route::group([
    'middleware' => 'cors',
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::group([
    'middleware' => 'cors',
    'middleware' => 'auth:api'
], function ($router) {
    Route::apiResource('/beers', 'BeerController');
});

Route::group([
    'middleware' => 'cors',
    'middleware' => 'auth:api'
], function ($router) {
    Route::apiResource('/customers', 'CustomerController');
    Route::get('/orders', 'OrderController@index');
    Route::get('/orders/{customerId}', 'OrderController@getOrdersByCustomer');
    Route::get('/ordersPrintable', 'OrderController@getPrintableOrders')->name('ordersPrintable');
    Route::get('/getMostOrderedBeers/{numOfBeers}', 'OrderController@getMostOrderedBeers');
    Route::post('/orders/addOrder', 'OrderController@addOrder');
    Route::delete('/orders/{id}', 'OrderController@destroy');
});