<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCabezotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabezotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('orden')->default(1);
            $table->string('titulo', 255)->nullable();
            $table->string('resumen', 255)->nullable();
            $table->string('texto_boton')->nullable();
            $table->string('enlace_boton')->nullable();
            $table->string('foto', 255)->nullable();
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
        Schema::dropIfExists('cabezotes');
    }
}
