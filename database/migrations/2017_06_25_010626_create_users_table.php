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
        Schema::create('users',function (Blueprint $table){
            $table->increments('id');
            $table->string('phone',11);
            $table->string('password',16);
            $table->string('name',20);
            $table->string('id_number',20);
            $table->string('sex',1)->default('n')->commit('m-男 f-女 n-未知');
            $table->integer('credit')->default(0);
            $table->unsignedTinyInteger('level')->default(0);
            $table->integer('experience')->default(0);
            $table->text('signature')->nullable();
            $table->string('authorization',20)->default('');
            $table->unique('phone');
            $table->index('name');
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
