<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class IndexController extends Controller
{
    public function getOrders(Request $request){//获取所有订单
        $phone = $request->header('phone');
        $user = User::where('phone',$phone)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $orders = $user->orders;
        return Response::json(['status' => 200,'msg' => 'orders required successfully','data' => $orders]);
    }

    public function finishService(Request $request){//对发单者适用
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        $order->state = 2;
        $order->save();
        $users = $order->users;
        foreach ($users as $user){
            $user->credit +=5;
            $user->save();
        }
        return Response::json(['status' => 200,'msg' => 'finish service successfully']);
    }

    public function comment(Request $request){//对发单者和接单者适用
        $star = $request->input('star');
        $id = $request->input('id');
        if (!$star||!$id){
            return Response::json(['status' => 400,'msg' => 'need star or id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        $servant = $order->servant;
        $userServant = User::where('phone',$servant)->first();
        if (!$userServant){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $userServant->credit += $star;
        if (!$userServant->save()){
            return Response::json(['status' => 402,'msg' => 'comment failed']);
        }
        return Response::json(['status' => 200,'msg' => 'comment successfully']);
    }
}
