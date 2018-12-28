<?php
/**
 * Created by PhpStorm.
 * UserModel: Baiyan
 * Date: 2018/4/5
 * Time: 18:45
 */
namespace App\Service;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use Illuminate\Support\Facades\Redis;
use src\ApiHelper\ApiResponse;
use src\Logger\Logger;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ResourceNotFoundException;


/**
 * 短信发送服务类
 * Class SmsService
 * @package App\Service
 */
class SmsService{

    const REDIS_SMS_VERIFY = 'bang_sms_verify_%s';

    //阿里云短信配置
    private static $config = [
        'accessKeyId'    => 'LTAIFUVgskph3h00',
        'accessKeySecret' => 'eq1CrL826196CvOm65wc7n5BPN3PZ9',
    ];

    /**
     * 获取验证码
     * @param $phone
     * @return string
     * @throws OperateFailedException
     */
    public static function getCode($phone){
        $client = new Client(self::$config);
        $sendSms = new SendSms();
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName('帮帮吧');
        $sendSms->setTemplateCode('SMS_126460515');
        $code = rand(1000, 9999);
        $sendSms->setTemplateParam(compact('code'));
        try{
            $res = $client->execute($sendSms);
            $res = json_decode(json_encode($res),true);
            if ($res['Code'] != 'OK'){
                Logger::fatal('sms|send_sms_failed|msg:' . json_encode($res['Message']));
                throw new OperateFailedException('短信官方接口异常,请稍后重试');
            }
        } catch (\Exception $e){
            Logger::fatal('sms|send_sms_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException('短信官方接口异常,请稍后重试');
        }
        //设置Cache，为验证接口使用
        $key = sprintf(self::REDIS_SMS_VERIFY,$phone);
        Redis::set($key,$code);
        Redis::expire($key,90);
        return ApiResponse::responseSuccess();
    }

    /**
     * 判断验证码是否正确
     * @param $phone
     * @param $frontCode
     * @throws OperateFailedException
     */
    public static function verifyCode($phone,$frontCode){
        $key = sprintf(self::REDIS_SMS_VERIFY,$phone);
        if (Redis::ttl($key) <= 0){
            Logger::notice('sms|wrong_sms_code|key:' . $key . '|frontCode:' . $frontCode);
            throw new OperateFailedException('短信验证码已过期，请重新获取');
        }
        $backCode = Redis::get($key);
        if ($frontCode != $backCode){
            Logger::notice('sms|wrong_sms_code|key:' . $key . '|frontCode:' . $frontCode . '|backCode:' . $backCode);
            throw new OperateFailedException('短息验证码错误，请重试');
        }
    }
}