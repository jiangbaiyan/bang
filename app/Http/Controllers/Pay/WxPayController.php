<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/23/023
 * Time: 8:22
 */

namespace App\Http\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;
use Yansongda\Pay\Pay;

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
        $pay = Pay::wechat(config('wx.pay'))->miniapp($params);
        return ApiResponse::responseSuccess($pay);
    }

    /**
     * 通知微信支付结果
     * @return string
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function notify(){
        $pay = Pay::wechat(config('wx.pay'));
        $data = $pay->verify();
        Log::debug('Wechat notify', $data->all());
        return $pay->success();
    }
}