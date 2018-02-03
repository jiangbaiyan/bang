<?php
//好友系统
Route::post('create','IMController@create');
Route::put('update','IMController@update');

//登录注册模块
Route::group(['prefix' => 'registerAndLogin','namespace' => 'RegisterAndLogin'],function (){
    Route::get('code','RegisterController@getCode');
    Route::post('register','RegisterController@register');
    Route::post('name','RegisterController@saveName');
    Route::post('login','LoginController@login');
    Route::post('qqRegister','RegisterController@qqRegister');
    Route::post('qqLogin','LoginController@qqLogin');
});

Route::group(['middleware' => 'CheckLogin'],function (){
    //订单模块（与用户相关）
    Route::group(['prefix' => 'order','namespace' => 'Order'],function (){
        Route::get('order','IndexController@getOrders');
        Route::get('detail','DetailController@getDetail');
        Route::put('finish','IndexController@finishService');
        Route::put('comment','IndexController@comment');
        Route::delete('order','DetailController@cancelOrder');
    });

    //求支援模块
    Route::group(['prefix' => 'askForHelp','namespace' => 'AskForHelp'],function(){
        Route::post('order','AskForHelpController@createOrder');
    });

    //我来帮模块
    Route::group(['prefix' => 'helpOthers','namespace' => 'HelpOthers'],function(){
        Route::get('order','IndexController@getOrder');
        Route::get('detail','DetailController@getDetail');
        Route::put('receive','DetailController@receiveOrder');
    });
});