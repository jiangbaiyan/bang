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
        $user = $request->get('user');
        $middleRes = $user->sendOrders()
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
        $user = $request->get('user');
        $middleRes = $user->receiveOrders()
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
        $order = OrderModel::getOrderById($req['id']);
        $order->sender;
        $order->receiver;
        return ApiResponse::responseSuccess($order);
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
        if (!isset($order->receiver)){
            Logger::notice('order|no_order_receiver|order:' . json_encode($order));
            throw new OperateFailedException(ConstHelper::USER);
        }
        $order->status = OrderModel::STATUS_FINISHED;
        $order->save();
        $receiver = $order->receiver;
        $receiver->point += $req['star'];
        $receiver->save();
        return ApiResponse::responseSuccess();
    }

}