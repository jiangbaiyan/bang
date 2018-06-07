<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/7
 * Time: 10:00
 */

namespace App\Http\Controllers\HelpOthers;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class HelpOthersController extends Controller{

    /**
     * 校验id并且根据id获取到订单并返回
     * @param array $req
     * @return OrderModel|mixed
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    private function verifyIdAndReturnOrder(array $req){
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $orderId = $req['id'];
        $order = OrderModel::getOrderById($orderId);
        return $order;
    }


    /**
     * 获取所有等待服务的订单（可传递参数选择类型）
     * @param Request $request
     * @return string
     */
    public function getReleasedOrdersList(Request $request){
        $param = $request->input('type');
        $orderModel = new OrderModel();
        $res = $orderModel
            ->select('id','title','content','begin_time','end_time','price')
            ->where('status',OrderModel::statusReleased);
        if (isset($param)){
            $res = $res->where('type',$param);
        }
        $data = $res->simplePaginate(10);
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 获取某个等待服务订单的详情
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function getReleasedOrderDetail(Request $request){
        $req = $request->all();
        $order = $this->verifyIdAndReturnOrder($req);
        return ApiResponse::responseSuccess($order);
    }

    /**
     * 接单
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function receiveOrder(Request $request){
        $req = $request->all();
        $order = $this->verifyIdAndReturnOrder($req);
        if ($order->status != OrderModel::statusReleased){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $userId = UserModel::getCurUser(true);
        if ($order->sender_id == $userId){
            throw new OperateFailedException(ConstHelper::WRONG_RECEIVER);
        }
        $order->status = OrderModel::statusRunning;
        $order->receiver_id = $userId;
        if (!$order->save()){
            throw new OperateFailedException();
        }
        return ApiResponse::responseSuccess();
    }

    /**
     * 完成订单
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function finishOrder(Request $request){
        $req = $request->all();
        $order = $this->verifyIdAndReturnOrder($req);
        if ($order->status != OrderModel::statusRunning){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $userId = UserModel::getCurUser(true);
        if ($order->sender_id != $userId){
            throw new OperateFailedException(ConstHelper::WRONG_FINISHER);
        }
        $sender = $order->sender;
        $sender->point += OrderModel::awardSenderPoint;
        $sender->save();
        $receiver = $order->receiver;
        $receiver->point += OrderModel::awardReceiverPoint;
        $receiver->save();
        $order->status = OrderModel::statusFinished;
        if (!$order->save()){
            throw new OperateFailedException();
        }
        return ApiResponse::responseSuccess();
    }
}