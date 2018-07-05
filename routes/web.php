<?php

Route::group(['prefix' => 'v1'],function (){

     //登录注册模块
     Route::group(['prefix' => 'common'],function (){

         //获取短信验证码
         Route::post('getCode','Common\RegisterController@getCode');

         //注册
         Route::post('register','Common\RegisterController@registerAndVerify');

         //添加身份证号信息
         Route::post('addWxInfo','Common\RegisterController@addWxInfo');

         //手机号密码登录
         Route::post('loginByPassword','Common\LoginController@loginByPassword');

         //手机验证码登录
         Route::post('loginByCode','Common\LoginController@loginByCode');
     });

     Route::group(['middleware' => 'auth:api'],function (){

         Route::group(['prefix' => 'user'],function (){

             //获取个人信息
             Route::get('getUserInfo','Common\UserController@getUserInfo');

             //更新个人信息
             Route::post('modifyUserInfo','Common\UserController@modifyUserInfo');

         });


         //求支援模块
         Route::group(['prefix' => 'askForHelp'],function (){

             //发布求支援订单
             Route::post('releaseOrder','AskForHelp\AskForHelpController@releaseOrder');

             //删除订单
             Route::post('cancelOrder','AskForHelp\AskForHelpController@cancelOrder');

         });

         //我来帮模块
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

         //查看订单模块
         Route::group(['prefix' => 'order'],function (){

             //获取自己发布的订单
             Route::get('getSentOrder','Order\OrderController@getSentOrder');

             //获取自己接到的订单
             Route::get('getReceivedOrder','Order\OrderController@getReceivedOrder');

             //查看订单详情
             Route::get('getOrderDetail','Order\OrderController@getOrderDetail');

             //评价订单
             Route::post('commentOrder','Order\OrderController@commentOrder');

         });

         Route::group(['prefix' => 'pay'],function (){

             //统一下单
             Route::get('unifyPay','Pay\WxPayController@unifyPay');

             //微信支付结果通知
             Route::get('wxNotify','Pay\WxPayController@wxNotify');

             //支付结果通知
             Route::get('aliNotify','Pay\AliPayController@aliNotify');

             //转账给接单者
             Route::get('wxTransfer','Pay\WxPayController@wxTransfer');

             //转账给接单者
             Route::get('aliTransfer','Pay\AliPayController@aliTransfer');
         });
     });
});
