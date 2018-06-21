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

class WxService{

    private static $appId = 'wx7e2caeca5c50a086';
    private static $appKey = 'c1f7a5af2f140e9811a1290c185f8de0';


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