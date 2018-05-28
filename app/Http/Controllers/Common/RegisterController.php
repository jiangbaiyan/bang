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
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Ofcold\IdentityCard\IdentityCard;
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
        SmsService::verifyCode($frontCode);
        $user = UserModel::create([
            'phone' => $phone,
            'password' => \Hash::make($password),
        ]);
        if (!$user){
            throw new OperateFailedException();
        }
        Session::put('user',$user);
        return ApiResponse::responseSuccess();
    }


    /**
     * 用户输入姓名和身份证号信息
     * @param Request $request
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function addIdInfo(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'name' => 'required',
            'idCard' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $user = Session::get('user');
        if (!isset($user)){
            throw new ResourceNotFoundException(ConstHelper::USER);
        }
        $res = IdentityCard::make($req['idCard']);
        if ($res === false){
            throw new OperateFailedException(ConstHelper::WRONG_ID_CARD);
        }
        $province = $res->getProvince();
        $city = $res->getCity();
        $sex = $res->getGender();
        $age = $res->getAge();
        $user->name = $req['name'];
        $user->province = $province;
        $user->city = $city;
        $user->age = $age;
        $user->sex = $sex;
        $user->save();
        return ApiResponse::responseSuccess();
    }
}