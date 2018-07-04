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
use App\UserModel;
use EasyWeChat\Factory;
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
        $app = Factory::payment(config('wechat.payment.default'));
        $result = $app->order->unify([
            'body' => $order->title,
            'out_trade_no' => time(),
            'total_fee' => ($order->price) * 100,
            'trade_type' => 'JSAPI',
            'openid' => $user->openid,
        ]);
        return ApiResponse::responseSuccess($result);
    }

    /**
     * 通知微信支付结果
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify(){
        $app = Factory::payment(config('wechat.payment.default'));
        $response = $app->handlePaidNotify(function ($message,$fail){
            return true;
        });
        return $response;
    }


    /**
     * 付款给接单者
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function transfer(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        if ($order->status != OrderModel::statusWaitingComment){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $app = Factory::payment(config('wechat.payment.default'));
        $params = [
            'partner_trade_no' => $order->id, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $order->receiver->openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            'amount' => ($order->price) * 100, // 企业付款金额，单位为分
            'desc' => $order->title, // 企业付款操作说明信息。必填
        ];
        $result = $app->transfer->toBalance($params);
        return ApiResponse::responseSuccess($result);
    }
}