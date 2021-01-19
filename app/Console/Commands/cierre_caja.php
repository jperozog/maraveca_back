<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class cierre_caja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cierre_caja';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $traerUsuariosAdministrativos = DB::select("SELECT * FROM registro_pagos AS r
                                                        INNER JOIN users AS u ON r.responsable = u.id_user
                                                         WHERE estatus_registro = 1 GROUP BY r.responsable");

        foreach ($traerUsuariosAdministrativos as $usuario) {
          
           $registros = DB::select("SELECT * FROM registro_pagos WHERE estatus_registro = 1 AND responsable = ?",[$usuario->id_user]);
            
           if(count($registros) > 0){
                $id_cierre = rand();
                foreach ($registros as $re) {
                 
                    $id_re = $re->id_registro;
                    
                    $result = DB::update('UPDATE registro_pagos SET estatus_registro = 2 WHERE id_registro = ?',[$id_re]);

                    $result2 = DB::update('INSERT INTO cierre_caja (id_cierre,pago,estatus) VALUES (?,?,?)',[$id_cierre,$id_re,1]);
                    
                }
           }
           
        }                                                 
    }
}
