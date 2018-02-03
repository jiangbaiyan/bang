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
            $table->string('title',100)->default('');
            $table->text('content');
            $table->string('state',1)->default('1');
            $table->string('category',1)->default('');
            $table->dateTime('end_time');
            $table->string('image')->default('');
            $table->double('money')->default(0);
            $table->integer('applicant_id');
            $table->integer('servant_id');
            $table->index('applicant_id');
            $table->index('servant_id');
            $table->index('category');
            $table->index('state');
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
