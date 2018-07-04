<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4/004
 * Time: 23:10
 */

namespace App\Service;

use App\Helper\ConstHelper;
use Yansongda\Pay\Pay;

class AliService{

    private static $config = [
            'app_id' => '',
            'notify_url' => ConstHelper::HOST . 'pay/aliNotify',
            //'return_url' => 'http://yansongda.cn/return.php',
            'ali_public_key' => '',
            'private_key' => '',
            'log' => [ // optional
                'file' => '.storage/logs/alipay.log',
                'level' => 'debug'
            ],
    ];


    /**
     * 返回支付宝操作实例
     * @return \Yansongda\Pay\Gateways\Alipay
     */
    public static function getAliPayApp(){
        return Pay::alipay(self::$config);
    }

}