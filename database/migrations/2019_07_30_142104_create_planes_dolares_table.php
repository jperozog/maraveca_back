<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanesDolaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {Schema::create('planes_dolares', function (Blueprint $table) {
        $table->increments('id_plan');
        $table->string('name_plan')->default('');
        $table->string('cost_plan')->default('');
        $table->string('dmb_plan')->default('');
        $table->string('umb_plan')->default('');
        $table->string('carac_plan')->default('');
        $table->string('descripcion')->default('');
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
        Schema::dropIfExists('planes_dolares');
    }
}
