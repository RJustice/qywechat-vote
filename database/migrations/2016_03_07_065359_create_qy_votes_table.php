<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->tinyInteger('type');
            $table->string('title');
            $table->text('info');
            $table->integer('starttime');
            $table->integer('endtime');
            $table->integer('ym');
            $table->tinyInteger('status');
            $table->text('extra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qy_votes');
    }
}
