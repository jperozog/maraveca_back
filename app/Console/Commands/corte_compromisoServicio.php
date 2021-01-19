<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mikrotik\RouterosAPI;
use \Carbon\Carbon;
use App\cola_de_ejecucion;
use App\historico_cliente;
use App\historico;

class corte_compromisoServicio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compromisos_servicios';

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
        $compromisos = DB::select("SELECT * FROM compromisos_servicios AS c
                                         inner join clientes as cli on c.id_cliente_com = cli.id
                                         inner join servicios as s on c.id_servicio_com = s.id_srv
                                         inner join aps as a on s.ap_srv = a.id
                                         inner join celdas as ce on a.celda_ap = ce.id_celda
                                         inner join servidores as se on ce.servidor_celda = se.id_srvidor
                                         inner join planes as p on s.plan_srv = p.id_plan
                                          where c.status = 1");

    foreach ($compromisos as $c) {
        if ($c->fecha_finalizacion <= Carbon::now()) {

            echo $c->id_srv."\n";

            if($c->social == "null" || $c->social == null){
                $cliente1 = ucfirst($c->nombre) . " " . ucfirst($c->apellido);
                $remp_cliente = array('ñ', 'Ñ');
                $correct_cliente = array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
              } else{
                $cliente1 = ucwords(strtolower($c->social));
                $remp_cliente = array('ñ', 'Ñ');
                $correct_cliente = array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
              }

              if ($c->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($c->carac_plan == 2) {
                $parent = "none";
            }  

            $API = new RouterosAPI();
                  if ($API->connect($c->ip_srvidor, $c->user_srvidor, $c->password_srvidor)) {
                      $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
                      $API->write('?list=ACTIVOS',false);
                      $API->write('?disabled=false',false);
                      $API->write('?address='.$c->ip_srv,true);
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);
                      if(count($ARRAY)>0){
              
                      $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                      $API->write('?name='.$cliente."(".$c->id_srv.")",true);
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);
              
                      if(count($ARRAY)>0) {                                                                //  valida que  exista el cliente registrado en la lista y lo edita
                          $API->write("/queue/simple/set", false);
                          $API->write('=.id=' . $ARRAY[0]['.id'], false);
                          $API->write('=max-limit='.$c->umb_plan."M". "/".$c->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                          $API->write('=parent='.$parent,true);         // comentario
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
                      }else{
                          // aqui valida que no exista el cliente registrado en la lista y lo agrega
                          $API->write("/queue/simple/add",false);
                          $API->write('=target='.$c->ip_srv,false);   // IP
                          $API->write('=name='.$cliente."(".$c->id_srv.")",false);       // nombre
                          $API->write('=max-limit='.$c->mb_plan."M". "/".$c->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                          $API->write('=parent='.$parent,true);         // comentario
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
              
                      }
                          if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                              $API->write('/queue/simple/getall', false);
                              $API->write('?name='.$cliente."(".$c->id_srv.")");
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
              
                
                  }
                  $API->disconnect();

                $actualizarCompromimso = DB::update("UPDATE compromisos_servicios SET status = 2 WHERE id_compromiso = ?",[$c->id_compromiso]);
                historico::create(['responsable'=>0, 'modulo'=>'Compromiso de Servicio', 'detalle'=>'Culminacion de Compromiso de Servicio para el Cliente']);
                historico_cliente::create(['history'=>'Culminacion de Compromiso de Servicio para el Cliente', 'modulo'=>'Compromiso de Servicio', 'cliente'=>$c->id_cliente_com, 'responsable'=>0]);    
            }
        }
    }
}
