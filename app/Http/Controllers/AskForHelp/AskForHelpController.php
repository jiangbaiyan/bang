<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/4
 * Time: 10:27
 */

namespace App\Http\Controllers\AskForHelp;

use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        if (!$orderModel->save()){
            throw new OperateFailedException();
        };
        return ApiResponse::responseSuccess(['id' => $orderModel->id]);
    }

}