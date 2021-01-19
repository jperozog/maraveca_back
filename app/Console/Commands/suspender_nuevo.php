<?php

namespace App\Console\Commands;
use App\clientes;
use App\soportes;
use DB;
use App\servicios;
use App\historico;
use App\historico_cliente;
use App\cola_de_ejecucion;
use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
use Illuminate\Http\Request;

class Suspender_nuevo extends command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Suspender_Nuevo';

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
            //->where('clientes.serie', '=', '0')
            ->orderBy('nombre', 'ASC')->get();
        foreach ($clientes as $cliente) { //para cada cliente
            $prog=DB::table ('corte_progs')
                ->where ('corte_progs.id_cliente', '=',$cliente->id)
                ->where(	'corte_progs.status', '=','1')
                ->get();
            if ($prog->count()<1){// si existe algun cliente con compromiso de pago no se le suspendera a traves de este proceso
                $idcli = $cliente->id;
                $servicios=DB::table('servicios')  //buscamos los servicios
                ->select('clientes.nombre', 'clientes.apellido', 'clientes.phone1', 'clientes.social', 'clientes.kind','clientes.tipo_planes', 'servidores.*', 'servicios.ip_srv', 'servicios.id_srv', 'servicios.stat_srv', 'servicios.cliente_srv', 'planes.*' )
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                    ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                    ->where('servicios.cliente_srv','=',$cliente->id)
                    // ->where('servicios.stat_srv','!=','3')
                    ->where('servicios.stat_srv','!=','4')
                    ->where('servicios.stat_srv','!=','5')


                    //  ->where('servidores.ip_srvidor','=','192.168.0.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware
                    //  ->where('servidores.ip_srvidor','=','172.16.28.1')
                    //->where('servicios.ip_srv', '192.168.0.1')
                    ->groupBy('servicios.ip_srv')
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

                    //  ->where('servidores.ip_srvidor','=','192.168.0.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware
                    //->where('servicios.ip_srv', '192.168.0.1')
                    ->groupBy('servicios.ip_srv')
                    ->get();
                $exonerados=DB::table('servicios')  //buscamos los servicios
                ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                    ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                    ->where('servicios.cliente_srv','=',$cliente->id)
                    //->where('servicios.stat_srv','!=','3')

                    ->where('servicios.stat_srv','=','5')


                    // ->where('servidores.ip_srvidor','=','192.168.0.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware
                    //->where('servicios.ip_srv', '192.168.0.1')
                    ->groupBy('servicios.ip_srv')
                    ->get();


                $pend_serv=DB::table('pendiente_servis')  //buscamos los servicios
                ->select('clientes.*', 'servidores.*', 'pendiente_servis.*')
                    ->join('clientes','clientes.id','=','pendiente_servis.cliente_pd')
                    ->join('planes','planes.id_plan','=','pendiente_servis.plan_pd')
                    ->join('celdas','celdas.id_celda','=','pendiente_servis.celda_pd')
                    ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                    ->where('pendiente_servis.cliente_pd','=',$cliente->id)
                    ->where('pendiente_servis.status_pd','=','2')

                    // ->where('servidores.ip_srvidor','=','192.168.12.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

                    ->groupBy('pendiente_servis.ip_pd')
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
                                $API->write("/queue/simple/getall",false);                // verificara si existe en la lista queue
                                $API->write('?name='.$cliente1."(".$moroso->id_srv.")",true);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);
                                if(count($ARRAY)>0) {
                                    $API->write("/queue/simple/remove", false);            // en caso de existir lo eliminara
                                    //  $API->write('=.name='.$cliente."(".$id_srv.")");
                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                    $READ = $API->read(false);
                                    $ARRAY = $API->parseResponse($READ);
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
                            if ($moroso->carac_plan == 1 ) {
                                $parent = "Asimetricos";
                            } else  {

                                $parent = "none";
                            }
                            $API = new RouterosAPI();

                            if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                                $API->write('/ip/firewall/address-list/print',false);
                                $API->write('?list=ACTIVOS',false);
                                $API->write('?disabled=false',false);
                                $API->write('?address='.$moroso->ip_srv,true);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);

                                if(count($ARRAY)==0){

                                    $API->write('/ip/firewall/address-list/add',false);
                                    $API->write('=list=ACTIVOS',false);
                                    $API->write('=address='.$moroso->ip_srv,false);
                                    $API->write('=comment='.$cliente1,true);
                                    $READ = $API->read(true);

                                    //return $READ;
                                }
                                $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                                $API->write('?name='.$cliente1."(".$moroso->id_srv.")",true);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);

                                if(count($ARRAY)==0){
                                    // aqui valida que no exista el cliente registrado en la list ay lo agrega
                                    $API->write("/queue/simple/add",false);
                                    $API->write('=target='.$moroso->ip_srv,false);   // IP
                                    $API->write('=name='.$cliente1."(".$moroso->id_srv.")",false);       // nombre
                                    $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                                    $API->write('=parent='.$parent,true);         // comentario
                                    $READ = $API->read(false);
                                    $ARRAY = $API->parseResponse($READ);


                                    if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                                        $API->write('/queue/simple/getall', false);
                                        $API->write('?name='.$cliente1."(".$moroso->id_srv.")");
                                        // $READ = $API->read(false);
                                        $READ = $API->read(false);
                                        $ARRAY = $API->parseResponse($READ);
                                        if(count($ARRAY)>0){
                                            $API->write('/queue/simple/move', false);
                                            $API->write('=.id=' . $ARRAY[0]['.id'], false);
                                            $API->write('=destination=*0',true);
                                            $ARRAY = $API->read();
                                            //$API->disconnect();



                                        }
                                    }
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
                            $API->write('?list=ACTIVOS',false);
                            $API->write('?disabled=false',false);
                            $API->write('?address='.$moroso->ip_srv,true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY) > 0){
                                $API->write('/ip/firewall/address-list/remove', false);
                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                $READ = $API->read(false);
                                $READ = $API->read(true);
                            }
                            $API->write("/queue/simple/getall",false);                // verificara si existe en la lista queue
                            $API->write('?name='.$cliente1."(".$moroso->id_srv.")",true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY)>0) {
                                $API->write("/queue/simple/remove", false);            // en caso de existir lo eliminara
                                //  $API->write('=.name='.$cliente."(".$id_srv.")");
                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);
                            }

                            $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                            $servicios1->update(["stat_srv"=>4]);
                            historico_cliente::create(['history'=>'Suspension automatica marcado como retirado', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'r')->delete();

                        }
                        echo "retirado: ".$cliente1."/".$moroso->ip_srv."\n";
                    }
                } if ($exonerados->count()  >= 1){
                    foreach ($exonerados as $moroso){
                        //echo "3\n";
                        if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                            $cliente1= ucwords(strtolower($moroso->social));
                        }else {
                            $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                        }
                        cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'e', 'contador'=>'1']);


                        if ($moroso->carac_plan == 1 ) {
                            $parent = "Asimetricos";
                        } else    {

                            $parent = "none";
                        }

                        $API = new RouterosAPI();
                        if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                            $API->write('/ip/firewall/address-list/print',false);
                            $API->write('?list=ACTIVOS',false);
                            $API->write('?disabled=false',false);
                            $API->write('?address='.$moroso->ip_srv,true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY)==0){

                                $API->write('/ip/firewall/address-list/add',false);
                                $API->write('=list=ACTIVOS',false);
                                $API->write('=address='.$moroso->ip_srv,false);
                                $API->write('=comment='.$cliente1,true);
                                $READ = $API->read(true);

                                //return $READ;
                            }


                            $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                            $API->write('?name='.$cliente1."(".$moroso->id_srv.")",true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);

                            if(count($ARRAY)==0){
                                // aqui valida que no exista el cliente registrado en la lista y lo agrega
                                $API->write("/queue/simple/add",false);
                                $API->write('=target='.$moroso->ip_srv,false);   // IP
                                $API->write('=name='.$cliente1."(".$moroso->id_srv.")",false);       // nombre
                                $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                                $API->write('=parent='.$parent,true);         // comentario
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);


                                if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                                    $API->write('/queue/simple/getall', false);
                                    $API->write('?name='.$cliente1."(".$moroso->id_srv.")");
                                    // $READ = $API->read(false);
                                    $READ = $API->read(false);
                                    $ARRAY = $API->parseResponse($READ);
                                    if(count($ARRAY)>0){
                                        $API->write('/queue/simple/move', false);
                                        $API->write('=.id=' . $ARRAY[0]['.id'], false);
                                        $API->write('=destination=*0',true);
                                        $ARRAY = $API->read();
                                        //$API->disconnect();



                                    }
                                }
                            }

                            $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                            $servicios1->update(["stat_srv"=>5]);
                            //historico_cliente::create(['history'=>'Cliente exonerado', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'e')->delete();

                        }
                        echo "exonerado: ".$cliente1."/".$moroso->ip_srv."\n";
                    }
                }

                /*   =============================================================================================================================================================================================== */
                if ($pend_serv->count()  >= 1){
                    foreach ($pend_serv as $moroso){
                        //echo "3\n";
                        if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                            $cliente1= ucwords(strtolower($moroso->social));
                        }else {
                            $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                        }
                        cola_de_ejecucion::create(['id_srv'=>$moroso->soporte_pd."P_I",'soporte_pd'=>$moroso->soporte_pd, 'accion'=>'r_p_i', 'contador'=>'1']);
                        $status= "P_I";
                        $API = new RouterosAPI();
                        if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                            $API->write('/ip/firewall/address-list/print',false);
                            $API->write('?list=ACTIVOS',false);
                            $API->write('?disabled=false',false);
                            $API->write('?address='.$moroso->ip_pd,true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY) > 0){
                                $API->write('/ip/firewall/address-list/remove', false);
                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                $READ = $API->read(false);
                                $READ = $API->read(true);
                            }
                            $API->write("/queue/simple/getall",false);                // verificara si existe en la lista queue
                            $API->write('?name='.$cliente1."(".$status.")",true);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if(count($ARRAY)>0) {
                                $API->write("/queue/simple/remove", false);            // en caso de existir lo eliminara
                                //  $API->write('=.name='.$cliente."(".$id_srv.")");
                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                $READ = $API->read(false);
                                $ARRAY = $API->parseResponse($READ);
                            }


                            historico_cliente::create(['history'=>'desactivacion de conexion por estar aun pendiente por instalacion de servicio', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_pd, 'responsable'=>'0']);
                            cola_de_ejecucion::where('soporte_pd', $moroso->soporte_pd)->where('accion', 'r_p_i')->delete();


                        }
                        echo "pendiente por instalacion: ".$cliente1."/".$moroso->ip_pd."\n";
                    }
                }

            }

        }
    }
}


