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

        HOST = 'https://bang.cloudshm.com/api/v1',

        MALE = '男',
        FEMALE = '女',
        UNKNOWN = '未知',

        USER = '用户不存在',
        WRONG_PASSWORD = '密码错误',
        WRONG_CODE = '验证码错误',
        SMS_ERROR = '短信官方接口异常,请稍后重试',
        CODE = '后台短信验证码不存在',
        WRONG_ID_CARD = '身份证号码不正确',

        ORDER = '订单不存在',
        WRONG_ORDER_STATUS = '错误的订单状态',
        WRONG_RECEIVER = '您不能接自己的单',
        WRONG_FINISHER = '您不能完成其他人的订单';
}