<?php

Route::group(['prefix' => 'v1'],function (){
     Route::group(['prefix' => 'common'],function (){

         //获取短信验证码
         Route::post('getCode','Common\LoginController@getCode');
     });
});
