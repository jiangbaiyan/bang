<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/23/023
 * Time: 8:22
 */

namespace App\Http\Controllers\Pay;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\Service\WxService;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class WxPayController extends Controller{

    /**
     * 统一下单
     * @param Request $request
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function unifyPay(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        $user = UserModel::getCurUser();
        $params = [
            'out_trade_no' => time(),
            'total_fee' => ($order->price) * 100, // **单位：分**
            'body' => $order->title,
            'openid' => $user->openid,
        ];
        $app = WxService::getWxPayApp();
        $res = $app->miniapp($params);
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 微信支付返回结果通知
     * @return string
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function wechatNotify(){
        $app = WxService::getWxPayApp();
        $app->verify();
        return $app->success();
    }


    /**
     * 付款给接单者
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function wxTransfer(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        if ($order->status != OrderModel::statusWaitingComment){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $params = [
            'partner_trade_no' => time(),              //商户订单号
            'openid' => $order->receiver->openid,        //收款人的openid
            'check_name' => 'NO_CHECK',                //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
            'amount' => ($order->price) * 100,         //企业付款金额，单位为分
            'desc' => $order->title,                   //付款说明
            'type' => 'miniapp'
        ];
        $app = WxService::getWxPayApp();
        $res = $app->transfer($params);
        return ApiResponse::responseSuccess($res);
    }
}