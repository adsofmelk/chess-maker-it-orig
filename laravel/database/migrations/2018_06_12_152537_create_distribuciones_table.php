<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistribucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribuciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hall')->nullable()->default(1);
            $table->integer('level')->nullable()->default(1);
            $table->string('status', 3)->nullable()->default(1);
            $table->string('distribution')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribuciones');
    }
}
