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
            $table->string('title')->nullable()->comment('订单标题');
            $table->text('content')->nullable()->comment('订单内容');
            $table->dateTime('begin_time')->nullable()->comment('开始时间');
            $table->dateTime('end_time')->nullable()->comment('结束时间');
            $table->tinyInteger('type')->nullable()->comment('订单类别：0-跑腿 1-悬赏提问 2-学习服务 3-技术服务 4-生活服务 5-其他');
            $table->tinyInteger('status')->default(0)->nullable()->comment('0-未发布 1-已发布 2-正在进行 3-服务完成 4-评价完成');
            $table->double('price')->nullable()->comment('酬金');
            $table->integer('sender_id')->nullable()->comment('发单者id');
            $table->integer('receiver_id')->nullable()->comment('接单者id');
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
