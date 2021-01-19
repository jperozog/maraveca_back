<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soportes', function (Blueprint $table) {
            $table->increments('id_soporte');
            $table->integer('servicio_soporte')->nullable();
            $table->integer('status_soporte')->nullable();
            $table->integer('afectacion_soporte')->nullable();
            $table->integer('tipo_soporte')->nullable();
            $table->text('comment_soporte')->nullable();
            $table->integer('user_soporte')->nullable();
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
        Schema::dropIfExists('soportes');
    }
}
