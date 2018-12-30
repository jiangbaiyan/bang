<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/4
 * Time: 10:27
 */

namespace App\Http\Controllers\AskForHelp;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Logger\Logger;

class AskForHelpController extends Controller{


    /**
     * 求支援发布订单
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function releaseOrder(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'title' => 'required',
            'content' => 'required',
            'beginTime' => 'required|date',
            'endTime' => 'required|date',
            'type' => 'required',
            'price' => 'required',
            'longitude' => 'required',
            'latitude' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        if (strtotime($req['beginTime']) >= strtotime($req['endTime'])){
            Logger::notice('ask|illegal_time|params:' . json_encode($req));
            throw new OperateFailedException('起止时间不合法，请重新输入');
        }
        $orderModel = new OrderModel();
        $orderModel->title = $req['title'];
        $orderModel->content = $req['content'];
        $orderModel->begin_time = $req['beginTime'];
        $orderModel->end_time = $req['endTime'];
        $orderModel->type = $req['type'];
        $orderModel->status = OrderModel::STATUS_WAITING_PAY;
        $orderModel->price = $req['price'];
        $orderModel->sender_id = $req['user']->id;
        $orderModel->uuid = time() . mt_rand(0,100000);
        $orderModel->longitude = $req['longitude'];
        $orderModel->latitude = $req['latitude'];
        $orderModel->save();
        return ApiResponse::responseSuccess(['id' => $orderModel->id]);
    }

    /**
     * 取消订单(软删除）
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \Exception
     */
    public function cancelOrder(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        if ($order->status != OrderModel::STATUS_RELEASED){
            Logger::notice('ask|wrong_order_status|order:' . json_encode($order));
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $order->delete();
        if (!$order->trashed()){
            Logger::notice('ask|delete_order_failed|order:' . json_encode($order));
            throw new OperateFailedException('删除失败，请稍后重试');
        }
        $order->status = OrderModel::STATUS_CANCELED;
        $order->save();
        //TODO:微信退款逻辑
        return ApiResponse::responseSuccess();
    }
}