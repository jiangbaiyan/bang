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
Route::group(['prefix' => 'registerAndLogin','namespace' => 'RegisterAndLogin'],function (){
    Route::get('getCode','RegisterController@getCode');
    Route::get('verify','RegisterController@verify');
    Route::post('register','RegisterController@register');
    Route::get('login','LoginController@login');
});

Route::group(['prefix' => 'order','namespace' => 'Order'],function (){
    //Route::group(['middleware' => 'CheckLogin'],function (){
        Route::get('getOrders','IndexController@getOrders');
        Route::get('finishService','IndexController@finishService');
        Route::post('comment','IndexController@comment');
        Route::get('showDetail','DetailController@showDetail');
        Route::delete('cancelOrder','DetailController@cancelOrder');
    //});
});