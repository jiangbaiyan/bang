<?php

namespace App;

use App\Model\OrderModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use src\Exceptions\ResourceNotFoundException;
use src\Logger\Logger;

class UserModel extends Model
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = ['id'];


    /**
     * 根据id获取用户模型
     * @param $id
     * @return
     * @throws ResourceNotFoundException
     */
    public static function getUserById($id){
        if (empty($id)){
            return [];
        }
        $user = UserModel::find($id);
        if (!$user){
            Logger::notice('userMdl|user_not_exists|userId:' . $id);
            throw new ResourceNotFoundException();
        }
        return $user;
    }
}
