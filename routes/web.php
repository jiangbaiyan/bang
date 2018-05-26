<?php

Route::group(['prefix' => 'v1'],function (){
     Route::group(['prefix' => 'common'],function (){

         //获取短信验证码
         Route::post('getCode','Common\RegisterController@getCode');

         //注册
         Route::post('register','Common\RegisterController@registerAndVerify');

         //手机号密码登录
         Route::post('loginByPassword','Common\LoginController@loginByPassword');

         //手机验证码登录
         Route::post('loginByCode','Common\LoginController@loginByCode');
     });
});
