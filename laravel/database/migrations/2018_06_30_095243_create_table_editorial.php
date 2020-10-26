<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEditorial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('editorial', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->text('texto')->nullable();
            $table->text('default')->nullable();
            $table->text('reset')->nullable();
            $table->integer('orden')->default(1)->nullable();
            $table->smallInteger('activo')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('editorial');
    }
}
