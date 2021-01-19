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

class Agregar_plan_lista extends command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Agregar_plan_lista';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega planes en queue list';

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
            $servicios=DB::table('servicios')  //buscamos los servicios
            ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
                ->join('aps','aps.id','=','servicios.ap_srv')
                ->join('clientes','clientes.id','=','servicios.cliente_srv')
                ->join('planes','planes.id_plan','=','servicios.plan_srv')
                ->join('celdas','aps.celda_ap','=','celdas.id_celda')
                ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
                ->where('servicios.cliente_srv','=',$cliente->id)
                ->where('servicios.stat_srv','!=','3')
                ->where('servicios.stat_srv','!=','2')
              ->where('servicios.stat_srv','!=','4')
              //  ->where('servidores.ip_srvidor','=','192.168.12.1')// este cambio se hizo solo para los MK galica y valle claro debido a al actualizacion del firmware

                //->where('servicios.ip_srv', '192.168.0.1')
                ->groupBy('servicios.ip_srv')
                ->get();



                    foreach ($servicios as $moroso) {

                        if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                            $cliente1= ucwords(strtolower($moroso->social));
                        }else {
                            $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                        }
                        cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'cp', 'contador'=>'1']);
                        if ($moroso->carac_plan == 1 ) {
                            $parent = "Asimetricos";
                        } else if ($moroso->carac_plan ==2 )  {

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
                           // $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                            //$servicios1->update(["stat_srv"=>1]);
                            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'cp')->delete();
                        }
                        $API->disconnect();
                        echo "cliente agregado: ".$cliente1."/".$moroso->ip_srv."\n";
                    }
                }
            }



        }//

