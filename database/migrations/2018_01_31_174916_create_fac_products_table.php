<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacProductsTable extends Migration
{
    /**
     * Run the migrations.
     * de los productos voy a utilizar todos los
     * datos disponibles para tener historial de
     * facturacion sin alterar los montos
     * @return void
     */
    public function up()
    {
        Schema::create('fac_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo_factura');
            $table->string('codigo_articulo');
            $table->string('nombre_articulo');
            $table->string('precio_articulo');
            $table->string('comment_articulo')->nullable();
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
        Schema::dropIfExists('fac_products');
    }
}
