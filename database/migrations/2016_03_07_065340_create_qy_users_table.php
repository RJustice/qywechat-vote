<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('userid');
            $table->string('name');
            $table->string('department');
            $table->string('position');
            $table->string('mobile');
            $table->tinyInteger('gender');
            $table->string('email');
            $table->string('weixinid');
            $table->string('openid');
            $table->string('avatar');
            $table->tinyInteger('status');
            $table->string('extattr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qy_users');
    }
}
