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
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class AskForHelpController extends Controller{


    /**
     * 求支援发布订单
     * @param Request $request
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws OperateFailedException
     */
    public function releaseOrder(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'title' => 'required|max:255',
            'content' => 'required',
            'beginTime' => 'required|date|before:'.$req['endTime'],
            'endTime' => 'required|date',
            'type' => 'required',
            'price' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $orderModel = new OrderModel();
        $orderModel->title = $req['title'];
        $orderModel->content = $req['content'];
        $orderModel->begin_time = $req['beginTime'];
        $orderModel->end_time = $req['endTime'];
        $orderModel->type = $req['type'];
        $orderModel->status = OrderModel::statusReleased;
        $orderModel->price = $req['price'];
        $orderModel->sender_id = UserModel::getCurUser(true);
        $orderModel->uuid = time() . mt_rand(0,100000);
        if (!$orderModel->save()){
            throw new OperateFailedException();
        };
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
        if ($order->status != OrderModel::statusReleased){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $order->delete();
        if (!$order->trashed()){
            throw new OperateFailedException();
        }
        //TODO:微信退款逻辑
        return ApiResponse::responseSuccess();
    }
}