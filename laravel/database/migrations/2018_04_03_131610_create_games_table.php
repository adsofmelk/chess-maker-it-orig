<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user1')->nullable();
            $table->integer('user2')->nullable();
            $table->string('status', 3)->nullable();
            $table->string('channel_ably')->nullable();
            $table->string('moves_pieces')->nullable();
            $table->string('distribution')->nullable();
            $table->integer('who_begin')->nullable();
            $table->integer('who_move_now')->nullable();
            $table->integer('winner')->nullable();
            $table->integer('loser')->nullable();
            $table->integer('score_winner')->nullable();
            $table->integer('score_loser')->nullable();
            $table->string('comments',255)->nullable();
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
        Schema::dropIfExists('partidas');
    }
}
