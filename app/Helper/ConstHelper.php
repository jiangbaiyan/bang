<?php
/**
 * Created by PhpStorm.
 * UserModel: Baiyan
 * Date: 2018/5/23
 * Time: 22:34
 */

namespace App\Helper;

class ConstHelper{

    const
        USER = '用户不存在',
        WRONG_PASSWORD = '密码错误',
        WRONG_CODE = '验证码错误',
        SMS_ERROR = '短信官方接口异常,请稍后重试',
        CODE = '后台短信验证码不存在';
}