<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/5/26
 * Time: 8:56
 */

namespace app\Http\Controllers\Common;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Service\SmsService;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class LoginController extends Controller{

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


    /**
     * 验证码登录
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function loginByCode(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'phone' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $code = $req['code'];
        SmsService::verifyCode($code);
        $userModel = new UserModel();
        $user = $userModel->where('phone',$phone)->first();
        if (!$user){
            throw new ResourceNotFoundException(ConstHelper::USER);
        }
        $token = \JWTAuth::fromUser($user);
        if (!$token){
            throw new OperateFailedException();
        }
        return ApiResponse::responseSuccess(['jwtToken' => $token]);
    }
}
