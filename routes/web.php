<?php

Route::group(['prefix' => 'v1'],function (){
     Route::group(['prefix' => 'common'],function (){

         //获取短信验证码
         Route::post('getCode','Common\RegisterController@getCode');

         //注册
         Route::post('register','Common\RegisterController@registerAndVerify');

         //添加身份证号信息
         Route::post('addIdInfo','Common\RegisterController@addIdInfo');

         //手机号密码登录
         Route::post('loginByPassword','Common\LoginController@loginByPassword');

         //手机验证码登录
         Route::post('loginByCode','Common\LoginController@loginByCode');
     });

     Route::group(['middleware' => 'auth:api'],function (){
         Route::group(['prefix' => 'askForHelp'],function (){

             //发布求支援订单
             Route::post('releaseOrder','AskForHelp\AskForHelpController@releaseOrder');

         });

         Route::group(['prefix' => 'helpOthers'],function (){

             //获取所有发布的等待服务的订单（可传递参数选择类型）
             Route::get('getReleasedOrderList','HelpOthers\HelpOthersController@getReleasedOrdersList');

             //获取发布的订单详情
             Route::get('getReleasedOrderDetail','HelpOthers\HelpOthersController@getReleasedOrderDetail');

             //接单
             Route::post('receiveOrder','HelpOthers\HelpOthersController@receiveOrder');

             //完成订单
             Route::post('finishOrder','HelpOthers\HelpOthersController@finishOrder');

         });
     });
});
