<?php

namespace App\Model;

use App\Helper\ConstHelper;
use App\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use src\Exceptions\ResourceNotFoundException;

class OrderModel extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $guarded = ['id'];

    protected $dates = ['delete_at'];

    /**
     * 订单状态
     */
    const
        statusNotReleased = 0,//草稿(暂时用不到)
        statusReleased = 1,//已发布
        statusRunning = 2,//正在服务
        statusWaitingComment = 3,//服务完成等待评价
        statusFinished = 4,//评价完成
        statusCanceled = 5;//订单取消


    /**
     * 订单类别
     */
    const
        typeRunning = 0,//跑腿
        typeAsking = 1,//悬赏提问
        typeLearning = 2,//学习辅导
        typeTechnique = 3,//技术服务
        typeDailyLife = 4,//生活服务
        typeOthers = 5;//其他

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

    /**
     * 获取两个订单之间的距离
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float|int
     */
    public static function getDistance($lng1, $lat1, $lng2, $lat2) {
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return $s;
    }
}
