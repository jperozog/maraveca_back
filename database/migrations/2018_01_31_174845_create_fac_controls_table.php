<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_controls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fac_num', 90)->unique()->nullable();
            $table->string('denominacion');
            $table->string('id_cliente');
            $table->string('fac_status');
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
        Schema::dropIfExists('fac_controls');
    }
}
