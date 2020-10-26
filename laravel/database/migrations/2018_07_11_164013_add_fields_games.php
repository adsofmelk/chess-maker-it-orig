<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->integer('moves')->default(0)->nullable();
            $table->text('board_data')->nullable();
            $table->smallInteger('type')->default(2)
                ->comment('1=> user against machine, 2 => user against user, 3 => user without authenticate against machine')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn('moves');
            $table->dropColumn('board_data');
            $table->dropColumn('type');
        });
    }
}
