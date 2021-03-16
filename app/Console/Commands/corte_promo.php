<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use \Carbon\Carbon;
use App\historico_cliente;
use App\historico;
use App\Mikrotik\RouterosAPI;
use App\cola_de_ejecucion;
use App\Console\Commands;

class corte_promo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corte_promo';

    /**
     * The console command description.
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

      /*
      $API = new RouterosAPI();
      if ($API->connect("192.168.12.1", "16cevingst", "*BA557272686075A9E84114187738DAE6F9E24979")) {
        $API->write('/ping',false);    // send ping command and more is coming
        $API->write('=address=172.16.20.1',false);    //more...   
        $API->write('=count=2',true);   //more...  
          $READ = $API->read(false);
          $ARRAY = $API->parseResponse($READ);

         if(count($ARRAY) > 0 ){
           //var_dump($ARRAY);
           
            if($ARRAY[0]["received"] > 0){
              echo "Responde";
            }else{
              echo "No Responde";
            }
            
         }else{
           echo $ARRAY['!trap'][0]['message'];	
         }
      }
      $API->disconnect();
      */
        
        
      
        $promociones = DB::select("SELECT * FROM fac_promo AS f
                                       INNER JOIN promociones AS pr ON f.promocion = pr.id_promocion
                                       INNER JOIN planes AS p ON f.id_plan_p = p.id_plan WHERE f.status = 1");

        foreach ($promociones as $promo) {
              if ($promo->fecha <= Carbon::now()) {
                  if($promo->id_promocion == 2){
                    $fecha = date("d/m/Y");
                    echo "Promocion eliminada, cliente:(".$promo->comentario.")\n";
                    $finPromo = DB::update("UPDATE fac_promo SET status = 2 WHERE id_promo = ?",[$promo->id_promo]);
                    \Artisan::call('factura:generar', [
                        'cliente' => $promo->id_cliente_p, 'fecha'=>$fecha, 'pro'=>1, 'nro_servicio'=>$promo->id_servicio_p, 'responsable'=>0
                    ]);
                    
                    //$eliminar  = DB::delete("DELETE FROM fac_promo WHERE id_promo = ?",[$promo->id_promo]);
                  }else{

                    $result = DB::select("SELECT * FROM servicios AS s
                                                INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                                INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                                  WHERE s.id_srv = ?",[$promo->id_servicio_p])[0];

                    if ($result->tipo_srv == 1) {
                      $masDatos = DB::select("SELECT * FROM aps AS a
                                                INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                                INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                  WHERE a.id = ?",[$result->ap_srv])[0];
                    } else {
                      $masDatos = DB::select("SELECT * FROM caja_distribucion AS c
                                                INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                                INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                  WHERE c.id_caja = ?",[$result->ap_srv])[0];
                    }
                                                  


                    $ip = $result->ip_srv;

                    if($result->kind == "V" || $result->kind == "E"){
                      $nombre4 = explode(" ",$result->nombre);
                      $apellido4 = explode(" ",$result->apellido);
                      $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
      
                      $cliente1= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                      $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                      $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                      $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                      $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                    } else{
                      $cliente1= ucwords(strtolower($result->social));
                      $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                      $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                      $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                      $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    }    



                    
                    $MK = $masDatos->ip_srvidor;
                    $usermk = $masDatos->user_srvidor;
                    $passwordmk = $masDatos->password_srvidor;
                    $dmb_plan = $promo->dmb_plan;
                    $umb_plan = $promo->umb_plan;

                    if ($promo->carac_plan == 1) {
                      $parent = "Asimetricos";
                  } else if ($promo->carac_plan == 2) {
      
                      $parent = "none";
                  }

                  $id_srv = $promo->id_servicio_p;



                  $API = new RouterosAPI();
                  if ($API->connect($MK, $usermk, $passwordmk)) {
                      $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
                      $API->write('?list=ACTIVOS',false);
                      $API->write('?disabled=false',false);
                      $API->write('?address='.$ip,true);
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);
                      if(count($ARRAY)>0){
                        if ($result->tipo_srv == 1) {
                          $API->write('/ppp/secret/print',false);
                          $API->write('?name='.$cliente."(".$id_srv.")",true);
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
                        }else{
                          $API->write('/ppp/secret/print',false);
                          $API->write('?name='.$cliente2."(".$id_srv.")",true);
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
                        }  
                      if(count($ARRAY)>0) {                                                                //  valida que  exista el cliente registrado en la lista y lo edita
                         
                        $API->write('/ppp/secret/set',false);
                        $API->write('=.id='.$ARRAY[0]['.id'],false);
                        $API->write('=profile='.$result->name_plan,true);
                        $READ = $API->read(false);
                        $ARRAY = $API->parseResponse($READ);


                        if ($result->tipo_srv == 1) {
                          $API->write('/ppp/active/print',false);
                          $API->write('?name='.$cliente."(".$id_srv.")",true);
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
                        }else{
                          $API->write('/ppp/active/print',false);
                          $API->write('?name='.$cliente2."(".$id_srv.")",true);
                          $READ = $API->read(false);
                          $ARRAY = $API->parseResponse($READ);
                        }
                        if(count($ARRAY)>0) {
                            $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                            $API->write('=.id=' . $ARRAY[0]['.id']);
                            $READ = $API->read(false);
                            //return $READ;
                        } 
                        
                      }

                          if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                              $API->write('/queue/simple/getall', false);
                              $API->write('?name='.$cliente."(".$id_srv.")");
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
              
                      
              
                      cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'cp')->delete();
                  }
                  $API->disconnect();

                  
                  $actualizar = DB::update("UPDATE servicios SET plan_srv = ? WHERE id_srv = ?",[$promo->id_plan_p,$result->id_srv]);
                  $finPromo = DB::update("UPDATE fac_promo SET status = 2 WHERE id_promo = ?",[$promo->id_promo]);
                    echo "Promocion eliminada, cliente:(".$id_srv.")\n";
                    //$eliminar  = DB::delete("DELETE FROM fac_promo WHERE id_promo = ?",[$promo->id_promo]);
                    if ($result->serie == 1) {
                     
                    }else{
                      $fecha = date("d/m/Y");
                      \Artisan::call('factura:generar', [
                        'cliente' => $promo->id_cliente_p, 'fecha'=>$fecha, 'pro'=>1, 'nro_servicio'=>$promo->id_servicio_p, 'responsable'=>0
                    ]);
                    }
                  }
                  
                  historico::create(['responsable'=>0, 'modulo'=>'Promociones', 'detalle'=>'Culminaciones de Promocion']);
                  historico_cliente::create(['history'=>'Culminaciones de Promocion', 'modulo'=>'Promociones', 'cliente'=>$promo->id_cliente_p, 'responsable'=>0]);
                    
              }else{
                  
              }
              
        }
      
           
    
  }
}