<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceClienteInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_clientes_ins', function (Blueprint $table) {
                $table->increments('id_bal_in');
                $table->string('bal_cli_in');
                $table->string('bal_tip_in');
                $table->string('bal_monto_in');
                $table->string('bal_rest_in');
                $table->string('bal_comment_in')->nullable();
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
        Schema::dropIfExists('balance_cliente_ins');
    }
}
