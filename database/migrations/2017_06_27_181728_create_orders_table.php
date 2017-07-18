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
            $table->tinyInteger('category')->comment('1-跑腿,2-悬赏提问,3-学习辅导,4-技术服务,5-生活服务,6-其他');
            $table->tinyInteger('state')->cpmment('1-等待接单,2-正在服务,3-服务完成,4-评价完成')->default(1);
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
