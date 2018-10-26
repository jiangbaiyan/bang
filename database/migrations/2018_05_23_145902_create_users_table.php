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
            $table->string('phone',11)->default('')->comment('手机号');
            $table->unsignedTinyInteger('sex')->default('0')->comment('性别 0-未知|1-男|2-女');
            $table->string('name',20)->default('')->comment('姓名');
            $table->string('uid',15)->default('')->comment('学号');
            $table->string('school',50)->default('')->comment('学校');
            $table->string('unit',50)->default('')->comment('学院');
            $table->unsignedsmallInteger('grade')->default('0')->comment('年级');
            $table->unsignedInteger('class')->default('0')->comment('班级');
            $table->integer('point')->default('0')->comment('积分');
            $table->string('avatar')->default('')->comment('头像url');
            $table->timestamps();
            $table->index('uid');
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
