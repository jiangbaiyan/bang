<?php

namespace App\Http\Controllers\HelpOthers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DetailController extends Controller
{
    //获取订单详情（等待接单的订单）
    public function getDetail(Request $request){
        $orderid = $request->header('orderid');
        if (!isset($orderid)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $data = Order::join('users','orders.applicant_id','=','users.id')
            ->select('orders.*','users.phone','users.name','users.sex','users.credit','users.head')
            ->find($orderid);
        if (!$data){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        return Response::json(['status' => 200,'msg' => 'success','data' => $data]);
    }

    //接单
    public function receiveOrder(Request $request){
        $userid = $request->input('id');
        $orderid = $request->input('orderid');
        if (!isset($orderid)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $order = Order::find($orderid);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        if($order->state!=1){
            return Response::json(['status' => 402,'msg' => 'the order is not waiting for receiving']);
        }
        if ($order->applicant_id == $userid){
            return Response::json(['status' => 403,'msg' => 'cannot receive your own order']);
        }
        $order->state = 2;
        $order->servant_id = $userid;
        $order->save();
        return Response::json(['status' => 200,'msg' => 'success']);
    }
}
