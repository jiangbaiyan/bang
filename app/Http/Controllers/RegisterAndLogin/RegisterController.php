<?php

namespace App\Http\Controllers\RegisterAndLogin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class RegisterController extends Controller
{
    //获取短信验证码
    public function getCode(Request $request){
        $phone = $request->header('phone');
        if (!isset($phone)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        if (!preg_match('^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$',$phone)){
            return Response::json(['status' => 403,'msg' => 'wrong phone format']);
        }
        $user = User::where('phone',$phone)->first();
        if ($user->count()){
            return Response::json(['status' => 401,'msg' => 'phone was registered']);
        }
        return Response::json(['status' => 200,'msg' => 'success']);
    }


    //验证短信验证码的正确性并注册
    public function register(Request $request){
        $phone = $request->input('phone');
        $password = $request->input('password');
        $user = User::where('phone',$phone)->first();
        if ($user->count()){
            return Response::json(['status' => 401,'msg' => 'phone was registered']);
        }
/*        $userCode = $request->input('code');
        if (!$phone||!$password||!$userCode){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $code = Cache::get($phone);
        if ($code==null){
            return Response::json(['status' => 402,'msg' => 'the code is overdue']);
        }
        if (strcmp($userCode,$code)){
            return Response::json(['status' => 404,'msg' => 'wrong code']);
        }*/
        User::create(['phone' => $phone,'password' => Hash::make($password)]);
        return Response::json(['status' => 200,'msg' => 'success']);

    }

    //用户输入昵称并保存
    public function saveName(Request $request){
        $phone = $request->input('phone');
        $name = $request->input('name');
        if (!isset($phone)||!isset($name)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $user = User::where('phone','=',$phone)->first();
        $user->name = $name;
        $user->save();
        return Response::json(['status' => 200,'msg' => 'success']);
    }

/*    public function qqRegister(Request $request){
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
    }*/
}
