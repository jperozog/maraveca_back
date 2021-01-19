<?php

namespace App\Console\Commands;

use App\corte_prog;
use App\historico;
use App\historico_cliente;
use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
use App\cola_de_ejecucion;
use App\servicios;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class cortes_prog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cortes_prog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza los cortes porgramados por compromisos de pagos';

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




                $servicios=corte_prog::select('corte_progs.*', 'clientes.*', 'servidores.*', 'servicios.*', 'planes.*', 'corte_progs.id as id_prog')
                    ->join('servicios', 'servicios.id_srv', '=', 'corte_progs.id_servicio')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                    ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                    ->where('corte_progs.status','=', '1')
                    ->get();



                foreach ($servicios as $moroso) {
                    $fecha_prog = $moroso->fecha;
                    $today = \Carbon\Carbon::now();
                    $fecha = $today->toDateString();


                    if($fecha_prog <= $fecha )
                    {
                        echo $fecha. "\n";
                        echo $fecha_prog. "\n";
                        corte_prog::where('id', $moroso->id_prog)->update(['contador'=>($moroso->contador+1)]);
                    cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'s', 'contador'=>'1']);
                        /*===================================================== para suspender cliente===========================================================*/


                        if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                            $cliente1= ucwords(strtolower($moroso->social));
                            $remp_cliente= array('ñ', 'Ñ');
                            $correct_cliente= array('n', 'N');
                            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                        }else {
                            $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                            $remp_cliente= array('ñ', 'Ñ');
                            $correct_cliente= array('n', 'N');
                            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                        }
                        $API = new RouterosAPI();
                        if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                            $API->write('/ip/firewall/address-list/print',false);
                            $API->write('?list=ACTIVOS',false);
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
                            $API->write("/queue/simple/getall",false);
                            $API->write('?name='.$cliente."(".$moroso->ip_srv.")",true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY)>0) {
                                $API->write("/queue/simple/remove", false);
                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);

                            }

                            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 's')->delete();
                            historico_cliente::create(['history'=>'Suspension automatica por no cumplir compromiso de pago', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);

                            $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                            $servicios1->update(["stat_srv"=>3]);

                        }
                        $API->disconnect();
                        corte_prog::where('id', $moroso->id_prog)->update(['status'=>2]);
                        /*===================================================== Mensaje al cliente===========================================================*/
                    $debe=0; //variable para contador
                    $monto=0; //variable para contador
                    $pagado=0; //variable para contador
                    $facturas=DB::select(
                        "SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
              from fac_controls where fac_controls.id_cliente = ".$moroso->cliente_srv." and fac_status = 1");//selecciono todas las facturas
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
                            $moroso->deuda = $monto-$pagado;
                        }
                    }
                    if ($moroso->tipo_planes == 2 ||$moroso->tipo_planes == 5 ) {
                        $txt = "le informamos que su servicio fue suspendido por no cumplir con el compromiso de pago acordado, presenta una deuda pendiente de " . number_format($moroso->deuda, 2) ." US$. por favor reporte su pago a través de: info@maraveca.com, maraveca.com/mi-ventana";
                    } else{
                        $txt = "le informamos que su servicio fue suspendido por no cumplir con el compromiso de pago acordado, presenta una deuda pendiente de ". number_format($moroso->deuda, 2) ." Bs.S. por favor reporte su pago a través de: info@maraveca.com, maraveca.com/mi-ventana";
                    }
                    if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                        $cliente1= ucwords(strtolower($moroso->social));
                        $message= "MARAVECA: Srs. ".$cliente1.", ".$txt;
                    }else {
                        $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                        $message= "MARAVECA: Sr.(a) ".$cliente1.", ".$txt;
                    }

                    //echo $message;
                    //sendsms($moroso->phone1, $message);
                    //  echo $message.$moroso->monto,"/".$moroso->pagado."\n";
               $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);

                    if (!$fp) {
                        echo "ERROR: $errno - $errstr<br />\n";
                    }
                    else {
                        fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                        fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($moroso->phone1)." \"".urlencode($message)."\" ".rand()." \r\n\r\n");
                        //while (!feof($fp)) 	echo fgets($fp, 1024);
                        echo fread($fp, 4096);
                        fclose($fp);
                    }
                    echo $moroso->phone1." \ ".$message."\n\n\n";
                }
            }
        }

}
