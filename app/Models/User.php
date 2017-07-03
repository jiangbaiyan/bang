<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    public function orders(){
        return $this->belongsToMany('App\Models\Order');
    }
}
