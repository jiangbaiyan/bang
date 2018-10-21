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
