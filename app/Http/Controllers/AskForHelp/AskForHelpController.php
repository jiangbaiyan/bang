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
        $order = Order::create(['title' => $title,'content' => $content,'category' => $category,'state' => 1,'end_time' => $endTime,'money' => $money,'applicant_id' => $id]);
        $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
        $allowedFormat = ['png','bmp','jpg','jpeg'];
        if ($request->hasFile('img')){
            $img = $request->file('img');
            $ext = $img->getClientOriginalExtension();//获取扩展名
            $name = $img->getClientOriginalName();
            if (!in_array($ext,$allowedFormat)){//判断格式是否是允许上传的格式
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = \Storage::disk('upyun')->putFileAs("Bang/order/$id",$img,"$order->id".'_'.time().'_'.$name,'public');
            $url = $url.$path;
            $order->image = $url;
            $order->save();
        }
        return Response::json(['status' => 200,'msg' => 'success']);
    }
}
