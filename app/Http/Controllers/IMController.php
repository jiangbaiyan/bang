<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class IMController extends Controller
{
    public function setHeader(){
        $appKey = '589b8d80917010609e25948ac3422451';
        $appSecret = '46f46dab1f0b';
        $nonce = (string)rand(1,1000000);
        $time = time();
        $checkSum = sha1($appSecret.$nonce.$time,false);
        $header = [
            "AppKey:$appKey",
            "Nonce:$nonce",
            "CurTime:$time",
            "CheckSum:$checkSum",
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8'
        ];
        return $header;
    }

    public function create(Request $request){
        $phone = $request->header('phone');
        if (!$phone){
            return Response::json(['status' => 400,'msg' => 'need phone']);
        }
        $token = Redis::get($phone);
        $postData = "accid=$phone";
        $url = 'https://api.netease.im/nimserver/user/create.action';
        $con = curl_init();
        curl_setopt($con,CURLOPT_HTTPHEADER,$this->setHeader());
        curl_setopt($con,CURLOPT_URL,$url);
        curl_setopt($con,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($con,CURLOPT_POST,1);
        curl_setopt($con,CURLOPT_POSTFIELDS,$postData);
        $result = curl_exec($con);
        curl_close($con);
        return $result;
    }

    public function update(Request $request){
        $phone = $request->input('phone');
        if (!$phone){
            return Response::json(['status' => 400,'msg' => 'need phone']);
        }
        $postData = "accid=$phone";
        $url = 'https://api.netease.im/nimserver/user/refreshToken.action';
        $con = curl_init();
        curl_setopt($con,CURLOPT_HTTPHEADER,$this->setHeader());
        curl_setopt($con,CURLOPT_URL,$url);
        curl_setopt($con,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($con,CURLOPT_POST,1);
        curl_setopt($con,CURLOPT_POSTFIELDS,$postData);
        $result = curl_exec($con);
        curl_close($con);
        return $result;
        /*$resultarr = json_decode($result,true);
        if ($resultarr['code']==200){
            return Response::json(['status' => 200,'msg' => 'id updated successfully','token' => $token]);
        */
    }
}
