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

class Suspender_nuevo2 extends command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Suspender_Nuevo2';

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
        $servidores=DB::select("SELECT * FROM servidores WHERE id_srvidor != 26  ");
           foreach ($servidores as $servidor) {
               echo "-".$servidor->nombre_srvidor."\n"; 
                
                $celdas = DB::select('SELECT c.* FROM servidores AS s INNER JOIN celdas AS c ON c.servidor_celda = s.id_srvidor WHERE s.id_srvidor = ?',[$servidor->id_srvidor]);

                $olts = DB::select('SELECT o.* FROM servidores AS s INNER JOIN olts AS o ON s.id_srvidor = o.servidor_olt WHERE  s.id_srvidor = ?',[$servidor->id_srvidor]);
            
                foreach ($celdas as $celda) {
                    //echo "   ".$celda->nombre_celda."\n";

                    $aps =DB::select('SELECT a.* FROM celdas AS c INNER JOIN aps AS a ON a.celda_ap = c.id_celda WHERE c.id_celda = ?',[$celda->id_celda]);

                        foreach ($aps as $ap) {
                            //echo "      ".$ap->nombre_ap."\n";
                            
                            $clientes = DB::select('SELECT c.id, c.kind,c.nombre,c.apellido,c.social,c.serie,s.* FROM aps AS a 
                                                        INNER JOIN servicios AS s ON a.id = s.ap_srv
                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id 
                                                            WHERE a.id = ? AND s.stat_srv = 1 AND s.tipo_srv = 1 AND (c.serie != 1 OR c.serie is null)',[$ap->id]);

                             foreach ($clientes as $cliente) {
                                    if($cliente->kind == "V" || $cliente->kind == "E"){
                                        $cliente1 = ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
                                       
                                    }else{
                                        $cliente1 = ucwords($cliente->social);
                                       
                                    }
                                    
                                $facturas = DB::select('SELECT f.*, 
                                (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where f.id = fac_products.codigo_factura) as monto,
                                 (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where f.id = fac_pagos.fac_id) as pagado
                                        FROM fac_controls AS f WHERE f.fac_serv = ? AND f.fac_status = 1 ORDER BY id DESC LIMIT 1',[$cliente->id_srv]);
                                    foreach ($facturas as $fac){

                                        if($fac->monto > $fac->pagado){

                                                    $comprosimo = DB::select('SELECT * FROM corte_progs WHERE id_servicio = ? AND status = 1',[$cliente->id_srv]);

                                                        if(count($comprosimo) >= 1){
                                                           // echo "(Compromiso de Pago)"."[".$servidor->nombre_srvidor."]"."\n";   
                                                        }else{

                                                            $promo = DB::select('SELECT * FROM fac_promo WHERE id_cliente_p = ? AND status = 1',[$cliente->id]);

                                                            if(count($promo) > 0){
                                                               // echo "(Promocion)"."[".$servidor->nombre_srvidor."]"."\n";
                                                            }else{
                                                                
                                                            $API = new RouterosAPI();
                                                            if ($API->connect($servidor->ip_srvidor, $servidor->user_srvidor, $servidor->password_srvidor)) {
                                                                $API->write('/ip/firewall/address-list/print',false);
                                                                $API->write('?list=ACTIVOS',false);
                                                                $API->write('?disabled=false',false);
                                                                $API->write('?address='.$cliente->ip_srv,true);
                                                                $READ = $API->read(false);
                                                                $ARRAY = $API->parseResponse($READ);
                                                                if(count($ARRAY)>0){
                                                                    $API->write('/ip/firewall/address-list/remove', false);
                                                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                    $READ = $API->read(false);
                                                                }
                                                                $API->write("/queue/simple/getall",false);                // verificara si existe en la lista queue
                                                                $API->write('?name='.$cliente1."(".$cliente->id_srv.")",true);
                                                                $READ = $API->read(false);
                                                                $ARRAY = $API->parseResponse($READ);
                                                                if(count($ARRAY)>0) {
                                                                    $API->write("/queue/simple/remove", false);            // en caso de existir lo eliminara
                                                                    //  $API->write('=.name='.$cliente."(".$id_srv.")");
                                                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                    $READ = $API->read(false);
                                                                    $ARRAY = $API->parseResponse($READ);
                                                                }

                                                                $API->write('/ppp/secret/print',false);
                                                                $API->write('?remote-address='.$cliente->ip_srv,true);
                                                                $READ = $API->read(false);
                                                                $ARRAY = $API->parseResponse($READ);
                                                                if(count($ARRAY)>0) {
                                                                    $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                                                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                    $READ = $API->read(false);
                                                                    //return $READ;
                                                                }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                                                                    $API->write('/ppp/active/print',false);
                                                                    $API->write('?address='.$cliente->ip_srv,true);
                                                                    $READ = $API->read(false);
                                                                    $ARRAY = $API->parseResponse($READ);
                                                                   
                                                                    if(count($ARRAY)>0) {
                                                                        $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                                                                        $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                        $READ = $API->read(false);
                                                                        //return $READ;
                                                                }


                                                                $servicios1 = servicios::where('ip_srv', $cliente->ip_srv);
                                                                $servicios1->update(["stat_srv"=>3]);
                                                                historico_cliente::create(['history'=>'Suspension automatica por deber 1 factura o mas', 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>'0']);
                                                                //cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $cliente->ip_srv)->where('accion', 's')->delete();
                                                            }
                                                            $API->disconnect();
                                                            
                                                            echo $cliente1." || ".$cliente->ip_srv."(Cortado)"."[".$servidor->nombre_srvidor."]"."\n";    
                                                            
                                                            }
                                                            
                                                        }
                                                
                                        }else{
                                            //echo " (Solvente)"."[".$servidor->nombre_srvidor."]"."\n";   
                                        }    
                                    } 
                                    
                             }                               
                             
                        }
                }
                


                foreach ($olts as $olt) {
                    $cajas_distribucion = DB::select("SELECT c.* FROM caja_distribucion AS c 
                                                        INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                                        INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                            WHERE o.id_olt = ?",[$olt->id_olt]);


                    foreach ($cajas_distribucion as $caja) {

                       
                        $clientes = DB::select('SELECT c.id, c.kind,c.nombre,c.apellido,c.social,c.serie,s.* FROM caja_distribucion AS a 
                                                    INNER JOIN servicios AS s ON a.id_caja = s.ap_srv
                                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id 
                                                        WHERE a.id_caja = ? AND s.stat_srv = 1 AND s.tipo_srv = 2 AND (c.serie != 1 OR c.serie is null)',[$caja->id_caja]);

                        foreach ($clientes as $cliente) {

                            if($cliente->kind == "V" || $cliente->kind == "E"){
                                $cliente1 = ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
                                $nombre4 = explode(" ",$cliente->nombre);
                                $apellido4 = explode(" ",$cliente->apellido);
                                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
        
                             
                                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                               

                            }else{
                                $cliente1 = ucwords($cliente->social);
                                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                                
                            }
                            
                        $facturas = DB::select('SELECT f.*, 
                        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where f.id = fac_products.codigo_factura) as monto,
                        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where f.id = fac_pagos.fac_id) as pagado
                                FROM fac_controls AS f WHERE f.fac_serv = ? AND f.fac_status = 1 ORDER BY id DESC LIMIT 1',[$cliente->id_srv]);
                            foreach ($facturas as $fac){

                                if($fac->monto > $fac->pagado){

                                   
                                            $comprosimo = DB::select('SELECT * FROM corte_progs WHERE id_servicio = ? AND status = 1',[$cliente->id_srv]);

                                                if(count($comprosimo) >= 1){
                                                    //echo "(Compromiso de Pago)"."[".$servidor->nombre_srvidor."][F.O]"."\n";   
                                                }else{

                                                    $promo = DB::select('SELECT * FROM fac_promo WHERE id_cliente_p = ? AND status = 1',[$cliente->id]);

                                                    if(count($promo) > 0){
                                                       // echo "(Promocion)"."[".$servidor->nombre_srvidor."][F.O]"."\n";
                                                    }else{
                                                        
                                                    $API = new RouterosAPI();
                                                    if ($API->connect($servidor->ip_srvidor, $servidor->user_srvidor, $servidor->password_srvidor)) {                                  //se conecta y verifica si el cliente exista en la lista
                                                        $API->write('/ppp/secret/print',false);
                                                                $API->write('?name='.$cliente2."(".$cliente->id_srv.")",true);
                                                                $READ = $API->read(false);
                                                                $ARRAY = $API->parseResponse($READ);
                                                                if(count($ARRAY)>0) {
                                                                    $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                                                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                    $READ = $API->read(false);
                                                                    //return $READ;
                                                                }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                                                                    $API->write('/ppp/active/print',false);
                                                                    $API->write('?name='.$cliente2."(".$cliente->id_srv.")",true);
                                                                    $READ = $API->read(false);
                                                                    $ARRAY = $API->parseResponse($READ);
                                                                  
                                                                    
                                                                    if(count($ARRAY)>0) {
                                                                        $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                                                                        $API->write('=.id=' . $ARRAY[0]['.id']);
                                                                        $READ = $API->read(false);
                                                                        //return $READ;
                                                                    } 
        
                                        
                                                            }
                                                            $API->disconnect();
                                                            $servicios1 = servicios::where('id_srv', $cliente->id_srv);
                                                            $servicios1->update(["stat_srv"=>3]);
                                                            historico_cliente::create(['history'=>'Suspension automatica por deber 1 factura o mas', 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>'0']);
                                                           // cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('id_srv', $cliente->id_srv)->where('accion', 's')->delete();         
                                                    
                                                     
                                                            echo $cliente1." || ".$cliente->ip_srv."(Cortado)"."[".$servidor->nombre_srvidor."][F.O]"."\n"; 
                                                    }
                                                    
                                                }
                                        
                                }else{
                                   // echo " (Solvente)"."[".$servidor->nombre_srvidor."][F.O]"."\n";   
                                }    
                            } 
                            
                        } 

                    }

                }

                
           }
           
        
    }

}
