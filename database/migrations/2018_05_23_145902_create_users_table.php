<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid',32)->default('')->comment('微信openid');
            $table->string('alipay_account',20)->default('')->comment('支付宝账户');
            $table->string('phone',11)->default('')->comment('手机号');
            $table->string('password')->default('')->comment('密码');
            $table->string('name',20)->default('')->comment('姓名');
            $table->unsignedTinyInteger('age')->default('0')->comment('年龄');
            $table->string('sex',2)->default('value')->comment('性别');
            $table->string('uid',15)->default('')->comment('学号');
            $table->string('school',50)->default('')->comment('学校');
            $table->string('unit',50)->default('')->comment('学院');
            $table->unsignedsmallInteger('grade',20)->default('0')->comment('年级');
            $table->unsignedInteger('point')->default('0')->comment('积分');
            $table->string('province',50)->default('')->comment('省份');
            $table->string('city',50)->default('')->comment('城市');
            $table->string('avatar')->default('')->comment('头像url');
            $table->unique('openid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
