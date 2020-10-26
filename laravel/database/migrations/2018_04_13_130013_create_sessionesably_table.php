<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionesablyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessiones_ably', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_ably')->nullable();
            $table->string('status')->nullable()->default('initial');
            $table->integer('user_id');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessiones_ably');
    }
}
