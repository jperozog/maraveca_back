<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\historico;
use App\notify;
use App\User;
use App\servidores;
use App\celdas;
use App\ap;
use App\planes;
use App\cola_email_notify;
use Mail;
use App\historico_mensaje;
class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *modulo para enviar mensajes masivos segun router (detail 1)
     *segun celda (datail 2)
     *segun ap (detail 3)
     *y segun plan (detail 4)
     * @return \Illuminate\Http\Response
     */
    public function snotify(Request $request)
    {
        
        $listaDatos=[];
        $detail1 = [];
        
        if ($request->tipo == 1) {

            foreach ($request->datos as $cliente) {
        
                if($cliente["kind"]=='G'|| $cliente["kind"]=='J' || $cliente["kind"]=='V-'){
                    $cli= ucwords(strtolower($cliente["social"]));
                }else {
                    $cli= ucfirst($cliente["nombre"])." ".ucfirst($cliente["apellido"]);
                }
                
                $servicios= DB::select("SELECT * FROM servicios WHERE cliente_srv = ? AND (stat_srv = 1 OR stat_srv = 5)",[$cliente["id"]]);

                if(count($servicios) > 0){
                    array_push($listaDatos, $cliente);
                }

                
            }

          foreach ($listaDatos as $dato) {
                
                $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                if (!$fp) {
                    //echo "ERROR: $errno - $errstr<br />\n";
                }
                else {
                    fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                    fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($dato["phone1"])." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                    
                    fclose($fp);
                }

                historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);

          }  
        }
        
        if ($request->tipo == 2) {

            foreach ($request->datos as $servidor) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN aps AS a ON s.ap_srv = a.id
                                            INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                            INNER JOIN servidores AS se on c.servidor_celda = se.id_srvidor
                                                WHERE se.id_srvidor = ?",[$servidor["id_srvidor"]]);

                foreach ($servicios as $servicio) {
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
            }
            
        
      
        }

        if ($request->tipo == 3) {

            foreach ($request->datos as $celda) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN aps AS a ON s.ap_srv = a.id
                                            INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                                WHERE c.id_celda = ?",[$celda["id_celda"]]);
                
                foreach ($servicios as $servicio) {
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
                
            }
            
        
      
        }

        if ($request->tipo == 4) {

            foreach ($request->datos as $ap) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN aps AS a ON s.ap_srv = a.id
                                                WHERE a.id = ?",[$ap["id"]]);
                
                foreach ($servicios as $servicio) {
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
                
            
            }      
        }
        
        if ($request->tipo == 5) {

            foreach ($request->datos as $caja) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN caja_distribucion AS c ON s.ap_srv = c.id_caja
                                                WHERE c.id_caja = ?",[$caja["id_caja"]]);
                
                foreach ($servicios as $servicio) {
                    
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
                      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
                
            
            }      
        }

        if ($request->tipo == 6) {

            foreach ($request->datos as $manga) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN caja_distribucion AS c ON s.ap_srv = c.id_caja
                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                                WHERE m.id_manga = ?",[$manga["id_manga"]]);
                
                foreach ($servicios as $servicio) {
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
                
                
            }    
             
        }

        if ($request->tipo == 7) {

            foreach ($request->datos as $olt) {

                $servicios = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS cl ON s.cliente_srv = cl.id
                                            INNER JOIN caja_distribucion AS c ON s.ap_srv = c.id_caja
                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                WHERE o.id_olt = ?",[$olt["id_olt"]]);
                
                foreach ($servicios as $servicio) {
                      $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                      if (!$fp) {
                          //echo "ERROR: $errno - $errstr<br />\n";
                      }
                      else {
                          fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                          fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($servicio->phone1)." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                          
                          fclose($fp);
                      }
      
                      historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'Noficacion de Falla/Mejora en la Red', 'detalle'=>$request->mensaje]);
                      
                }
                
                
            }    
             
        }
        

        return response()->json($servicios);
    }

   
    public function mensajes_morosos()
    {
        $msj_moroso = notify::where('tipo_sms','=', 3)->get();

        return $msj_moroso;
    }

    public function env_msj_morosos(Request $request)
    {
        
        historico::create(['responsable'=>$request->responsable, 'modulo'=>'Notificacion', 'detalle'=>'Envio de mensajes masivos a clientes que presentan deuda:'.$request->mensaje]);
        //notify::where('tipo_sms','=', $request->tipo_sms)->update(['mensaje' => $request->mensaje]);

        \Artisan::call('MensajesAMorososFront', [
            'mensaje' => $request->mensaje, 'Responsable'=>$request->responsable
        ]);
        
        return response()->json($request);
    }


    public function traerHistoricoMensaje(){

        $historicos = DB::select("SELECT h.*,COUNT(id) as cantidad FROM historico_mensajes AS h
                                        WHERE modulo = 'mensaje a deudores'
                                        GROUP BY DAY(created_at), MONTH(created_at), YEAR(created_at)  
                                            ORDER BY id  DESC");

        return response()->json($historicos);
    }

    public function traerHistoricoMensajeFallas(){

        $historicos = DB::select("SELECT h.*,COUNT(id) as cantidad FROM historico_mensajes AS h
                                        WHERE modulo = 'Noficacion de Falla/Mejora en la Red' 
                                        GROUP BY DAY(created_at), MONTH(created_at), YEAR(created_at)  
                                            ORDER BY id  DESC");

        return response()->json($historicos);
    }

}
