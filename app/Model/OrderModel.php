<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    protected $guarded = ['id'];


    /**
     * 订单状态
     */
    const
        statusNotReleased = 0,
        statusReleased = 1,
        statusRunning = 2,
        statusFinished = 3;

    /**
     * 订单类别
     */
    const
        typeRunning = 0,
        typeAsking = 1,
        typeLearning = 2,
        typeTechnique = 3,
        typeDailyLife = 4,
        typeOthers = 5;
}
