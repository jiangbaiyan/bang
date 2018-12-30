<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018-6-21
 * Time: 8:20
 */

namespace App\Service;

use Illuminate\Support\Facades\Redis;
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

    private static $model =  [
        'touser' => '',
        'template_id' => 'zGYFT0ZK4MV02Ql0OdUF0ZXKjaEqmmBsOOok4UJte6g',
        'form_id' => '',
        'data' => array(
            'keyword1' => array(
                'value' => ''
            ),
            'keyword2' => array(
                'value' => ''
            ),
            'keyword3' => array(
                'value' => ''
            ),
            'keyword4' => array(
                'value' => ''
            ),
            'keyword5' => array(
                'value' => ''
            ),
        )
    ];

    const REDIS_OPENID_KEY = 'bang_openid';

    const REDIS_ACCESS_TOKEN_KEY = 'bang_access_token';


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

    /**
     * 获取access_token
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getAccessToken(){
        $appId = self::$appId;
        $appKey = self::$appKey;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appKey";
        $res = ApiRequest::sendRequestNew('GET', $url);
        $res = json_decode($res, true);
        if (isset($res['errmsg'])){
            Logger::fatal('wx|get_access_token_failed|msg:' . json_encode($res));
            throw new OperateFailedException('获取access_token失败');
        }
        $accessToken = $res['access_token'];
        return $accessToken;
    }

    /**
     * 发送模板消息
     * @param $openid
     * @param array $params
     * @return bool
     * @throws OperateFailedException
     */
    public static function sendModelInfo($openid, $params = array()){
        $accessToken = self::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$accessToken";
        $config = self::$model;
        $config['touser'] = $openid;
        $config['form_id'] = $params['form_id'];
        $config['data']['keyword1']['value'] = $params['uuid'];
        $config['data']['keyword2']['value'] = $params['created_at'];
        $config['data']['keyword3']['value'] = $params['type'];
        $config['data']['keyword4']['value'] = $params['title'];
        $config['data']['keyword5']['value'] = $params['price'] . '元';
        $res = ApiRequest::sendRequestNew('POST', $url, [
            'json' => json_encode($config)
        ]);
        $res = json_decode($res, true);
        if ($res['errcode']){
            Logger::fatal('wx|send_model_info_failed|msg:' . json_encode($res));
            throw new OperateFailedException('模板消息发送失败');
        }
        return true;
    }
}