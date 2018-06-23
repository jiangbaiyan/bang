<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/6/23/023
 * Time: 21:07
 */

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class UserController extends Controller{

    /**
     * 获取个人信息
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getUserInfo(){
        return ApiResponse::responseSuccess(UserModel::getCurUser());
    }

    /**
     * 更新个人信息
     * @param Request $request
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function modifyUserInfo(Request $request){
        if ($request->has('name') || $request->has('age') || $request->has('sex') || $request->has('province') || $request->has('city')){
            $user = UserModel::getCurUser();
            if (!$user->update($request->all())){
                throw new OperateFailedException();
            }
            return ApiResponse::responseSuccess();
        } else{
            throw new ParamValidateFailedException();
        }
    }
}