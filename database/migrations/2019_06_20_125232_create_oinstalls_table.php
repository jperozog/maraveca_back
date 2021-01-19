<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateoinstallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oinstalls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket');
            $table->string('installer');
            $table->string('status_pgo');
            $table->string('comment_tr');
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
        Schema::dropIfExists('oinstall');
    }
}
