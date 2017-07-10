<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class IndexController extends Controller
{
    public function getOrders(Request $request){
        $phone = $request->header('phone');
        $user = User::where('phone',$phone)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $orders = Order::where('applicant',$phone)->orWhere('servant',$phone)->orderBy('updated_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'orders required successfully','data' => $orders]);
    }

    public function finishService(Request $request){
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        if ($order->state != 1){
            return Response::json(['status' => 402,'msg' => 'wrong state, the service is not running']);
        }
        $order->state = 2;
        $order->save();
        $applicantUser = $order->applicant;
        $servantUser = $order->servant;
        $users = User::where('phone',$applicantUser)->orWhere('phone',$servantUser)->get();
            foreach ($users as $user){
            $user->credit +=5;
            $user->save();
        }
        return Response::json(['status' => 200,'msg' => 'finish service successfully']);
    }

    public function comment(Request $request){
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
