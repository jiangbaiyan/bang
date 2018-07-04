<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018-6-21
 * Time: 8:20
 */

namespace App\Service;

use EasyWeChat\Factory;
use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;

class WxService{

    private static $payConfig = [
        'sandbox'            => false,
        'app_id'             => 'wx7e2caeca5c50a086',
        'mch_id'             => '1508225431',
        'key'                => 'c1f7a5af2f140e9811a1290c185faff8',   // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'          => '/home/wwwroot/Bang/storage/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => '/home/wwwroot/Bang/storage/storage/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！

        'notify_url'         => \App\Helper\ConstHelper::HOST . 'pay/notify',     // 你也可以在下单时单独设置来想覆盖它
    ];

    /**
     * 返回EasyWechat实例
     * @return \EasyWeChat\Payment\Application
     */
    public static function getEasyApp(){
        return Factory::payment(self::$payConfig);
    }

    /**
     * 返回openid
     * @param $code
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getOpenid($code){
        $appId = config('wx.appid');
        $appKey = config('wx.appKey');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appKey&js_code=$code&grant_type=authorization_code";
        $res = ApiRequest::sendRequest('GET',$url);
        if (array_key_exists('errmsg',$res)){
            throw new OperateFailedException($res['errmsg']);
        }
        return $res['openid'];
    }

}