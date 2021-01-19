<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_clientes', function (Blueprint $table) {
          $table->increments('id_bal');
          $table->string('bal_cli');
          $table->string('bal_tip');
          $table->string('bal_monto');
          $table->string('bal_rest');
          $table->string('bal_comment')->nullable();
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
        Schema::dropIfExists('balance_clientes');
    }
}
