<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('admin')->default(0);
            $table->string('id_profile')->nullable();
            $table->string('type_auth')->nullable()->default('local');
            $table->string('status')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->integer('rating')->default(8000);
            $table->integer('game_win')->default(0);
            $table->integer('game_lose')->default(0);
            $table->integer('game_empates')->default(0);
            $table->integer('game_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
