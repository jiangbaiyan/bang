<?php

namespace App\Http\Controllers\RegisterAndLogin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use iscms\Alisms\SendsmsPusher as Sms;

class RegisterController extends Controller
{
    public function __construct(Sms $sms)
    {
        $this->sms=$sms;
    }

    //获取短信验证码
    public function getCode(Request $request){
        $phone = $request->header('phone');
        if (!$phone){
            return Response::json(['status' => 400,'msg' => 'need phone']);
        }
        if (strlen($phone)>11){
            return Response::json(['status' => 402,'msg' => 'wrong phone format']);
        }
        $phoneFromDataBase = User::where('phone',$phone)->first();
        if ($phoneFromDataBase){
            return Response::json(['status' => 404,'msg' => 'phone has existed']);
        }
        $num = rand(100000,999999);
        $smsParams = [
            'code'    => "$num"
        ];
        $name = '帮帮吧';
        $content = $content = json_encode($smsParams);
        $code = 'SMS_57925111';
        $result = $this->sms->send($phone,$name,$content,$code);
        if(property_exists($result,'result')){
            Cache::put($phone,$num,1);//验证码60秒过期
            return Response::json(['status' => 200,'msg' => 'send sms successfully']);
        }
        else{
            return Response::json(['status' => 402,'msg' => 'send sms failed']);
        }
    }

    //验证逻辑
    public function verify(Request $request){
        $phone = $request->header('phone');
        $userCode = $request->header('code');
        if (!$phone){
            return Response::json(['status' => 400,'msg' => 'need phone']);
        }
        $code = Cache::get($phone);
        if (!$code){
            return Response::json(['status' => 402,'msg' => 'the code is overdue']);
        }
        if (strcmp($userCode,$code)){
            return Response::json(['status' => 404,'msg' => 'wrong code']);
        }
        return Response::json(['status' => 200,'msg' => 'phone verified successfully']);
    }

    //最终注册按钮逻辑
    public function register(Request $request){
        $phone = $request->input('phone');
        $password = $request->input('password');
        $name = $request->input('name');
        $id_number = $request->input('id_number');
        if (!$phone||!$password||!$id_number||!$name){
            return Response::json(['status' => 400,'msg' => 'need phone or password or name or id_number']);
        }
        if (User::create(['phone' => $phone,'password' => Hash::make($password),'name' => $name,'id_number' => $id_number])){
            return Response::json(['status' => 200,'msg' => 'user created successfully']);
        }
        else{
            return Response::json(['status' => 402,'msg' => 'user created failed']);
        }
    }
}
