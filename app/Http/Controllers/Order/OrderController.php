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

class OrderController extends Controller{

    /**
     * 获取已发布的订单
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getSentOrder(){
        $user = UserModel::getCurUser();
        $datas = $user->sendOrders()
            ->select('id','title','status','content','price','updated_at')
            ->latest()
            ->simplePaginate(10);
        foreach ($datas as $items){
            $items->content = str_limit($items->content,100,'...');
        }
        return ApiResponse::responseSuccess($datas);
    }

    /**
     * 获取已接的单
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getReceivedOrder(){
        $user = UserModel::getCurUser();
        $datas = $user->receiveOrders()
            ->select('id','title','status','content','price','updated_at')
            ->latest()
            ->simplePaginate(10);
        foreach ($datas as $items){
            $items->content = str_limit($items->content,100,'...');
        }
        return ApiResponse::responseSuccess($datas);
    }

    /**
     * 查看订单详情
     * @param Request $request
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
        if ($order->status != OrderModel::statusWaitingComment){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        if (!isset($order->receiver)){
            throw new OperateFailedException(ConstHelper::USER);
        }
        $receiver = $order->receiver;
        $receiver->point += $req['star'];
        $receiver->save();
        return ApiResponse::responseSuccess();
    }
}