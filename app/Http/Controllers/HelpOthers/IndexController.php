<?php

namespace App\Http\Controllers\HelpOthers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IndexController extends Controller
{
    public function getOrder(Request $request){
        $category = $request->header('category');
        if (!isset($category)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        switch ($category){
            case 0://全部订单
                $Orders = Order::join('users','orders.applicant_id','=','users.id')
                    ->select('orders.id','orders.title','orders.category','orders.end_time','orders.state','orders.image','orders.money','orders.created_at','users.phone','users.name','users.sex','users.credit','users.head')
                    ->where('orders.state',1)
                    ->orderByDesc('orders.created_at')
                    ->get();
                break;
            default://其余类别订单
                $Orders = Order::join('users','orders.applicant_id','=','users.id')
                    ->select('orders.id','orders.title','orders.category','orders.end_time','orders.state','orders.image','orders.money','orders.created_at','users.phone','users.name','users.sex','users.credit','users.head')
                    ->where(['orders.state' => 1,'orders.category' => $category])
                    ->orderByDesc('orders.created_at')
                    ->get();
                break;
        }
        return Response::json(['status' => 200,'msg' => 'success','data' => $Orders]);
    }

}
