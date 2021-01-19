<?php

namespace App\Console\Commands;
use DB;
use App\servicios;
use App\historico;
use App\historico_cliente;
use App\cola_de_ejecucion;
use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
class Suspender extends command
{
  /**
  * The name and signature of the console command.
  *
  * @var string
  */
  protected $signature = 'Suspender';

  /**
  * The console command description.
  *
  * @var string
  */
  protected $description = 'Revisa Los Clientes y suspende o activa conforme facturacion';

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

        //$sms = new Helpers;
        $clientes=DB::table('clientes')
        ->where('clientes.id', '=', '1373')
        ->orderBy('nombre', 'ASC')->get();
        foreach ($clientes as $cliente) { //para cada cliente
          $idcli = $cliente->id;
          $servicios=DB::table('servicios')  //buscamos los servicios
          ->select('clientes.nombre', 'clientes.apellido', 'clientes.phone1', 'clientes.social', 'clientes.kind','clientes.tipo_planes', 'servidores.*', 'servicios.ip_srv', 'servicios.id_srv', 'servicios.stat_srv', 'servicios.cliente_srv', 'servicios.serie_srv', 'servicios.tipo_plan_srv' )
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->join('aps','aps.id','=','servicios.ap_srv')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->join('celdas','aps.celda_ap','=','celdas.id_celda')
          ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
          ->where('servicios.cliente_srv','=',$cliente->id)
          //->where('servicios.stat_srv','!=','3')
          ->where('servicios.stat_srv','!=','4')
          ->where('servicios.stat_srv','!=','5')
          //->where('servicios.ip_srv', '192.168.0.1')
          //->groupBy('servicios.ip_srv')
          ->get();
          $retirados=DB::table('servicios')  //buscamos los servicios
          ->select('clientes.*', 'servidores.*', 'servicios.*')
          ->join('aps','aps.id','=','servicios.ap_srv')
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->join('celdas','aps.celda_ap','=','celdas.id_celda')
          ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
          ->where('servicios.cliente_srv','=',$cliente->id)
          //->where('servicios.stat_srv','!=','3')
          ->where('servicios.stat_srv','=','4')
          //->where('servicios.ip_srv', '192.168.0.1')
          ->groupBy('servicios.ip_srv')
          ->get();

          if ($servicios->count()  >= 1) { //en caso de que exista al menos un servicio
            $debe=0; //variable para contador
            $monto=0; //variable para contador
            $pagado=0; //variable para contador
            $facturas=DB::select(
              "SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
              from fac_controls where fac_controls.id_cliente = ".$cliente->id." and fac_status = 1");//selecciono todas las facturas
              foreach ($facturas as $factura) {
                if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
                  if ($factura->denominacion == 'BSF'){
                    if($factura->fac_status==1){
                    $monto=$monto+($factura->monto/100000);
                    $pagado=$pagado+($factura->pagado/100000);
                  }
                  }elseif ($factura->denominacion == 'Bs.S' ||$factura->denominacion == '$') {
                    if($factura->fac_status==1){
                    $monto=$monto+$factura->monto;
                    $pagado=$pagado+$factura->pagado;
                  }
                  }
                }else{
                  if($factura->denominacion == 'BSF'){
                    if($factura->fac_status==1){
                    $monto=$monto+($factura->monto/100000);
                  }
                  }elseif ($factura->denominacion == 'Bs.S'||$factura->denominacion == '$') {
                    if($factura->fac_status==1){
                    $monto=$monto+$factura->monto;
                  }
                  }
                }
                if($factura->monto > $factura->pagado){ //reviso cuales estan pagadas
                  $debe+=1;
                  $cliente->deuda = $monto-$pagado;
                }
              }
              if ($debe >= 1) { //si debe 1 o mas, suspendera sus servicios
                foreach ($servicios as $moroso) {
                    //echo "1\n";
                    if ($moroso->tipo_planes == 2 ||$moroso->tipo_planes == 5 ) {
                        $txt = "le informamos que su servicio ha sido suspendido, actualmente presenta una mora de " . number_format($cliente->deuda, 2) ." US$. por favor reporte su pago a través de: info@maraveca.com, maraveca.com/mi-ventana";
                    } else{
                        $txt = "le informamos que su servicio ha sido suspendido, actualmente presenta una mora de " . number_format($cliente->deuda, 2) ." Bs.S. por favor reporte su pago a través de: info@maraveca.com, maraveca.com/mi-ventana";
                }
                  if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                    $cliente1= ucwords(strtolower($moroso->social));
                    $message= "MARAVECA: Srs. ".$cliente1.", ".$txt;
                  }else {
                    $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    $message= "MARAVECA: Sr.(a) ".$cliente1.", ".$txt;
                  }
                  cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'s', 'contador'=>'1']);
                  echo "moroso: ".$cliente1."/".$moroso->ip_srv."\n";
              $API = new RouterosAPI();

                  if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                    //  echo $API;

                    $API->write('/ip/firewall/address-list/print',false);
                    $API->write('?list=MOROSOS',false);
                    $API->write('?disabled=false',false);
                    $API->write('?address='.$moroso->ip_srv,true);
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);
                   // echo $API;
                   // echo $READ;
                  //    echo $ARRAY;
                    if(count($ARRAY)==0){
                      $API->write('/ip/firewall/address-list/add',false);
                      $API->write('=list=MOROSOS',false);
                      $API->write('=address='.$moroso->ip_srv,false);
                      $API->write('=comment='.$cliente1,true);
                      $READ = $API->read(true);
                     // return $READ;
                      // $numero=urlencode($moroso->phone1);
                      // $mensaje=urlencode($message);
                      // $fp = stream_socket_client("tcp://200.209.74.251:5038", $errno, $errstr);

                      // if (!$fp) {
                      //   echo "ERROR: $errno - $errstr<br />\n";
                      // }
                      // else {
                      //   fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                      //   fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 2 ".$numero." \"".$mensaje."\" ".rand()." \r\n\r\n");
                      //   //while (!feof($fp)) 	echo fgets($fp, 1024);
                      //   //echo fread($fp, 4096);
                      //   fclose($fp);
                      //
                      // }
                    }
                    $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                    $servicios1->update(["stat_srv"=>3]);
                    historico_cliente::create(['history'=>'Suspension automatica por deber 1 factura o mas', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 's')->delete();
                  }
                  $API->disconnect();
                }
                }else{
                  //echo "2\n";
                  foreach ($servicios as $moroso) {
                    if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                      $cliente1= ucwords(strtolower($moroso->social));
                    }else {
                      $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    }
                    cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'a', 'contador'=>'1']);
                   $API = new RouterosAPI();
                    if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                      $API->write('/ip/firewall/address-list/print',false);
                      $API->write('?list=MOROSOS',false);
                      $API->write('?disabled=false',false);
                      $API->write('?address='.$moroso->ip_srv,true);
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);
                      if(count($ARRAY)>0){
                        $API->write('/ip/firewall/address-list/remove', false);
                        $API->write('=.id=' . $ARRAY[0]['.id']);
                        $READ = $API->read(false);
                        //return $READ;
                      }
                      $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                      $servicios1->update(["stat_srv"=>1]);
                      cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'a')->delete();
                    }
                    $API->disconnect();
                    echo "solvente: ".$cliente1."/".$moroso->ip_srv."\n";
                  }
                }
              }
              if ($retirados->count()  >= 1){
                foreach ($retirados as $moroso){
                  //echo "3\n";
                  if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                    $cliente1= ucwords(strtolower($moroso->social));
                  }else {
                    $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                  }
                  cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'r', 'contador'=>'1']);
                  $API = new RouterosAPI();
                  if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                    $API->write('/ip/firewall/address-list/print',false);
                    $API->write('?list=MOROSOS',false);
                    $API->write('?disabled=false',false);
                    $API->write('?address='.$moroso->ip_srv,true);
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);
                    if(count($ARRAY)==0){
                      $API->write('/ip/firewall/address-list/add',false);
                      $API->write('=list=MOROSOS',false);
                      $API->write('=address='.$moroso->ip_srv,false);
                      $API->write('=comment='."RETIRADO:".$cliente1,true);
                      $READ = $API->read(true);
                    }
                    $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                    $servicios1->update(["stat_srv"=>4]);
                    historico_cliente::create(['history'=>'Suspension automatica marcado como retirado', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'r')->delete();
                    echo "retirado: ".$cliente1."/".$moroso->ip_srv."\n";
                  }

              }
            }
          }

    }
}
