<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresupuestosTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('presupuestos', function (Blueprint $table) {
      $table->increments('id');
      $table->string('cliente');
      $table->string('tipo');
      $table->string('planes');
      $table->string('instalacion');
      $table->string('moneda');
      $table->string('responsable');
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
    Schema::dropIfExists('presupuestos');
  }
}
