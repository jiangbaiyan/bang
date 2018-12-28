<?php

namespace App\Http\Middleware;

use App\Helper\ConstHelper;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use src\Exceptions\UnAuthorizedException;
use src\Logger\Logger;

class CheckLogin
{
    const REDIS_TOKEN_PREFIX = 'bang_token_%s';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws UnAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        $frontToken = Request::header('Authorization');
        if (empty($frontToken)) {
            Logger::notice('auth|header_token_empty');
            throw new UnAuthorizedException();
        }
        try{
            $user = JWT::decode($frontToken, ConstHelper::JWT_KEY ,['HS256']);
        }catch (\Exception $e){
            Logger::notice('auth|decode_token_failed|msg:' . $e->getMessage() . 'frontToken:'. $frontToken);
            throw new UnAuthorizedException();
        }
        $redisKey = sprintf(self::REDIS_TOKEN_PREFIX,$user->phone);
        if (Redis::ttl($redisKey) <= 0) {
            Logger::notice('auth|token_expired|user:' . json_encode($user));
            throw new UnAuthorizedException();
        }
        $token = Redis::get($redisKey);//查redis里token，比较
        if ($frontToken !== $token) {
            Logger::notice('auth|front_token_not_equals_redis_token|front_token:' . $frontToken . '|redis_token:' . $token);
            throw new UnAuthorizedException();
        }
        $request->merge(['user' => $user]);
        return $next($request);
    }
}
