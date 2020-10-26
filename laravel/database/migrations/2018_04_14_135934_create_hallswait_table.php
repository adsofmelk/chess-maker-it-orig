<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHallswaitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halls_wait', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hall')->default('default')->nullable();
            $table->integer('user')->unsigned();
            $table->string('channel')->nullable();
            $table->integer('level')->unsigned()->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('halls_wait');
    }
}
