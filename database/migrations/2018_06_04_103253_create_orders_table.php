<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->default('')->comment('唯一订单号');
            $table->string('title')->default('')->comment('订单标题');
            $table->string('content',1024)->default('')->comment('订单内容');
            $table->dateTime('begin_time')->default('1971-01-01 00:00:00')->comment('开始时间');
            $table->dateTime('end_time')->default('1971-01-01 00:00:00')->comment('结束时间');
            $table->unsignedTinyInteger('type')->default('0')->comment('订单类别：0-跑腿 1-悬赏提问 2-学习服务 3-技术服务 4-生活服务 5-其他');
            $table->unsignedTinyInteger('status')->default('0')->comment('0-未发布 1-已发布 2-正在进行 3-服务完成 4-评价完成 5-订单取消');
            $table->unsignedDecimal('price',8,2)->default('0')->comment('酬金');
            $table->unsignedDecimal('longitude',9,6)->default('0')->comment('经度');
            $table->unsignedDecimal('latitude',9,6)->default('0')->comment('纬度');
            $table->unsignedInteger('sender_id')->default('0')->comment('发单者id');
            $table->unsignedInteger('receiver_id')->default('0')->comment('接单者id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->unique('uuid');
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
