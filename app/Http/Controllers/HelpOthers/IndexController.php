<?php

namespace App\Http\Controllers\HelpOthers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class IndexController extends Controller
{
    public function getAllOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where('orders.state',1)->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'all orders required successfully','data' => $Orders]);
    }

    public function getRunOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 1])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'run orders required successfully','data' => $Orders]);
    }

    public  function getAskOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 2])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'ask orders required successfully','data' => $Orders]);
    }

    public function getLearnOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 3])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'learn orders required successfully','data' => $Orders]);
    }

    public function getTechniqueOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 4])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'technique orders required successfully','data' => $Orders]);
    }

    public function getLifeOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 5])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'life orders required successfully','data' => $Orders]);
    }

    public function getOtherOrders(){
        $Orders = Order::join('users','orders.applicant','=','users.phone')->select('orders.id','orders.title','orders.category','orders.state','orders.created_at','users.name','users.credit')->where(['orders.state' => 1,'orders.category' => 6])->orderBy('orders.created_at','desc')->get();
        return Response::json(['status' => 200,'msg' => 'other orders required successfully','data' => $Orders]);
    }
}
