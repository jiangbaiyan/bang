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
     * 获取所有等待服务的订单(10km以内)
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     */
    public function getReleasedOrdersList(Request $request){
        $page = $request->get('page') ?? 1;
        $size = $request->get('size') ?? 10;
        $req = $request->all();
        $validator = Validator::make($req,['longitude' => 'required','latitude' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $param = $request->input('type');
        $orderModel = new OrderModel();
        $now = date('Y-m-d H:i:s');
        $res = $orderModel
            ->select('id','title','content','price','longitude','latitude','created_at')
            ->where('status',OrderModel::statusReleased)
            ->where('begin_time','<',$now)
            ->where('end_time','>',$now)
            ->latest();
        if (isset($param)){
            $res = $res->where('type',$param);
        }
        $datas = OrderModel::packLimitData($res,$page,$size,$request->fullUrl());
        if (empty($datas)){
            return ApiResponse::responseSuccess();
        }
        $curLng = $req['longitude'];
        $curLat = $req['latitude'];
        $realData = $datas['data'];
        foreach ($realData as &$data){
            $orderLng = $data['longitude'];
            $orderLat = $data['latitude'];
            $distance = OrderModel::getDistance($curLng,$curLat,$orderLng, $orderLat);
            $data['distance'] = $distance;
        }
        array_multisort(array_column($realData,'distance'),SORT_ASC,SORT_NUMERIC,$realData);
        $limitArr = [
            'first_page_url' => $datas['first_page_url'],
            'last_page_url' => $datas['last_page_url'],
            'current_page' => $datas['current_page'],
            'next_page_url' => $datas['next_page_url'],
            'prev_page_url' => $datas['prev_page_url'],
            'data_count' => $datas['data_count'],
            'total_page' => $datas['total_page']
        ];
        return ApiResponse::responseSuccess(array_merge(['data' => $realData],$limitArr));
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
        $order->sender;
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
        $order->save();
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
        $order->status = OrderModel::statusWaitingComment;
        $order->save();
        return ApiResponse::responseSuccess();
    }
}