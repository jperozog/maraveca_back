<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactibilidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factibilidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_pot', 190)->nullable();
            $table->string('coordenadaslat')->nullable();
            $table->string('coordenadaslon')->nullable();
            $table->string('equipo')->nullable();
            $table->string('celda')->nullable();
            $table->string('factible')->nullable();
            $table->string('status')->nullable();
            $table->text('comentario')->nullable();
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
        Schema::dropIfExists('factibilidades');
    }
}
