<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/13
 * Time: 16:34
 */

namespace App\Http\Controllers\Order;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Logger\Logger;

class OrderController extends Controller{

    /**
     * 获取已发布的订单
     * @param Request $request
     * @return string
     */
    public function getSentOrder(Request $request){
        $page = $request->get('page') ?? 1;
        $size = $request->get('size') ?? 10;
        $userId = $request->get('user')->id;
        $middleRes = OrderModel::where('sender_id',$userId)
            ->withTrashed()
            ->select('id','title','status','content','price','updated_at')
            ->latest();
        $data = OrderModel::packLimitData($middleRes,$page,$size,$request->fullUrl());
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 获取已接的单
     * @param Request $request
     * @return string
     */
    public function getReceivedOrder(Request $request){
        $page = $request->get('page') ?? 1;
        $size = $request->get('size') ?? 10;
        $userId = $request->get('user')->id;
        $middleRes = OrderModel::where('receiver_id',$userId)
            ->withTrashed()
            ->select('id','title','status','content','price','updated_at')
            ->latest();
        $data = OrderModel::packLimitData($middleRes,$page,$size,$request->fullUrl());
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 查看订单详情
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function getOrderDetail(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id'])->toArray();
        $sender = UserModel::getUserById($order['sender_id']);
        $receiver = UserModel::getUserById($order['receiver_id']);
        if (!empty($receiver)){
            $receiver = $receiver->toArray();
        }
        if (!empty($sender)){
            $sender = $sender->toArray();
        }
        return ApiResponse::responseSuccess(array_merge($order,[
            'sender' => $sender,
            'receiver' => $receiver
        ]));
    }

    /**
     * 评价订单
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function commentOrder(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required','star' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        if ($order->status != OrderModel::STATUS_WAITING_COMMENT){
            Logger::notice('order|wrong_order_status|order:' . json_encode($order));
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        if (empty($order->receiver_id)){
            Logger::notice('order|no_order_receiver|order:' . json_encode($order));
            throw new OperateFailedException(ConstHelper::USER);
        }
        $order->status = OrderModel::STATUS_FINISHED;
        $order->save();
        $receiver = UserModel::find($order->receiver_id);
        $receiver->point += $req['star'];
        $receiver->save();
        return ApiResponse::responseSuccess();
    }

}