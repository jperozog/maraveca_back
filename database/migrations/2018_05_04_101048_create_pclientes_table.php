<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePclientesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::create('Pclientes', function (Blueprint $table) {
          $table->increments('id');
          $table->string('kind')->nullable();
          $table->string('id_cli')->nullable();
          $table->string('dni', 190)->nullable()->unique();
          $table->string('email')->nullable();
          $table->string('nombre')->nullable();
          $table->string('apellido')->nullable();
          $table->string('direccion')->nullable();
          $table->date('day_of_birth')->nullable();
          $table->string('serie')->nullable();
          $table->string('phone1')->nullable();
          $table->string('phone2')->nullable();
          $table->string('social')->nullable();
          $table->text('comment')->nullable();
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
      Schema::dropIfExists('clientes');
  }
}
