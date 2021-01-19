<?php

namespace App\Console\Commands;
use DB;
use App\servicios;
use App\clientes;
use Illuminate\Console\Command;

use App\Helpers;
use App\historico_cliente;
use App\cola_de_ejecucion;

class clientes_servicios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mensaje que se enviara a aquellas personas que van a corte';

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
        $clientes = clientes::where('id', '=', '1400');
        $servicios = DB::table('servicios')//buscamos los servicios
        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            ->where('servicios.cliente_srv', '=', '1400')
            ->where('servicios.stat_srv', '!=', '4')
            ->get();
        $serviciosb = DB::table('servicios')//buscamos los servicios
        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            ->where('servicios.cliente_srv', '=', '1400')
            ->where('servicios.stat_srv', '!=', '4')
            ->where('servicios.tipo_plan_srv', '=', '1')
            ->get();
        $serviciosd = DB::table('servicios')//buscamos los servicios
        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            ->where('servicios.cliente_srv', '=', '1400')
            ->where('servicios.stat_srv', '!=', '4')
            ->where('servicios.tipo_plan_srv', '=', '3')
            ->get();
        foreach ($servicios as $servicio) {


            if (  count($serviciosd) <1 ) {
                $clientes->update(['tipo_planes' => 1]);

            } elseif (count($serviciosb)<1  ) {
                $clientes->update(['tipo_planes' => 2]);
            } elseif (count($servicios) >1  ) {
                $clientes->update(['tipo_planes' => 3]);

            }


        }
    }
}
