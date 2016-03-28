<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyVoteRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_vote_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('vid');
            $table->integer('vnodeid');
            $table->float('score');
            $table->string('userid');
            $table->date('ym');
            $table->string('vuid');
            $table->string('name');
            $table->tinyInteger('type');
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
        Schema::drop('qy_vote_records');
    }
}
