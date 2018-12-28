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
use src\Logger\Logger;
use Yansongda\Pay\Pay;

class WxService{

    private static $appId = 'wx7e2caeca5c50a086';

    private static $appKey = 'c1f7a5af2f140e9811a1290c185f8de0';

    private static $payConfig = [
            'miniapp_id' => 'wx7e2caeca5c50a086', // appId
            'mch_id' => '1508225431',//商户id
            'key' => 'c1f7a5af2f140e9811a1290c185faff8',//支付的key
            'notify_url' => \App\Helper\ConstHelper::HOST . 'pay/wechatNotify',
            'cert_client' => __DIR__ . '/apiclient_cert.pem', // optional, 退款，红包等情况时需要用到
            'cert_key' => __DIR__ . '/apiclient_key.pem',// optional, 退款，红包等情况时需要用到
            'type' => 'miniapp'
    ];

    /**
     * 获取微信支付实例
     * @return \Yansongda\Pay\Gateways\Wechat
     */
    public static function getWxPayApp(){
        return Pay::wechat(self::$payConfig);
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
        $res = ApiRequest::sendRequestNew('GET',$url);
        $res = json_decode($res,true);
        if (array_key_exists('errmsg',$res)){
            Logger::notice('wx|get_openid_from_api_failed|msg:' . json_encode($res));
            throw new OperateFailedException('获取微信授权失败');
        }
        return $res['openid'];
    }
}