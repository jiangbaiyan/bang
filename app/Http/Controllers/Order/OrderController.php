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
    public function getSentOrder(Request $request){
        $page = $request->get('page') ?? 0;
        $size = $request->get('size') ?? 10;
        $user = UserModel::getCurUser();
        $middleRes = $user->sendOrders()
            ->withTrashed()
            ->select('id','title','status','content','price','updated_at')
            ->latest();
        $limitParams = OrderModel::calculateLimitParam($page,$size);
        $datas = $middleRes->limit($limitParams['offset'],$limitParams['size'])->get()->toArray();
        $count = $middleRes->count();
        $pageData = OrderModel::calculatePage($count,$page,$request->fullUrl(),$size);
        foreach ($datas as $items){
            $items['content'] = str_limit($items['content'],100,'...');
        }
        return ApiResponse::responseSuccess(array_merge($datas,$pageData));
    }

    /**
     * 获取已接的单
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getReceivedOrder(Request $request){
        $page = $request->get('page') ?? 0;
        $size = $request->get('size') ?? 10;
        $user = UserModel::getCurUser();
        $middleRes = $user->receiveOrders()
            ->withTrashed()
            ->select('id','title','status','content','price','updated_at')
            ->latest();
        $limitParams = OrderModel::calculateLimitParam($page,$size);
        $datas = $middleRes->limit($limitParams['offset'],$limitParams['size'])->get()->toArray();
        $count = $middleRes->count();
        $pageData = OrderModel::calculatePage($count,$page,$request->fullUrl(),$size);
        foreach ($datas as $items){
            $items['content'] = str_limit($items['content'],100,'...');
        }
        return ApiResponse::responseSuccess(array_merge($datas,$pageData));
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
        if ($order->status != OrderModel::statusWaitingComment){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        if (!isset($order->receiver)){
            throw new OperateFailedException(ConstHelper::USER);
        }
        $order->status = OrderModel::statusFinished;
        $order->save();
        $receiver = $order->receiver;
        $receiver->point += $req['star'];
        $receiver->save();
        return ApiResponse::responseSuccess();
    }

    /**
     * 永久删除订单
     * @param Request $request
     */
    public function deleteOrder(Request $request){

    }
}