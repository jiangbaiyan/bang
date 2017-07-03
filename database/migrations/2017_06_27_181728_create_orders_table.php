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
        Schema::create('orders',function (Blueprint $table){
            $table->increments('id');
            $table->string('title',20)->default('');
            $table->tinyInteger('category')->comment('0-跑腿,1-悬赏提问,2-学习辅导,3-技术服务,4-生活服务,5-其他')->default(0);
            $table->tinyInteger('state')->cpmment('0-等待接单,1-正在服务,2-服务完成');
            $table->text('content');
            $table->dateTime('close_time');
            $table->decimal('money');
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
