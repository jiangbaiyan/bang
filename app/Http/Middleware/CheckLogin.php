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
        if ($request->isMethod('GET')){
            $id = $request->header('id');
            $token = $request->header('token');
        }
        else{
            $id = $request->input('id');
            $token = $request->input('token');
        }
        if (!isset($id)||!isset($token)){
            return response()->json(['status' => '400','msg' => 'missing parameters']);
        }
        $token_exists = Redis::exists($id);
        if(!$token_exists){
            return Response::json(["status"=>404,"msg"=>"token not exists"]);
        }
        $redisToken = Redis::get($id);
        if(strcmp($redisToken,$token)!=0){
            return Response::json(["status"=>402,"msg"=>"wrong login token"]);
        }
        return $next($request);
    }
}
