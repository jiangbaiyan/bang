<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/22/022
 * Time: 23:53
 */

return [

    'appid' => 'wx7e2caeca5c50a086',

    'appKey' => 'c1f7a5af2f140e9811a1290c185f8de0',

    'pay' => [
        'miniapp_id' => 'wx7e2caeca5c50a086', // appId
        'mch_id' => '1508225431',//商户id
        'key' => 'hdu_qlc_2018_01bdf25*9',//支付的key
        'notify_url' => \App\Helper\ConstHelper::HOST . 'pay/notify',
        'cert_client' => './storage/cert/apiclient_cert.pem', // optional, 退款，红包等情况时需要用到
        'cert_key' => './storage/cert/apiclient_key.pem',// optional, 退款，红包等情况时需要用到
        'log' => [ // optional
            'file' => './storage/logs/wechat.log',
            'level' => 'debug'
        ],
        'type' => 'miniapp'
    ]
];
