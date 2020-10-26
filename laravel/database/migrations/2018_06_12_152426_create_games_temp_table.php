<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_temp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user1')->nullable();
            $table->integer('user2')->nullable();
            $table->string('status', 3)->nullable();
            $table->string('channel')->nullable();
            $table->string('distribution')->nullable();
            $table->integer('user_begin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_temp');
    }
}
