<?php

namespace App\Http\Controllers\AskForHelp;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Response;

class AskForHelpController extends Controller
{
    public function createOrder(Request $request){
        $id = $request->input('id');
        $title = $request->input('title');
        $content = $request->input('content');
        $category = $request->input('category');
        $endTime = $request->input('end_time');
        $money = $request->input('money');
        if (!isset($id)||!isset($title)||!isset($content)||!isset($category)||!isset($endTime)||!isset($money)){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        Order::create(['title' => $title,'content' => $content,'category' => $category,'state' => 1,'end_time' => $endTime,'money' => $money,'applicant_id' => $id]);
        return Response::json(['status' => 200,'msg' => 'success']);
    }

}
