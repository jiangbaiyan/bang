<?php

namespace App\Http\Controllers\RegisterAndLogin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //登录
    public function login(Request $request){
        $phone = $request->input('phone');
        $password = $request->input('password');
        if (!isset($phone)||!isset($password)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $user = User::where('phone',$phone)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        if (!Hash::check($password,$user->password)){
            return Response::json(['status' => 402,'msg' => 'wrong password']);
        }
        $token = Hash::make($phone.$password.date(DATE_W3C));
        Redis::set($user->id,$token);
        Redis::expire($user->id,604800);
        return Response::json(["status"=>200,"msg"=>"success",'data' => ['user' => $user,'token' => $token]]);
    }

    public function uploadHead(Request $request){
        $id = $request->input('id');
        if (!$request->hasFile('head')){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $user = User::find($id);
        $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
        $file = $request->file('head');
        $allowedFormat = ['png','bmp','jpg','jpeg'];
        $ext = $file->getClientOriginalExtension();//获取扩展名
        if (!in_array($ext,$allowedFormat)){//判断格式是否是允许上传的格式
            return response()->json(['status' => 402,'msg' => 'wrong file format']);
        }
        $path = \Storage::disk('upyun')->putFileAs('Bang/head',$file,"$id".'_'.time(),'public');
        $url = $url.$path;
        $user->head = $url;
        $user->save();
        return Response::json(['status' => 200,'msg' => 'success']);
    }

/*    public  function qqLogin(Request $request){
        $openid = $request->input('openid');
        $name = $request->input('name');
        if (!$openid){
            return Response::json(['status' => 400,'msg' => 'need openid']);
        }
        $user = User::where('openid','=',$openid)->first();
        if (!$user){
            User::create(['openid' => $openid,'name' => $name]);
            return Response::json(['status' =>200,'msg' => 'firstQQLogin successfully']);
        }
        $token = Hash::make($user->phone.date(DATE_W3C));
        Redis::set($user->phone,$token);
        Redis::expire($user->phone,100000);
        return Response::json(['status' =>200,'msg' => 'qqLogin successfully','user' => $user,'token' => $token]);
    }*/
}
