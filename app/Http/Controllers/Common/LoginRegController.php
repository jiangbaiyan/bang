<?php
/**
 * Created by PhpStorm.
 * UserModel: Baiyan
 * Date: 2018/5/23
 * Time: 19:56
 */

namespace App\Http\Controllers\Common;

use app\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Service\SmsService;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class LoginRegController extends Controller{


    /**
     * 获取验证码
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     */
    public function getCode(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['phone' => 'required|unique:users']);
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
        return ApiResponse::responseSuccess();
    }


    /**
     * 使用手机号和密码登录
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function loginByPassword(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'phone' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $password = $req['password'];
        $userModel = new UserModel();
        $user = $userModel->where('phone',$phone)->first();
        if (!$user){
            throw new ResourceNotFoundException(ConstHelper::USER);
        }
        if (!$token = \Auth::attempt(['phone' => $phone,'password' => $password])){
            throw new OperateFailedException(ConstHelper::WRONG_PASSWORD);
        }
        return ApiResponse::responseSuccess(['jwtToken' => $token]);
    }

    public function loginByCode(){

    }
}