<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4/004
 * Time: 23:09
 */
namespace App\Http\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Helper\ConstHelper;
use App\Model\OrderModel;
use App\Service\AliService;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class AliPayController extends Controller{

    /**
     * 支付宝企业付款
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function aliTransfer(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,['id' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $order = OrderModel::getOrderById($req['id']);
        $userAliAccount = UserModel::getCurUser()->aliAccount;
        if ($order->status != OrderModel::statusWaitingComment){
            throw new OperateFailedException(ConstHelper::WRONG_ORDER_STATUS);
        }
        $params = [
            'out_biz_no' => time(),
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => $userAliAccount,
            'amount' => $order->price,
        ];
        $app = AliService::getAliPayApp();
        $res = $app->transfer($params);
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 支付宝返回结果通知
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function aliNotify(){
        $app = AliService::getAliPayApp();
        $app->verify();
        return $app->success();
    }
}