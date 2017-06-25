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
    public function login(Request $request){
        $phone = $request->input('phone');
        $password = $request->input('password');
        if (!$phone||!$password){
            return Response::json(['status' => 400,'msg' => 'need phone or password']);
        }
        $user = User::where('phone',$phone)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        if (!Hash::check($password,$user->password)){
            return Response::json(['status' => 402,'msg' => 'wrong password']);
        }
        $token = Hash::make($phone.$password.date(DATE_W3C));
        Redis::set($phone,$token);
        Redis::expire($phone,100000);
        return Response::json(["status"=>200,"msg"=>"login successfully",'data' => ['phone' => $phone,'token' => $token]]);
    }
}
