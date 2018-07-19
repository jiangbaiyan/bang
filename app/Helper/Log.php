<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/7/19
 * Time: 下午7:05
 */

namespace App\Helper;
use Illuminate\Support\Facades\Log as BaseLog;

class Log
{
    public static function fatal($msg){
        BaseLog::channel('daily')->error(json_encode($msg));
    }

    public static function notice($msg){
        BaseLog::channel('daily')->notice(json_encode($msg));
    }
}