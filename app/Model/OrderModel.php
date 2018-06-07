<?php

namespace App\Model;

use App\Helper\ConstHelper;
use App\UserModel;
use Illuminate\Database\Eloquent\Model;
use src\Exceptions\ResourceNotFoundException;

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

    /**
     * 奖励积分数量
     */
    const
        awardSenderPoint = 1,
        awardReceiverPoint = 5;

    /**
     * 获取发送者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender(){
        return $this->belongsTo(UserModel::class,'sender_id','id');
    }

    /**
     * 获取接单者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver(){
        return $this->belongsTo(UserModel::class,'receiver_id','id');
    }

    /**
     * 根据id查找订单模型
     * @param $id
     * @param array $select
     * @return OrderModel|mixed
     * @throws ResourceNotFoundException
     */
    public static function getOrderById($id,$select = []){
        $orderModel = new OrderModel();
        if (!empty($select)){
            $order = $orderModel->select($select)->find($id);
        } else{
            $order = $orderModel->find($id);
        }
        if (!$order){
            throw new ResourceNotFoundException(ConstHelper::ORDER);
        }
        return $order;
    }

}
