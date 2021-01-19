<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOinstallpgosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oinstallpgos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('monto')->nullable();
            $table->string('referencia')->nullable();
            $table->string('banco')->nullable();
            $table->string('fecha')->nullable();
            $table->string('responsable')->nullable();
            $table->string('installer')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('oinstallpgos');
    }
}
