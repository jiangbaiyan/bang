<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 13:51
 */

namespace App\Http\Controllers\Common;

use App\Helper\ConstHelper;
use App\Http\Controllers\Controller;
use App\Service\SmsService;
use App\UserModel;
use ComConf;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\Exceptions\OperateFailedException;
use src\Logger\Logger;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;

class HduLogin extends Controller {


    const LOGIN_SERVER = 'http://cas.hdu.edu.cn/cas/login';

    const VALIDATE_SERVER = 'http://cas.hdu.edu.cn/cas/serviceValidate';

    const THIS_URL = ConstHelper::HOST . '/common/hduLogin';


    /**
     * 获取验证码
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     */
    public function getCode(){
        $validator = Validator::make($req = Request::all(),['phone' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $user = UserModel::where('phone',$phone)->first();
        if ($user){
            Logger::notice('login|user_has_exist|user:' . json_encode($user));
            throw new OperateFailedException('抱歉，您的手机号已被注册');
        }
        SmsService::getCode($phone);
        return ApiResponse::responseSuccess();
    }

    /**
     * 短信验证码验证
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     * @throws \src\Exceptions\ResourceNotFoundException
     */
    public function verifySmsCode(){
        $validator = Validator::make($req = Request::all(),[
            'phone' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $phone = $req['phone'];
        $frontCode = $req['code'];
        SmsService::verifyCode($phone,$frontCode);
        return ApiResponse::responseSuccess();
    }

    /**
     * 杭电CAS登录
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     * @throws ParamValidateFailedException
     */
    public function casLogin(){
        $validator = Validator::make($params = Request::all(),[
            'phone' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException();
        }
        $phone = $params['phone'];
        //判断是否已经登录，如果ticket为空，则未登录
        if (!empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try {//认证ticket
                $validateurl = self::VALIDATE_SERVER . "?ticket=" . $_REQUEST["ticket"] . "&service=" . self::THIS_URL;

                $validateResult = file_get_contents($validateurl);

                //节点替换，去除sso:，否则解析的时候有问题
                $validateResult = preg_replace("/sso:/", "", $validateResult);

                $validateXML = simplexml_load_string($validateResult);

                $nodeArr = json_decode(json_encode($validateXML),true);

                if (empty($nodeArr['authenticationSuccess'])){//登录失败
                    Logger::notice('login|get_user_info_from_hdu_api_failed|msg:' . json_encode($validateXML));
                    die('登录失败，杭电官方系统异常，请稍后重试');
                }

                $attributes = $nodeArr['authenticationSuccess']['attributes']['attribute'];

                $data = [];

                foreach ($attributes as $attribute){
                    switch ($attribute['@attributes']['name']){
                        case 'user_name'://姓名
                            $data['name'] = $attribute['@attributes']['value'];
                            break;
                        case 'id_type'://用户类型 1-本科生 2-研究生 其他-教师
                            $data['idType'] = $attribute['@attributes']['value'];
                            break;
                        case 'userName'://学号/工号
                            $data['uid'] = $attribute['@attributes']['value'];
                            break;
                        case 'user_sex'://性别 1-男 其他-女
                            $data['sex'] = $attribute['@attributes']['value'];
                            break;
                        case 'unit_name'://学院
                            $data['unit'] = $attribute['@attributes']['value'];
                            break;
                        case 'classid'://班级号
                            $data['class'] = $attribute['@attributes']['value'];
                            break;
                    }
                }

                $data['phone'] = $phone;

                $res = $this->getLatestUser($data);

                $token = $this->setToken($res);

                return ApiResponse::responseSuccess(['token' => $token]);

            }
            catch (\Exception $e) {
                Logger::notice('login|get_user_info_from_hdu_api_failed|msg:' . json_encode($e->getMessage()));
                die('杭电官方系统异常，请稍后再试');
            }
        } else//没有ticket，说明没有登录，需要重定向到登录服务器
        {
            return redirect(self::LOGIN_SERVER . "?service=" .self::THIS_URL);
        }
    }

    /**
     * 设置token
     * @param $data
     * @return mixed
     */
    private function setToken($data){
        $token = JWT::encode($data,ComConf::JWT_KEY);
        Redis::set($data['uid'],$token);
        Redis::expire($data['uid'],2678400);
        return $token;
    }

    /**
     * 不存在则创建，存在则更新，返回最新的用户模型
     * @param $data
     * @return mixed
     */
    public function getLatestUser($data){
        $user = UserModel::where('uid',$data['uid'])->first();
        if (!$user){
            UserModel::create($data);
        } else{
            $user->update($data);
            $user = UserModel::where('uid',$data['uid'])->first();
        }
        return $user;
    }
}

