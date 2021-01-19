<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class activarSuspendidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ActivarSuspendidos';

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
      //selecciono todos los clientes
        $clientes = DB::table('clientes')
        ->get();

        foreach ($clientes as $cliente) {
          // busco las facturas
          $monto=0;
          $pagado=0;
          $cli=[];
          $facturas = DB::select(
          "SELECT fac_controls.*,
          (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
          ROUND((SELECT SUM(fac_products.precio_articulo) from  fac_products where fac_controls.id = fac_products.codigo_factura)) as monto,
          ROUND((SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id)) as pagado
          from fac_controls
          WHERE fac_controls.id_cliente = $cliente->id
          ORDER BY created_at DESC;");
            foreach ($facturas as $factura) {
              if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
                $monto=$monto+$factura->monto;
                $pagado=$pagado+$factura->pagado;
              }else{
                $monto=$monto+$factura->monto;
              }
            }
            $servicios=DB::table('servicios')
            ->where('servicios.cliente_srv','=',$cliente->id)
            ->where('servicios.stat_srv','=','3')
            ->get();
          $cliente->deuda = $monto-$pagado;
          $cliente->servicios = $servicios->count();
        }
        foreach ($clientes as $cliente) {
          if ($cliente->deuda>0 && $cliente->servicios > 0){
            array_push($cli, $cliente);
          }
        }
        foreach ($cli as $solvente) {
          
        }
    }
}
