<?php

namespace App\Http\Controllers\Order;

use App\Models\Middle;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DetailController extends Controller
{
    public function showDetail(Request $request){//订单详情
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        $order = Order::find($id);
        if (!$order){
            return Response::json(['status' => 404,'msg' => 'order not exists']);
        }
        $applicantName = User::where('phone',$order->applicant)->first()->name;
        $servantName = User::where('phone',$order->servant)->first()->name;
        return Response::json(['status' => 200,'msg' => 'order required successfully','data1' => $order,'data2' => ['applicant_name' => $applicantName,'servant_name' => $servantName]]);
    }

    public function cancelOrder(Request $request){//取消订单
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 400,'msg' => 'need id']);
        }
        if (!Order::destroy($id)){
            return Response::json(['status' => 402,'msg' => 'orderTableData deleted failed']);
        }
        if (!Middle::where('order_id',$id)->delete()){
            return Response::json(['status' => 402,'msg' => 'middleTableData deleted failed']);
        }
        return Response::json(['status' => 200,'msg' => 'order deleted successfully']);
    }
}
