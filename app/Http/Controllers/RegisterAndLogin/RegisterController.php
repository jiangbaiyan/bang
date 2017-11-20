<?php

namespace App\Http\Controllers\RegisterAndLogin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use iscms\Alisms\SendsmsPusher as Sms;

class RegisterController extends Controller
{
    public function __construct(Sms $sms)
    {
        $this->sms=$sms;
    }

    //获取短信验证码
    public function getCode($phone){
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
        $code = 'SMS_73870009';
        $result = $this->sms->send($phone,$name,$content,$code);
        if(property_exists($result,'result')){
            Cache::put($phone,$num,1);//验证码60秒过期
            return Response::json(['status' => 200,'msg' => 'send sms successfully']);
        }
        else{
            return Response::json(['status' => 402,'msg' => 'send sms failed']);
        }
    }


    //验证短信验证码的正确性并注册
    public function register(Request $request){
        $phone = $request->input('phone');
        $password = $request->input('password');
/*        $userCode = $request->input('code');
        if (!$phone||!$password||!$userCode){
            return Response::json(['status' => 400,'msg' => 'need phone or password or code']);
        }
        $code = Cache::get($phone);
        if ($code==null){
            return Response::json(['status' => 402,'msg' => 'the code is overdue']);
        }
        if (strcmp($userCode,$code)){
            return Response::json(['status' => 404,'msg' => 'wrong code']);
        }*/
        User::create(['phone' => $phone,'password' => Hash::make($password)]);
        return Response::json(['status' => 200,'msg' => 'user created successfully']);

    }

    public function saveName(Request $request){
        $phone = $request->input('phone');
        $name = $request->input('name');
        if (!$phone||!$name){
            return Response::json(['status' => 400,'msg' => 'need phone or name']);
        }
        $user = User::where('phone','=',$phone)->first();
        $user->name = $name;
        $user->save();
        return Response::json(['status' => 200,'msg' => 'name saved successfully']);
    }

    public function qqRegister(Request $request){
        $phone = $request->input('phone');
        $openid = $request->input('openid');
        if (!$phone||!$openid){
            return Response::json(['status' => 400,'msg' => 'need phone or openid']);
        }
        $user = User::where('openid','=',$openid)->first();
        if (!$user){
            return Response::json(['status' => 200,'msg' => 'user not exists']);
        }
        $user->phone = $phone;
        $user->save();
        $token = Hash::make($phone.date(DATE_W3C));
        Redis::set($phone,$token);
        Redis::expire($phone,100000);
        return Response::json(['status' => 200,'msg' => 'qqRegister successfully','data' => $user,'token' => $token]);
    }
}
