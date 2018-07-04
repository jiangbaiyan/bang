<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018-6-21
 * Time: 8:20
 */

namespace App\Service;

use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;
use Yansongda\Pay\Pay;

class WxService{

    private static $appId = 'wx7e2caeca5c50a086';

    private static $appKey = 'c1f7a5af2f140e9811a1290c185f8de0';

    public static $payConfig = [
            'miniapp_id' => 'wx7e2caeca5c50a086', // appId
            'mch_id' => '1508225431',//商户id
            'key' => 'c1f7a5af2f140e9811a1290c185faff8',//支付的key
            'notify_url' => \App\Helper\ConstHelper::HOST . 'pay/notify',
            'cert_client' => __DIR__ . '/apiclient_cert.pem', // optional, 退款，红包等情况时需要用到
            'cert_key' => __DIR__ . '/apiclient_key.pem',// optional, 退款，红包等情况时需要用到
            'log' => [ // optional
                'file' => './storage/logs/wechat.log',
                'level' => 'debug'
            ],
            'type' => 'miniapp'
    ];

    /**
     * 统一下单
     * @param array $params
     * @return \Yansongda\Pay\Gateways\Wechat\MiniappGateway
     */
    public static function unifyPay(array $params){
        return Pay::wechat(self::$payConfig)->miniapp($params);
    }

    /**
     *
     */
    public static function transfer(array $params){
        return Pay::wechat(self::$payConfig)->transfer($params);
    }
    /**
     * 返回openid
     * @param $code
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getOpenid($code){
        $appId = self::$appId;
        $appKey = self::$appKey;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appKey&js_code=$code&grant_type=authorization_code";
        $res = ApiRequest::sendRequest('GET',$url);
        if (array_key_exists('errmsg',$res)){
            throw new OperateFailedException($res['errmsg']);
        }
        return $res['openid'];
    }

}