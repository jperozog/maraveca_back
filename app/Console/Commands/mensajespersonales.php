<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers;

class mensajespersonales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Mensajes:personales
    { id : cliente para notificar }
    ';

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
      //selecciono todos los clientes
        $clientes = DB::table('clientes')
        ->where('id', $this->argument('id'))
        ->get();

        foreach ($clientes as $cliente) {
          // busco las facturas
          $monto=0;
          $pagado=0;
          $cli=[];
          $facturas = DB::select(
          "SELECT fac_controls.*,
          (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
          ROUND((SELECT SUM(fac_products.precio_articulo) from  fac_products where fac_controls.id = fac_products.codigo_factura), 2) as monto,
          ROUND((SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id), 2) as pagado
          from fac_controls
          WHERE fac_controls.id_cliente = $cliente->id
          and fac_controls.fac_status = 1 ORDER BY created_at DESC;");
            foreach ($facturas as $factura) {
              if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
                if ($factura->denominacion == 'BSF'){
                  $monto=$monto+($factura->monto/100000);
                  $pagado=$pagado+($factura->pagado/100000);
                }elseif ($factura->denominacion == 'Bs.S') {
                  $monto=+$monto+$factura->monto;
                  $pagado=+$pagado+$factura->pagado;
                }
              }else{
                if($factura->denominacion == 'BSF'){
                  $monto=+$monto+(+$factura->monto/100000);
                }elseif ($factura->denominacion == 'Bs.S') {
                  $monto=+$monto+$factura->monto;
                }
              }

            }

            $servicios=DB::table('servicios')
            ->where('servicios.cliente_srv','=',$cliente->id)
            ->where('servicios.stat_srv','!=','4')
            //->where('servicios.stat_srv','!=','5')
            //->where('servicios.stat_srv','!=','3')
            ->get();
            $cliente->monto = $monto;
            $cliente->pagado = $pagado;
          $cliente->deuda = $monto-$pagado;
          $cliente->servicios = $servicios->count();
        }
        foreach ($clientes as $cliente) {
          if ($cliente->deuda>0 && $cliente->servicios > 0){
            array_push($cli, $cliente);
          }
        }
        foreach ($cli as $moroso) {
          $txt="actualmente presenta un saldo deudor de: ".number_format($moroso->deuda, 2)." Bs.S. Le invitamos a realizar el pago y a reportarlo a traves de: info@maraveca.com, http://maraveca.com/mi-ventana";//modificacion de msj 07/06/2019
            if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
              $message= "MARAVECA: Srs. ".ucwords(strtolower($moroso->social)).", ".$txt;
            }else {
              $message= "MARAVECA: Sr.(a) ".ucwords(strtolower($moroso->apellido)).", ".$txt;
            }
            //echo $message;
          //sendsms($moroso->phone1, $message);
          //  echo $message.$moroso->monto,"/".$moroso->pagado."\n";
          $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr); //cambio de url 07/06/2019

          if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
          }
          else {
            fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
            fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($moroso->phone1)." \"".urlencode($message)."\" ".rand()." \r\n\r\n"); //cambio de puerto de 2 a 3 07/06/2019
            //while (!feof($fp)) 	echo fgets($fp, 1024);
            echo fread($fp, 4096);
            fclose($fp);
          }
        }
    }
}
