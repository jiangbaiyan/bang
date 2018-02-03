<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class IndexController extends Controller
{
    //获取与当前用户相关订单的列表（此时用户既可以是发单者也可以是接单者）
    public function getOrders(Request $request){
        $userid = $request->header('id');
        $user = User::find($userid);
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $orders = Order::where('applicant_id','=',$userid)
            ->orWhere('servant_id',$userid)
            ->orderBy('updated_at','desc')
            ->get();
        return Response::json(['status' => 200,'msg' => 'success','data' => $orders]);
    }

    //完成订单
    public function finishService(Request $request){
        $orderid = $request->input('orderid');
        if (!isset($orderid)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $order = Order::find($orderid);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        if ($order->state != 2){
            return Response::json(['status' => 402,'msg' => 'the service is not running']);
        }
        $order->state = 3;
        $applicant = User::find($order->applicant_id);
        $servant = User::find($order->servant_id);
        $servant->credit += 5;
        $applicant->credit += 5;
        $applicant->save();
        $servant->save();
        $order->save();
        return Response::json(['status' => 200,'msg' => 'success']);
    }

    //评价
    public function comment(Request $request){
        $star = $request->input('star');
        $orderid = $request->input('orderid');
        if (!isset($orderid)||!isset($star)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        $order = Order::find($orderid);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        if ($order->state != 3){
            return Response::json(['status' => 402,'msg' => 'the service is not finished']);
        }
        $servant = User::find($order->servant_id);
        $servant->credit += $star;
        $servant->save();
        $order->state = 4;
        $order->save();
        return Response::json(['status' => 200,'msg' => 'success']);
    }
}
