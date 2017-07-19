<?php

namespace App\Http\Controllers\HelpOthers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function submitReport(Request $request){
        $text = $request->input('text');
        if (!$text){
            return Response::json(['status' => 400,'msg' => 'need text']);
        }
        if (Report::create(['msg' => $text])){
            return Response::json(['status' => 200,'msg' => 'report saved successfully']);
        }
        else return Response::json(['status' => 402,'msg' => 'report saved failed']);
    }
}
