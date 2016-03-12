<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyVoteRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_vote_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('userid');
            $table->integer('vid');
            $table->tinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qy_vote_roles');
    }
}
