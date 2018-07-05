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
            'app_id' => '2017090608582098',
            'notify_url' => ConstHelper::HOST . 'pay/aliNotify',
            //'return_url' => 'http://yansongda.cn/return.php',
            'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA28Z6LQILxsm7xx9gxb5l7UvzwsSpIn7rzQwTKnnH8VNIObxPbEmzGL32vhfcrrWY+7qdaLlubGWwsCt3AoIC+ZfznDrsy0Sswsi9qXv/9ShQHhS0aUKem8eESyl3bpeeyZUP8L35vuH6Pqsvp/5aaA0wLQVKV4zv7hgi6p1D7PtB48o9F4dI4hUfDa/fFdRjVjaP5eUnoB0Vw0LnNj/8HAXtPTUD9TxkWXO2sLRhJQXhvqHrIR4tW/5zMkIL8/qKlovjpR8NRip5KD4tq9GWVa6oVlEbVCSvuNXKJJUhYT3+mfRZKTJbQFjDAYHxWh4k2kXAjSFDXNX4dSSmdIIzKQIDAQAB',
            'private_key' => '7LEVJv7IIoX85VgZXjEqUg==4',
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