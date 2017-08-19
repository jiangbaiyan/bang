<?php

namespace App\Http\Controllers\HelpOthers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DetailController extends Controller
{
    public function getDetail(Request $request){
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        $user = User::where('phone',$order->applicant)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'applicant not exists']);
        }
        return Response::json(['status' => 200,',msg' => 'order required successfully','data1' => $order,'data2' => ['applicant_name' => $user->name,'applicant_credit' => $user->credit,'applicant_sex' => $user->sex]]);
    }

    public function receiveOrder(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        if($order->state!=1){
            return Response::json(['status' => 402,'msg' => 'the order is not waiting for receiving']);
        }
        $order->state = 2;
        $order->save();
        return Response::json(['status' => 200,'msg' => 'receive order successfully']);
    }
}
