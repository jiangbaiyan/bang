<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $phone = $request->input('phone')||$request->header('phone');
        $token = $request->input('token')||$request->header('token');
        if (!$phone||!$token){
            return response()->json(['status' => '400','msg' => 'need phone or token']);
        }
        $token_exists = Redis::exists($phone);
        if(!$token_exists){
            return Response::json(["status"=>404,"msg"=>"token not exists"]);
        }
        $redisToken = Redis::get($phone);
        if(strcmp($redisToken,$token)!=0){
            return Response::json(["status"=>402,"msg"=>"wrong login token"]);
        }
        return $next($request);
    }
}
