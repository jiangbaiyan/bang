<?php
/**
 * Created by PhpStorm.
 * UserModel: Baiyan
 * Date: 2018/5/23
 * Time: 19:56
 */

namespace App\Http\Controllers\Common;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Service\SmsService;
use App\Service\WxService;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class RegisterController extends Controller{


    /**
     * 获取验证码
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     */
    public function getCode(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['phone' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        SmsService::getCode($phone);
        return ApiResponse::responseSuccess();
    }

    /**
     * 验证短信验证码并注册
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function registerAndVerify(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'phone' => 'required|unique:users',
            'password' => 'required',
            'code' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $frontCode = $req['code'];
        $password = $req['password'];
        SmsService::verifyCode($phone,$frontCode);
        $user = UserModel::create([
            'phone' => $phone,
            'password' => \Hash::make($password),
        ]);
        if (!$user){
            throw new OperateFailedException();
        }
        Cache::put('user'.$phone,$user);
        return ApiResponse::responseSuccess();
    }


    /**
     * 用户输入姓名和身份证号信息
     * @param Request $request
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function addWxInfo(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'phone' => 'required',
            'wxCode' => 'required',
            'nickName' => 'required',
            'avatarUrl' => 'required',
            'gender' => 'required',
            'city' => 'required',
            'province' => 'required',
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        if ($req['gender'] == 1){
            $sex = ConstHelper::MALE;
        } else if ($req['gender'] == 2){
            $sex = ConstHelper::FEMALE;
        } else{
            $sex = ConstHelper::UNKNOWN;
        }
        $openid = WxService::getOpenid($req['wxCode']);
        $user = Cache::get('user'.$req['phone']);
        if (!$user){
            throw new ResourceNotFoundException(ConstHelper::USER);
        }
        $user->openid = $openid;
        $user->name = $req['nickName'];
        $user->avatar = $req['avatarUrl'];
        $user->sex = $sex;
        $user->province = $req['province'];
        $user->city = $req['city'];
        $user->save();
        return ApiResponse::responseSuccess();
    }
}