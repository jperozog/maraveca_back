<?php

namespace App\Console\Commands;
use DB;
use App\servicios;
use App\historico;
use App\historico_cliente;
use App\cola_de_ejecucion;
use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
class Agregar_Activos extends command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Agregar_Activos';

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
            $idcli = $cliente->id;

           $activos=DB::table('servicios')  //buscamos los servicios
            ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
                ->join('aps','aps.id','=','servicios.ap_srv')
                ->join('clientes','clientes.id','=','servicios.cliente_srv')
                ->join('planes','planes.id_plan','=','servicios.plan_srv')
                ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                ->where('servicios.cliente_srv','=',$cliente->id)
                //->where('servicios.stat_srv','!=','3')
                ->where('servicios.stat_srv','=','1')
         ->where('servidores.ip_srvidor','=','172.16.16.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

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
               ->where('servidores.ip_srvidor','=','172.16.16.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

                //->where('servicios.ip_srv', '192.168.0.1')
                ->groupBy('servicios.ip_srv')
                ->get();

           $morosos=DB::table('servicios')  //buscamos los servicios
            ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
                ->join('aps','aps.id','=','servicios.ap_srv')
                ->join('clientes','clientes.id','=','servicios.cliente_srv')
                ->join('planes','planes.id_plan','=','servicios.plan_srv')
                ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                ->where('servicios.cliente_srv','=',$cliente->id)
                ->where('servicios.stat_srv','=','3')
                ->where('servidores.ip_srvidor','=','172.16.16.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

                //->where('servicios.ip_srv', '192.168.0.1')
                ->groupBy('servicios.ip_srv')
                ->get();

           $retirados=DB::table('servicios')  //buscamos los servicios
            ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
                ->join('aps','aps.id','=','servicios.ap_srv')
                ->join('clientes','clientes.id','=','servicios.cliente_srv')
                ->join('planes','planes.id_plan','=','servicios.plan_srv')
                ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                ->where('servicios.cliente_srv','=',$cliente->id)
                //->where('servicios.stat_srv','!=','3')
                ->where('servicios.stat_srv','=','4')
                ->where('servidores.ip_srvidor','=','172.16.16.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

                //->where('servicios.ip_srv', '192.168.0.1')
                ->groupBy('servicios.ip_srv')
                ->get();

            if ($activos->count()  >= 1){
                foreach ($activos as $moroso){
                    if ($moroso->carac_plan == 1 ) {
                        $parent = "Asimetricos";
                    } else if ($moroso->carac_plan ==2 )  {

                        $parent = "none";
                    }
                    //echo "3\n";
                    if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                        $cliente1= ucwords(strtolower($moroso->social));
                    }else {
                        $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    }
                    cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'a', 'contador'=>'1']);
                    $API = new RouterosAPI();
                    if ($API->connect("10.0.0.82", $moroso->user_srvidor, $moroso->password_srvidor)) {
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

                        //$servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                      //  $servicios1->update(["stat_srv"=>1]);
                        //historico_cliente::create(['history'=>'Suspension automatica marcado como retirado', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                        cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'a')->delete();
                        echo "Activo: ".$cliente1."/".$moroso->ip_srv."\n";
                    }

                }

        }if ($exonerados->count()  >= 1) {
                foreach ($exonerados as $moroso) {
                    //echo "3\n";
                    if ((strtolower($moroso->kind) == 'g' || strtolower($moroso->kind) == 'j') && (strtolower($moroso->social) != 'null' && $moroso->kind != null)) {
                        $cliente1 = ucwords(strtolower($moroso->social));
                    } else {
                        $cliente1 = ucfirst($moroso->nombre) . " " . ucfirst($moroso->apellido);
                    }
                    cola_de_ejecucion::create(['id_srv' => $moroso->id_srv, 'accion' => 'e', 'contador' => '1']);

                    if ($moroso->carac_plan == 1) {
                        $parent = "Asimetricos";
                    } else if ($moroso->carac_plan == 2) {

                        $parent = "none";
                    }
                    $API = new RouterosAPI();
                    if ($API->connect("10.0.0.82", $moroso->user_srvidor, $moroso->password_srvidor)) {
                        $API->write('/ip/firewall/address-list/print', false);
                        $API->write('?list=ACTIVOS', false);
                        $API->write('?disabled=false', false);
                        $API->write('?address=' . $moroso->ip_srv, true);
                        $READ = $API->read(false);
                        $ARRAY = $API->parseResponse($READ);
                        if (count($ARRAY) == 0) {

                            $API->write('/ip/firewall/address-list/add', false);
                            $API->write('=list=ACTIVOS', false);
                            $API->write('=address=' . $moroso->ip_srv, false);
                            $API->write('=comment=' . $cliente1, true);
                            $READ = $API->read(true);

                            //return $READ;
                        }
                    }
                    $API->write("/queue/simple/getall", false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                    $API->write('?name=' . $cliente1 . "(" . $moroso->id_srv . ")", true);
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);

                    if (count($ARRAY) == 0) {
                        // aqui valida que no exista el cliente registrado en la list ay lo agrega
                        $API->write("/queue/simple/add", false);
                        $API->write('=target=' . $moroso->ip_srv, false);   // IP
                        $API->write('=name=' . $cliente1 . "(" . $moroso->id_srv . ")", false);       // nombre
                        $API->write('=max-limit=' . $moroso->umb_plan . "M" . "/" . $moroso->dmb_plan . "M", false);   //   2M/2M   [TX/RX]
                        $API->write('=parent=' . $parent, true);         // comentario
                        $READ = $API->read(false);
                        $ARRAY = $API->parseResponse($READ);


                        if ($parent == "none") {                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                            $API->write('/queue/simple/getall', false);
                            $API->write('?name=' . $cliente1 . "(" . $moroso->id_srv . ")");
                            // $READ = $API->read(false);
                            $READ = $API->read(false);
                            $ARRAY = $API->parseResponse($READ);
                            if (count($ARRAY) > 0) {
                                $API->write('/queue/simple/move', false);
                                $API->write('=.id=' . $ARRAY[0]['.id'], false);
                                $API->write('=destination=*0', true);
                                $ARRAY = $API->read();
                                //$API->disconnect();


                            }
                        }
                    }


                    $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                    $servicios1->update(["stat_srv" => 5]);
                    //historico_cliente::create(['history'=>'Suspension automatica marcado como retirado', 'modulo'=>'Facturacion', 'cliente'=>$moroso->cliente_srv, 'responsable'=>'0']);
                    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'e')->delete();
                    echo "exonerado: " . $cliente1 . "/" . $moroso->ip_srv . "\n";
                }


            }if ($morosos->count()  >= 1){

                foreach ($morosos as $moroso){

                    //echo "3\n";
                    if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                        $cliente1= ucwords(strtolower($moroso->social));
                    }else {
                        $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    }
                    cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'s', 'contador'=>'1']);

                    $API = new RouterosAPI();

                    if ($API->connect("10.0.0.82", $moroso->user_srvidor, $moroso->password_srvidor)) {
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
                            echo "Moroso: ".$cliente1."/".$moroso->ip_srv."\n";
                        }




                }
            }if ($retirados->count()  >= 1){
                foreach ($retirados as $moroso){
                    //echo "3\n";
                    if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                        $cliente1= ucwords(strtolower($moroso->social));
                    }else {
                        $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    }
                    cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'r', 'contador'=>'1']);
                    $API = new RouterosAPI();
                    if ($API->connect("10.0.0.82", $moroso->user_srvidor, $moroso->password_srvidor)) {
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
                            echo "retirado: ".$cliente1."/".$moroso->ip_srv."\n";
                        }


                    }

                }
            }
        }
//        }





   // }
}

