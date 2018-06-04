<?php

namespace App;

use App\Model\OrderModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use src\Exceptions\UnAuthorizedException;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = ['id'];


    /**
     * 获取当期登录用户模型或id
     * @return UserModel|\Illuminate\Contracts\Auth\Authenticatable|null
     * @throws UnAuthorizedException
     */
    public static function getCurUser(bool $isNeedId = false){
        if ($isNeedId){
            $res  = Auth::id();
        } else{
            $res = Auth::user();
        }
        if (!$res){
            throw new UnAuthorizedException();
        }
        return $res;
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

    /**
     * 获取所有发送的订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sendOrders(){
        return $this->hasMany(OrderModel::class,'sender_id','id');
    }


    /**
     * 获取所有接到的订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receiveOrders(){
        return $this->hasMany(OrderModel::class,'receiver_id','id');
    }
}
