<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCeldasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('celdas', function (Blueprint $table) {
            $table->increments('id_celda');
            $table->string('nombre_celda')->default('');
            //$table->string('ip_celda',190)->unique()->default('');
            //$table->string('user_celda')->default('');
            //$table->string('password_celda')->default('');
            $table->integer('servidor_celda')->nullable();
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
        Schema::dropIfExists('celdas');
    }
}
