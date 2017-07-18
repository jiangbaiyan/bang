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
        $phone = $request->input('phone');
        $title = $request->input('title');
        $content = $request->input('content');
        $category = $request->input('category');
        $closeTime = $request->input('close_time');
        $money = $request->input('money');
        if (!$title||!$content||!$category||!$closeTime||!$money){
            return Response::json(['status' => 400,'msg' => 'need title or content or category or close_time or money']);
        }
        if (!Order::create(['title' => $title,'content' => $content,'category' => $category,'state' => 1,'close_time' => $closeTime,'money' => $money,'applicant' => $phone])){
            return Response::json(['status' => 402,'msg' => 'order created failed']);
        }
        return Response::json(['status' => 200,'msg' => 'order created successfully']);
    }
}
