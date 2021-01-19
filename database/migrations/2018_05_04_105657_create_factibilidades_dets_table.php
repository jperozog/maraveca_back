<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactibilidadesDetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factibilidades_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_fac', 190)->nullable();
            $table->string('nombre')->nullable();
            $table->string('valor')->nullable();
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
        Schema::dropIfExists('factibilidades_dets');
    }
}
