<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQyGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qy_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('group_id');
            $table->string('name');
            $table->integer('pid');
            $table->integer('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qy_groups');
    }
}
