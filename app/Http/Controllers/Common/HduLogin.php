<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 13:51
 */

namespace App\Http\Controllers\Common;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Service\SmsService;
use App\Service\WxService;
use App\UserModel;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\Exceptions\OperateFailedException;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;

class HduLogin extends Controller {


    const LOGIN_SERVER = 'http://cas.hdu.edu.cn/cas/login';

    const VALIDATE_SERVER = 'http://cas.hdu.edu.cn/cas/serviceValidate';

    const THIS_URL = ConstHelper::HOST . '/common/casLogin';

    const REDIS_TOKEN_PREFIX = 'bang_token_%s';


    /**
     * 获取验证码
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     */
    public function getCode(){
        $validator = Validator::make($req = Request::all(),['phone' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        SmsService::getCode($phone);
        return ApiResponse::responseSuccess();
    }


    /**
     * 验证码验证+登录
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function login(){
        $validator = Validator::make($req = Request::all(),[
            'phone' => 'required',
            'code' => 'required',
            'wxCode' => 'required',
            'avatar' => 'required',
            'nickname' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $frontCode = $req['code'];
        //SmsService::verifyCode($phone,$frontCode);
        $openid = WxService::getOpenid($req['wxCode']);
        $data = array(
            'phone' => $phone,
            'name'  => $req['nickname'],
            'avatar' => $req['avatar'],
            'openid' => $openid
        );
        $user = $this->getLatestUser($data);
        $token = $this->setToken($user);
        return ApiResponse::responseSuccess(['token' => $token]);
    }

    /**
     * 设置token
     * @param $data
     * @return mixed
     */
    private function setToken($data){
        $token = JWT::encode($data,ConstHelper::JWT_KEY);
        $redisKey = sprintf(self::REDIS_TOKEN_PREFIX,$data['phone']);
        Redis::set($redisKey,$token);
        Redis::expire($redisKey,2678400);
        return $token;
    }

    /**
     * 不存在则创建，存在则更新，返回最新的用户模型
     * @param $data
     * @return mixed
     */
    private function getLatestUser($data){
        $user = UserModel::where('phone',$data['phone'])->first();
        if (!$user){
            UserModel::create($data);
        } else{
            $user->update($data);
            $user = UserModel::where('phone',$data['phone'])->first();
        }
        return $user;
    }

    /**
     * 获取用户信息
     * @return string
     */
    public function getUserInfo(){
        $userId = Request::all()['user']->id;
        $user = UserModel::find($userId);
        return ApiResponse::responseSuccess($user);
    }

}

