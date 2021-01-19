<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
use Illuminate\Support\Facades\DB;

class agregarPPPOE extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agregar_pppoe';

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
                                     
        
        
        $clientes = DB::select("SELECT * FROM servicios as s 
                                        inner join clientes as cl on s.cliente_srv = cl.id
                                        inner join aps as a on s.ap_srv = a.id
                                        inner join celdas as c on a.celda_ap = c.id_celda
                                        inner join servidores as ser on c.servidor_celda = ser.id_srvidor
                                        inner join planes as p on s.plan_srv = p.id_plan
                                            WHERE c.id_celda = 29 and s.stat_srv = 1 and p.id_plan = 49");

            foreach ($clientes as $cli ) {
                echo $cli->nombre." ".$cli->apellido."\n";
                if($cli->kind=='G'|| $cli->kind=='J'  &&  $cli->social!= 'null' &&$cli->kind != null){
                    $cliente2 = $cli->social;
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
                }else{
                    $nombre1 = explode(" ",$cli->nombre);
                    $apellido1 = explode(" ",$cli->apellido);

                    $cliente2 = $nombre1[0]." ".$apellido1[0];
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
                }
                $clienteServicios = DB::select("SELECT * FROM servicios WHERE cliente_srv = ?",[$cli->cliente_srv]);   
                $clienteInstalaciones = DB::select("SELECT * FROM instalaciones WHERE cliente_insta = ? AND status_insta = 1",[$cli->cliente_srv]); 
                $cantidadServicios = count($clienteServicios) + count($clienteInstalaciones);
                $estatus = "S".$cantidadServicios;

                $API = new RouterosAPI();
                if ($API->connect($cli->ip_srvidor,$cli->user_srvidor,$cli->password_srvidor)) {
                    $API->write('/ppp/secret/add',false);
                    $API->write('=name='.$cliente."(".$cli->id_srv.")",false);
                    $API->write('=password='.$cli->dni,false);
                    $API->write('=service='."pppoe",false);
                    $API->write('=profile='.$cli->name_plan,false);
                    $API->write('=remote-address='.$cli->ip_srv,false);
                    $API->write('=local-address='.$cli->ip_srvidor,true);
                
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);
                }
                $API->disconnect();
            }
        
    }
}
