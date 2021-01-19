<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Aps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('aps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_ap')->default('');
            $table->string('ip_ap',190)->unique()->default('');
            $table->string('user_ap')->default('');
            $table->string('password_ap')->default('');
            $table->integer('celda_ap')->nullable();
            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	 Schema::dropIfExists('aps');        //
    }
}
