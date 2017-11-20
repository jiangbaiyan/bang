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
Route::post('create','IMController@create');
Route::put('update','IMController@update');
//登录注册模块
Route::group(['prefix' => 'registerAndLogin','namespace' => 'RegisterAndLogin'],function (){
    Route::get('code/{phone}','RegisterController@getCode');
    Route::post('register','RegisterController@register');
    Route::post('name','RegisterController@saveName');
    Route::post('qqRegister','RegisterController@qqRegister');
    Route::post('login','LoginController@login');
    Route::post('qqLogin','LoginController@qqLogin');
});

Route::group(['middleware' => 'CheckLogin'],function (){
    Route::group(['prefix' => 'order','namespace' => 'Order'],function (){
        Route::get('getOrders','IndexController@getOrders');
        Route::get('finishService','IndexController@finishService');
        Route::post('comment','IndexController@comment');
        Route::get('showDetail','DetailController@showDetail');
        Route::delete('cancelOrder','DetailController@cancelOrder');
    });

    Route::group(['prefix' => 'askForHelp','namespace' => 'AskForHelp'],function(){
        Route::post('createOrder','AskForHelpController@createOrder');
    });

    Route::group(['prefix' => 'helpOthers','namespace' => 'HelpOthers'],function(){
        Route::get('getAllOrders','IndexController@getAllOrders');
        Route::get('getRunOrders','IndexController@getRunOrders');
        Route::get('getAskOrders','IndexController@getAskOrders');
        Route::get('getLearnOrders','IndexController@getLearnOrders');
        Route::get('getTechniqueOrders','IndexController@getTechniqueOrders');
        Route::get('getLifeOrders','IndexController@getLifeOrders');
        Route::get('getOtherOrders','IndexController@getOtherOrders');
        Route::get('showDetail','DetailController@getDetail');
        Route::post('report','ReportController@submitReport');
        Route::post('receiveOrder','DetailController@receiveOrder');
    });
});