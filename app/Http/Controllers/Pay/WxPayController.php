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
use src\Logger\Logger;

class WxPayController extends Controller{

    /**
     * 统一下单
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function unifyPay(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        $user = $req['user'];
        $params = [
            'out_trade_no' => $order->uuid,
            'total_fee' => ($order->price) * 100, // **单位：分**
            'body' => $order->title,
            'openid' => $user->openid,
        ];
        if (empty($order->uuid) || empty($order->price) || empty($order->title) || empty($user->openid)){
            Logger::notice('wxpay|unify_pay_params_error' . json_encode($params));
            throw new OperateFailedException('统一下单参数不正确');
        }
        $app = WxService::getWxPayApp();
        Logger::notice('wxpay|unify_pay_params:' . json_encode($params));
        try{
            $res = $app->miniapp($params);
        } catch (\Exception $e){
            Logger::fatal('wxpay|error:' . json_encode($e->getMessage()));
            throw new OperateFailedException('调用支付接口异常');
        }
        Logger::notice('wxpay|unify_pay_res:|res:' . json_encode($res));
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 微信支付返回结果通知
     * @return string
     * @throws OperateFailedException
     */
    public function wechatNotify(){
        try{
            $app = WxService::getWxPayApp();
            $app->verify();
            return $app->success();
        } catch (\Exception $e){
            Logger::notice('wxpay|notify_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException($e->getMessage());
        }
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
        if ($order->status != OrderModel::STATUS_WAITING_COMMENT){
            Logger::notice('wxpay|wrong_order_status|order:' . json_encode($order));
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $receiver = UserModel::find($order->receiver_id);
        if (empty($receiver->openid) || empty($order->uuid) || empty($order->price) || empty($order->title)){
            Logger::notice('wxpay|transfer_params_error' . json_encode($params));
            throw new OperateFailedException('转账参数不正确');
        }
        $params = [
            'partner_trade_no' => $order->uuid,              //商户订单号
            'openid' => $receiver->openid,        //收款人的openid
            'check_name' => 'NO_CHECK',                //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
            'amount' => ($order->price) * 100,         //企业付款金额，单位为分
            'desc' => $order->title,                   //付款说明
            'type' => 'miniapp'
        ];
        $app = WxService::getWxPayApp();
        Logger::notice('wxpay|wxtransfer_pay_params:' . json_encode($params));
        try{
            $res = $app->transfer($params);
        } catch (\Exception $e){
            Logger::fatal('wxpay|wxtransfer_error:' . json_encode($e->getMessage()));
            throw new OperateFailedException('调用转账接口失败');
        }
        Logger::notice('wxpay|wxtransfer_pay_res:|res:' . json_encode($res));
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 发送模板消息
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function sendModelInfo(Request $request){
        $validator = Validator::make($req = $request->all(), [
            'id' => 'required',
            'form_id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $statusMapping =  array(
            '跑腿',
            '悬赏提问',
            '学习辅导',
            '技术服务',
            '生后服务',
            '其他'
        );
        $order = OrderModel::getOrderById($req['id']);
        $order->status = OrderModel::STATUS_RELEASED;
        $order->save();
        $openid = $request->get('user')->openid;
        WxService::sendModelInfo($openid, [
            'form_id'    => $req['form_id'],
            'created_at' => $order->created_at,
            'uuid'       => $order->uuid,
            'type'       => $statusMapping[$order->type],
            'title'      => $order->title,
            'price'      => $order->price
        ]);
        return ApiResponse::responseSuccess();
    }
}