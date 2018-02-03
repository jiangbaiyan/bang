<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DetailController extends Controller
{
    //查看与用户相关的订单详情（此时用户既可以是发单者也可以是接单者）
    public function getDetail(Request $request){
        $orderid = $request->header('orderid');
        if (!isset($orderid)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $order = Order::find($orderid);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        $applicantName = User::find($order->applicant_id)->name;
        if ($order->servant_id == null){//如果现在没有人接单
            return Response::json(['status' => 200,'msg' => 'order required successfully','data1' => $order,'data2' => ['applicant_name' => $applicantName,'servant_name' => '暂无接单者']]);
        }
        else{//有人接单
            $servantName = User::find($order->servant_id)->name;
            return Response::json(['status' => 200,'msg' => 'order required successfully','data1' => $order,'data2' => ['applicant_name' => $applicantName,'servant_name' => $servantName]]);
        }
    }

    //取消订单
    public function cancelOrder(Request $request){
        $orderid = $request->input('orderid');
        if (!isset($orderid)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        Order::destroy($orderid);
        return Response::json(['status' => 200,'msg' => 'success']);
    }
}
