<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyVoteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_vote_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('vid');
            $table->string('userid');
            $table->string('department')->index();
            $table->string('name');
            $table->string('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qy_vote_users');
    }
}
