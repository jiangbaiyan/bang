<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use src\Exceptions\UnAuthorizedException;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guarded = ['id'];


    /**
     * 获取当期登录用户
     * @return UserModel|\Illuminate\Contracts\Auth\Authenticatable|null
     * @throws UnAuthorizedException
     */
    public function getCurUser(){
        $user = Auth::user();
        if (!$user){
            throw new UnAuthorizedException();
        }
        return $user;
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
