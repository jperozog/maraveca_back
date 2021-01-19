<?php

use Illuminate\Database\Seeder;
use App\celdas;

class CeldasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        clientes::create(array(
            'nombre' => 'barijo',
            'ip' => '193124328734',
            'user' => 'admin',
            'password' => 'pass',
        ));
    }
}
