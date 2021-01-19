<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->increments('id_srv');
            $table->integer('cliente_srv');
            $table->date('instalacion_srv');
            $table->date('recibo_srv')->nullable();
            $table->string('costo_instalacion_srv')->default('')->nullable();
            $table->integer('credito_srv')->default(0)->nullable();
            $table->date('start_srv');
            $table->string('notify_srv')->default('');
            $table->integer('equipo_srv')->nullable();
            $table->integer('signal_srv')->nullable();
            $table->string('ip_srv')->default('');
            $table->string('mac_srv')->default('');
            $table->string('serial_srv')->default('');
            //$table->integer('servidor_srv')->nullable();
            //$table->integer('celda_srv');
            $table->integer('ap_srv');
            $table->integer('zona_srv')->nullable();
            $table->integer('plan_srv');
            $table->integer('stat_srv')->default(3);
            $table->text('comment_srv')->nullable();
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
        Schema::dropIfExists('servicios');
    }
}
