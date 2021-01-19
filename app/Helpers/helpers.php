<?php
//app/Helpers/Envato/User.php
//namespace App\Helpers\Helpers;
namespace App\Http\Controllers;
use App\corte_prog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mikrotik\RouterosAPI;
use App\servicios;
use App\configuracion;
use App\clientes;
use App\ap;
use App\fac_adic;
use App\fac_control;
use App\fac_pago;
use App\fac_product;
use App\equipos;
use App\celdas;
use App\planes;
use App\balance_cliente;
use App\balance_clientes_in;
use App\historico_cliente;
use App\historico;
use App\User;
use App\cola_de_ejecucion;
use App\notify;
use Mail;
//use Illuminate\Support\Facades\DB;

function Cambiar_plan($ip, $cliente, $MK, $usermk, $passwordmk, $dmb_plan, $umb_plan, $parent, $status,$name_plan){


    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {
        $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
       


        $API->write('/ppp/secret/print',false);
        $API->write('?remote-address='.$ip,true);
        
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);

                if(count($ARRAY)>0) {
                    $API->write('/ppp/secret/set',false);
                    $API->write('=.id='.$ARRAY[0]['.id'],false);
                    $API->write('=profile='.$name_plan,true);
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);
                }
                $API->write('/ppp/active/print',false);
                $API->write('?address='.$ip,true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                var_dump($ARRAY);
                
                if(count($ARRAY)>0) {
                    $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                    $API->write('=.id=' . $ARRAY[0]['.id']);
                    $READ = $API->read(false);
                    //return $READ;
                }



        cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'cp')->delete();
    }
    $API->disconnect();
}



function cambiar_Ip($ip,$ipNueva, $cliente, $MK, $usermk, $passwordmk, $dmb_plan, $umb_plan, $parent, $id_srv,$cliente2,$cedula,$nombre_plan){

    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {
        $API->write('/ip/firewall/address-list/print',false);
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)>0) {
            $API->write('/ip/firewall/address-list/remove', false); // en caso de existir lo eliminara
            $API->write('=.id=' . $ARRAY[0]['.id']);
            $READ = $API->read(false);
            //return $READ;
        }
  
          $API->write('/ppp/secret/print',false);
              $API->write('?remote-address='.$ip,true);
              $READ = $API->read(false);
              $ARRAY = $API->parseResponse($READ);
              if(count($ARRAY)>0) {
                  $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                  $API->write('=.id=' . $ARRAY[0]['.id']);
                  $READ = $API->read(false);
                  //return $READ;
              }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                  $API->write('/ppp/active/print',false);
                  $API->write('?address='.$ip,true);
                  $READ = $API->read(false);
                  $ARRAY = $API->parseResponse($READ);
                  var_dump($ARRAY);
                  
                  if(count($ARRAY)>0) {
                      $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                      $API->write('=.id=' . $ARRAY[0]['.id']);
                      $READ = $API->read(false);
                      //return $READ;
                  }  












        $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ipNueva,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)==0){                                                             //en caso de que no exista lo agrega
            $API->write('/ip/firewall/address-list/add',false);
            $API->write('=list=ACTIVOS',false);
            $API->write('=address='.$ipNueva,false);
            $API->write('=comment='.$cliente,true);
            $READ = $API->read(true);
        }
        
           $API->write('/ppp/secret/add',false);
           $API->write('=name='.$cliente2."(".$id_srv.")",false);
           $API->write('=password='.$cedula,false);
           $API->write('=service='."pppoe",false);
           $API->write('=profile='.$nombre_plan,false);
           $API->write('=remote-address='.$ipNueva,false);
           $API->write('=local-address='.$MK,true);
       
           $READ = $API->read(false);
           $ARRAY = $API->parseResponse($READ);
           
  
  
      cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'a')->delete();
    }
   $API->disconnect();
  }

function Cambiar_plan_pppoe($cliente, $MK, $user, $clave, $name_plan,$status){
    $API = new RouterosAPI();
    if ($API->connect($MK, $user,$clave)) {
        $API->write('/ppp/secret/print',false);
        $API->write('?name='.$cliente."(".$status.")",true);
        
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);

                if(count($ARRAY)>0) {
                    $API->write('/ppp/secret/set',false);
                    $API->write('=.id='.$ARRAY[0]['.id'],false);
                    $API->write('=profile='.$name_plan,true);
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);
                }
                $API->write('/ppp/active/print',false);
                $API->write('?name='.$cliente."(".$status.")",true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                var_dump($ARRAY);
                
                if(count($ARRAY)>0) {
                    $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                    $API->write('=.id=' . $ARRAY[0]['.id']);
                    $READ = $API->read(false);
                    //return $READ;
                }         
    }
    $API->disconnect();
      
}


function suspender($ip, $cliente, $MK, $usermk, $passwordmk,$id_srv){
  //require('routeros_api.class.php');

  $API = new RouterosAPI();
  if ($API->connect($MK, $usermk, $passwordmk)) {                                  //se conecta y verifica si el cliente exista en la lista
      $API->write('/ip/firewall/address-list/print',false);
      $API->write('?list=ACTIVOS',false);
      $API->write('?disabled=false',false);
      $API->write('?address='.$ip,true);
      $READ = $API->read(false);
      $ARRAY = $API->parseResponse($READ);
      if(count($ARRAY)>0) {
          $API->write('/ip/firewall/address-list/remove', false); // en caso de existir lo eliminara
          $API->write('=.id=' . $ARRAY[0]['.id']);
          $READ = $API->read(false);
          //return $READ;
      }

        $API->write('/ppp/secret/print',false);
            $API->write('?remote-address='.$ip,true);
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            if(count($ARRAY)>0) {
                $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                $API->write('=.id=' . $ARRAY[0]['.id']);
                $READ = $API->read(false);
                //return $READ;
            }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                $API->write('/ppp/active/print',false);
                $API->write('?address='.$ip,true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                var_dump($ARRAY);
                
                if(count($ARRAY)>0) {
                    $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                    $API->write('=.id=' . $ARRAY[0]['.id']);
                    $READ = $API->read(false);
                    //return $READ;
                }  
      

    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 's')->delete();
    ;
  }
  $API->disconnect();
}

function suspenderProvicional($servicio){

    $datosServicio = DB::select("SELECT * FROM servicios WHERE id_srv = ?",[$servicio])["0"];

    if ($datosServicio->tipo_srv == 1) {
        
    } else {
     
    }
    

}



function retirar($ip, $cliente, $MK, $usermk, $passwordmk,$id_srv){
  //require('routeros_api.class.php');

  $API = new RouterosAPI();
  if ($API->connect($MK, $usermk, $passwordmk)) {
      $API->write('/ip/firewall/address-list/print',false);
      $API->write('?list=ACTIVOS',false);
      $API->write('?disabled=false',false);
      $API->write('?address='.$ip,true);
      $READ = $API->read(false);
      $ARRAY = $API->parseResponse($READ);
      if(count($ARRAY)>0){
          $API->write('/ip/firewall/address-list/remove', false);
          $API->write('=.id=' . $ARRAY[0]['.id']);
          $READ = $API->read(false);
          //return $READ;
    }
      
    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'r')->delete();
    ;
  }
  $API->disconnect();
}
function activar($ip, $cliente, $MK, $usermk, $passwordmk, $dmb_plan, $umb_plan, $parent, $id_srv,$cliente2,$cedula,$nombre_plan){

  $API = new RouterosAPI();
  if ($API->connect($MK, $usermk, $passwordmk)) {
      $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
      $API->write('?list=ACTIVOS',false);
      $API->write('?disabled=false',false);
      $API->write('?address='.$ip,true);
      $READ = $API->read(false);
      $ARRAY = $API->parseResponse($READ);
      if(count($ARRAY)==0){                                                             //en caso de que no exista lo agrega
          $API->write('/ip/firewall/address-list/add',false);
          $API->write('=list=ACTIVOS',false);
          $API->write('=address='.$ip,false);
          $API->write('=comment='.$cliente,true);
          $READ = $API->read(true);
      }
      

         
         $API->write('/ppp/secret/add',false);
         $API->write('=name='.$cliente2."(".$id_srv.")",false);
         $API->write('=password='.$cedula,false);
         $API->write('=service='."pppoe",false);
         $API->write('=profile='.$nombre_plan,false);
         $API->write('=remote-address='.$ip,false);
         $API->write('=local-address='.$MK,true);
     
         $READ = $API->read(false);
         $ARRAY = $API->parseResponse($READ);
         


    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'a')->delete();
  }
 $API->disconnect();
    return 120;
}

function activar_pppoe($cliente2,$MK,$usermk,$passwordmk,$id_srv,$cedula,$nombre_plan){


    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {
           $API->write('/ppp/secret/add',false);
           $API->write('=name='.$cliente2."(".$id_srv.")",false);
           $API->write('=password='.$cedula,false);
           $API->write('=service='."pppoe",false);
           $API->write('=profile='.$nombre_plan,true);
          
           $READ = $API->read(false);
           $ARRAY = $API->parseResponse($READ);
           
    }
   $API->disconnect();



}

function exonerar($ip, $cliente, $MK, $usermk, $passwordmk, $dmb_plan, $umb_plan, $parent, $id_srv){

    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {
        $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)==0){                                                             //en caso de que no exista lo agrega
            $API->write('/ip/firewall/address-list/add',false);
            $API->write('=list=ACTIVOS',false);
            $API->write('=address='.$ip,false);
            $API->write('=comment='.$cliente,true);
            $READ = $API->read(true);
        }
        $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
        $API->write('?name='.$cliente."(".$id_srv.")",true);
        $READ = $API->read(false);
        $ARRAY2 = $API->parseResponse($READ);

        if(count($ARRAY)==0){
            // aqui valida que no exista el cliente registrado en la list ay lo agrega
            $API->write("/queue/simple/add",false);
            $API->write('=target='.$ip,false);   // IP
            $API->write('=name='.$cliente."(".$id_srv.")",false);       // nombre
            $API->write('=max-limit='.$umb_plan."M". "/".$dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
            $API->write('=parent='.$parent,true);         // comentario
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);


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



        cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $ip)->where('accion', 'e')->delete();
    }
    $API->disconnect();
}

function activar_ip_pd($ip, $cliente, $MK, $usermk, $passwordmk, $dmb_plan, $umb_plan, $parent,$status,$id_soporte,$cliente2,$cedula,$nombre_plan){ 

    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {
        $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)==0){                                                             //en caso de que no exista lo agrega
            $API->write('/ip/firewall/address-list/add',false);
            $API->write('=list=ACTIVOS',false);
            $API->write('=address='.$ip,false);
            $API->write('=comment='.$cliente,true);
            $READ = $API->read(true);
        }

        $API->write('/ppp/secret/add',false);
        $API->write('=name='.$cliente2."(".$status.")",false);
        $API->write('=password='.$cedula,false);
        $API->write('=service='."pppoe",false);
        $API->write('=profile='.$nombre_plan,false);
        $API->write('=remote-address='.$ip,false);
        $API->write('=comment= Pendiente por Instalacion',false);
        $API->write('=local-address='.$MK,true);
    
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);



        cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'a_p_i')->delete();
    }
    $API->disconnect();
    return 120;
}

function activar_pppoe_pendiente($cliente,$cedula, $MK, $usermk, $passwordmk, $perfil,$estatus,$id_insta){

    $API = new RouterosAPI();
        if ($API->connect($MK,$usermk,$passwordmk)) {

            $API->write('/ppp/secret/add',false);
            $API->write('=name='.$cliente."(".$id_insta.")",false);
            $API->write('=password='.$cedula,false);
            $API->write('=service='."pppoe",false);
            $API->write('=comment= Pendiente por Instalacion',false);
            $API->write('=profile='.$perfil,true);
        

            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
        }
        $API->disconnect();
     return 120;
}

function retirar_ip_pd($ip, $cliente, $MK, $usermk, $passwordmk,$status,$id_soporte){
    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {                                  //se conecta y verifica si el cliente exista en la lista
   $API->write('/ip/firewall/address-list/print',false);
        $API->write('?list=ACTIVOS',false);
        $API->write('?disabled=false',false);
        $API->write('?address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)>0) {
            $API->write('/ip/firewall/address-list/remove', false); // en caso de existir lo eliminara
            $API->write('=.id=' . $ARRAY[0]['.id']);
            $READ = $API->read(false);
            //return $READ;
        }

        $API->write('/ppp/secret/print',false);
        $API->write('?remote-address='.$ip,true);
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);
        if(count($ARRAY)>0) {
            $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
            $API->write('=.id=' . $ARRAY[0]['.id']);
            $READ = $API->read(false);
            //return $READ;
        }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
            $API->write('/ppp/active/print',false);
            $API->write('?address='.$ip,true);
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            var_dump($ARRAY);
            
            if(count($ARRAY)>0) {
                $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                $API->write('=.id=' . $ARRAY[0]['.id']);
                $READ = $API->read(false);
                //return $READ;
            } 

        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
    }
    $API->disconnect();
}

function retirar_pppoe_pendiente($cliente, $MK, $usermk, $passwordmk,$status){
    //require('routeros_api.class.php');

    $API = new RouterosAPI();
    if ($API->connect($MK, $usermk, $passwordmk)) {                                  //se conecta y verifica si el cliente exista en la lista
    $API->write('/ppp/secret/print',false);
            $API->write('?name='.$cliente."(".$status.")",true);
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            if(count($ARRAY)>0) {
                $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                $API->write('=.id=' . $ARRAY[0]['.id']);
                $READ = $API->read(false);
                //return $READ;
            }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                $API->write('/ppp/active/print',false);
                $API->write('?name='.$cliente."(".$status.")",true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                var_dump($ARRAY);
                
                if(count($ARRAY)>0) {
                    $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                    $API->write('=.id=' . $ARRAY[0]['.id']);
                    $READ = $API->read(false);
                    //return $READ;
                }  
        }
        $API->disconnect();
}



function sendsms($numero, $mensaje) {

  $numero=urlencode($numero);
  $mensaje=urlencode($mensaje);
  $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);

  if (!$fp) {
    echo "ERROR: $errno - $errstr<br />\n";
  }
  else {
    fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
    fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".$numero." \"".$mensaje."\" ".rand()." \r\n\r\n");
    //while (!feof($fp)) 	echo fgets($fp, 1024);
    echo fread($fp, 4096);
    fclose($fp);

  }
}
function sendmailBalance($mensaje, $cliente, $user, $observaciones) {
    Mail::send('emails.NotifyBalance', [
        'fecha'=>date('d-m-Y'),
        'mensaje'=>$mensaje,
        'cliente'=>$cliente,
        'moroso'=>$user,
        'observaciones'=>$observaciones],function ($message)
    {
        //$message->subject('Notificacion-'.$cliente.'('.date('d-m-Y').')');
        $message->subject('INFORMACION IMPORTANTE');
        $message->from('no-responder@maraveca.com', 'Maraveca Telecomunicaciones');
        $message->bcc('jose.perozo@maraveca.com');
        $message->to($user->email);
        //$message->to("ana.reyes@maraveca.com");
        //$message->to('jesus.orono@maraveca.com');


    });
    //return $user;
}


function GenFac() {

  $clientes=DB::table('clientes')->get();
  foreach ($clientes as $cliente) {
    $servicios=DB::table('servicios')
    ->join('planes','planes.id_plan','=','servicios.plan_srv')
    ->where('servicios.cliente_srv','=',$cliente->id)
    ->get();
    $adicionales=fac_adic::where('id_cli', $cliente->id)->get();
    if (($servicios != null && $servicios != [])||($adicionales != null && $adicionales != [])) {
      $id=DB::table('fac_control')->insertGetId(
        [
          'id_cliente'=>$cliente->id,
          'fac_status'=>'1',
        ]
      );
      if($servicios != null && $servicios != []){
        foreach ($servicios as $key) {
        DB::table('fac_products')->insert(
          [
            'codigo_factura'=>$id,
            'codigo_articulo'=>$key->id_plan,
            'nombre_articulo'=>$key->name_plan,
            'precio_articulo'=>$key->cost_plan,
            'comment_articulo'=>$key->comment_srv

          ]
        );
      }
    }
      if($adicionales != null && $adicionales != []){
        foreach ($adicionales as $key) {
        DB::table('fac_products')->insert(
          [
            'codigo_factura'=>$id,
            'codigo_articulo'=>$key->codigo_articulo,
            'nombre_articulo'=>$key->nombre_articulo,
            'precio_unitario'=>$key->precio_unitario,
            'IVA'=>$key->IVA,
            'cantidad'=>$key->cantidad,
            'precio_articulo'=>$key->precio_articulo,
            'comment_articulo'=>$key->comment_srv

          ]
        );
      }
      }
      # code...
    }
  }

}
function revisarBalance_in($id){
    $facturacion = DB::select("SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
              where fac_controls.id_cliente = $id and fac_controls.fac_status = 1  ORDER BY created_at ASC;");//selecciono todas las facturas del cliente
    foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
        $balance = balance_clientes_in::where('bal_cli_in', '=', $id)->where('bal_rest_in', '>', 0)->where('bal_stat_in', 1)->get();

        foreach ($balance as $restante1) {

                $restante = $restante1->bal_rest_in;

            echo $factura->denominacion;
            if ($restante > 0) {

                    $deuda = round($factura->monto - $factura->pagado, 2);//calculo su deuda
                    if ($factura->monto > $factura->pagado) {//si no esta solvente
                        if ($deuda >= $restante) {//si la deuda es mayor o igual que el resto
                            fac_pago::create(['fac_id' => $factura->id, 'pag_tip' => $restante1->bal_tip_in, 'status' => '1', 'pag_monto' => $restante, 'pag_comment' => $restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//coloco todo el monto en un pago
                            $factura->pagado = +$factura->pagado + $restante;
                            $restante = 0;
                            revisar_pagado($factura->id);
                        } elseif ($deuda < $restante) {//si la deuda es menor que el resto
                            $restante=round(($restante-$deuda),2);//calculo lo que quedara
                            fac_pago::create(['fac_id' => $factura->id, 'pag_tip' => $restante1->bal_tip_in, 'status' => '1', 'pag_monto' => $deuda, 'pag_comment' => $restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//registro el pago con el monto de la deuda
                            $factura->monto = 0;
                            revisar_pagado($factura->id);
                        }
                    /*    if ($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16){
                            $restante = (+$restante * (float)$restante1->tasa);
                            echo $restante;
                        }*/

                        $up = balance_clientes_in::where('id_bal_in', '=', $restante1->id_bal_in);
                        $up->update(['bal_rest_in'=>$restante, 'uso_bal_in' => 1]);//acualizo lo que quedo
                    }


            }
        }
    }

}
function revisarBalance($id){
  //1
  $taza = configuracion::select('valor')->where('nombre','=',"taza")->get()->first();
  $facturacion=DB::select("SELECT fac_controls.*,
    (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
    (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
    where fac_controls.id_cliente = $id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas del cliente
    foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
      $balance=balance_cliente::where('bal_cli', '=', $id)->where('bal_stat', 1)->where('bal_rest', '>', 0)->get();
      foreach ($balance as $restante1) {
        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
          //return +$restante1->bal_rest * $taza;
          $restante = round(+$restante1->bal_rest * $taza->valor, 2);
          //echo $restante;
        }else{

          $restante=$restante1->bal_rest;
        }
        //echo $factura->denominacion;
        if($restante>0){
          if($factura->denominacion == 'BSF'){
            $restante=round(+$restante*100000, 2);
            $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
            if($factura->monto>$factura->pagado){//si no esta solvente
              if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                echo 'queda 0 \n';
                fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                $factura->pagado=+$factura->pagado+$restante;
                $restante=0;
              }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                echo 'pagable';
                $restante=+$restante-$deuda;//calculo lo que quedara
                fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                $factura->monto=0;

              }
              if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                $restante == round(+$restante / $taza, 2);
              }
              $restante=round(+$restante/100000, 2);
              $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
              $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
            }

          }elseif($factura->denominacion == 'Bs.S'){
            $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
            if($factura->monto>$factura->pagado){//si no esta solvente
              if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                $factura->pagado=+$factura->pagado+$restante;
                $restante=0;
              }elseif ($deuda<$restante) {//si la deuda es menor que el resto
               $restante=round(($restante-$deuda),2);//calculo lo que quedara
                fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                $factura->monto=0;
              }
              if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                //echo $restante;
                $restante = round((+$restante / $taza->valor), 2);
                echo $restante;
              }

              $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
              $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
            }

          }}
        }
      }
  //2





}
function revisar($id){


  $servicios=DB::table('servicios')  //buscamos los servicios
  ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*' )
  ->join('aps','aps.id','=','servicios.ap_srv')
  ->join('clientes','clientes.id','=','servicios.cliente_srv')
  ->join('planes','planes.id_plan','=','servicios.plan_srv')
  ->join('celdas','aps.celda_ap','=','celdas.id_celda')
  ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
  ->where('servicios.cliente_srv','=',$id)
  //->where('servicios.stat_srv','!=','3')
  ->where('servicios.stat_srv','!=','4')
  ->where('servicios.stat_srv','!=','5')
  ->groupBy('servicios.ip_srv')
  ->get();
  if ($servicios->count()  >= 1) { //en caso de que exista al menos un servicio
    $debe=0; //variable para contador
    $monto=0; //variable para contador
    $pagado=0; //variable para contador
    $facturas=DB::select(
      "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
        where fac_controls.id_cliente = $id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas


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
              //$cliente->deuda = $monto-$pagado;
          }


      }
      if ($debe >= 1) { //si debe 1 o mas, suspendera sus servicios



      }else{ //si no debe




        foreach ($servicios as $moroso) {

            $corte_prog = corte_prog::where('id_cliente', $id)
                ->where ('status','=','1' );
               $corte = $corte_prog->get();
            if ($corte_prog->count() >= 1 ) {
                corte_prog::where('id_cliente',$id )->where('status', '1')->update(['status'=>'3']);
                historico_cliente::create(['history'=>'Anulación de "corte programado por compromiso de pago", por pago de deuda', 'modulo'=>'Facturacion', 'cliente'=>$id, 'responsable'=>'0']);
            }

            if ((strtolower($moroso->kind) == 'g' || strtolower($moroso->kind) == 'j') && (strtolower($moroso->social) != 'null' && $moroso->kind != null)) {
                $cliente2= ucwords(strtolower($moroso->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente1 = str_replace($remp_cliente, $correct_cliente, $cliente2);
            }else {
                $cliente2= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente1 = str_replace($remp_cliente, $correct_cliente, $cliente2);
            }
            cola_de_ejecucion::create(['id_srv' => $moroso->id_srv, 'accion' => 'a', 'contador' => '1']);
            if ($moroso->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($moroso->carac_plan == 2) {

                $parent = "none";
            }
            $API = new RouterosAPI();

            if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
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
                $API->write("/queue/simple/getall", false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                $API->write('?name=' . $cliente1 . "(". $moroso->id_srv .")", true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);

                if (count($ARRAY) == 0) {
                    // aqui valida que no exista el cliente registrado en la list ay lo agrega
                    $API->write("/queue/simple/add", false);
                    $API->write('=target=' . $moroso->ip_srv, false);   // IP
                    $API->write('=name=' . $cliente1 . "(". $moroso->id_srv .")", false);       // nombre
                    $API->write('=max-limit=' . $moroso->umb_plan . "M" . "/" . $moroso->dmb_plan . "M", false);   //   2M/2M   [TX/RX]
                    $API->write('=parent=' . $parent, true);         // comentario
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);


                    if ($parent == "none") {                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                        $API->write('/queue/simple/getall', false);
                        $API->write('?name=' . $cliente1 . "(". $moroso->id_srv .")");
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
                $servicios1->update(["stat_srv" => 1]);
                historico_cliente::create(['history' => 'Servicio Activado', 'modulo' => 'Facturacion', 'cliente' => $moroso->cliente_srv, 'responsable' => '0']);
                cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'a')->delete();
                $API->disconnect();}
          $API->disconnect();
          //echo "solvente: ".$cliente."/".$moroso->ip_srv."\n";
        }
      }

}
}

function revisar_in($id)
{
    $servicios = DB::select("SELECT * FROM servicios as s 
                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                        INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                            WHERE s.stat_srv != 4 AND s.stat_srv != 5 AND s.cliente_srv = ?",[$id]);    

        foreach ($servicios as $s) {
            
            if ($s->tipo_srv == 1) {
                $s->tipoDeServicio = "inalamabrico";
                $tecnologia = DB::select("SELECT * FROM aps AS a
                                                INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                                INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                    WHERE a.id = ?",[$s->ap_srv])["0"];
                $s->ip_srvidor = $tecnologia->ip_srvidor;   
                $s->user_srvidor = $tecnologia->user_srvidor;
                $s->password_srvidor = $tecnologia->password_srvidor;                              
            } else {
                $s->tipoDeServicio = "alamabrico";
                $tecnologia = DB::select("SELECT * FROM caja_distribucion AS c
                                                INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                                INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                    WHERE c.id_caja = ?",[$s->ap_srv])["0"];

                $s->ip_srvidor = $tecnologia->ip_srvidor;   
                $s->user_srvidor = $tecnologia->user_srvidor;
                $s->password_srvidor = $tecnologia->password_srvidor;     
            }
            
        }
   
    if (count($servicios) >= 1) { //en caso de que exista al menos un servicio
        $debe = 0; //variable para contador
        $monto = 0; //variable para contador
        $pagado = 0; //variable para contador
        $facturas = DB::select(
            "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
        where fac_controls.id_cliente = $id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas
        foreach ($facturas as $factura) {
            if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL') {

                if ($factura->denominacion == '$') {
                    if ($factura->fac_status == 1) {
                        $monto = $monto + $factura->monto;
                        $pagado = $pagado + $factura->pagado;
                    }
                }
            } else {
                if ($factura->denominacion == '$') {
                    if ($factura->fac_status == 1) {
                        $monto = $monto + $factura->monto;
                        $pagado = $pagado + $factura->pagado;
                    }
                }
            }
            if ($factura->monto > $factura->pagado) { //reviso cuales estan pagadas
                $debe += 1;
                //$cliente->deuda = $monto-$pagado;
            }
        }
        if ($debe > 1) { //si debe 1 o mas, suspendera sus servicios
        } else { //si no debe

            foreach ($servicios as $moroso) {
                $corte_prog = DB::select("SELECT * FROM corte_progs WHERE id_cliente = ? AND status = 1",[$id]);
                  
                if (count($corte_prog) > 0 ) {
                    $actCorteProg = DB::update("UPDATE corte_progs SET status = 3 WHERE id_cliente = ? AND status = 1",[$id]);
                    historico_cliente::create(['history'=>'Anulación de "corte programado por compromiso de pago", por pago de deuda', 'modulo'=>'Facturacion', 'cliente'=>$id, 'responsable'=>'0']);
                }
                cola_de_ejecucion::create(['id_srv'=>$moroso->id_srv, 'accion'=>'a', 'contador'=>'1']);

               
                if( $moroso->kind == 'V'|| $moroso->kind =='E'){
                    $nombre4 = explode(" ",$moroso->nombre);
                    $apellido4 = explode(" ",$moroso->apellido);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
    
                    $cliente1= ucfirst($moroso->nombre)." ".ucfirst($moroso->apellido);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }else {
                    $cliente1= ucwords(strtolower($moroso->social));
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }


                cola_de_ejecucion::create(['id_srv' => $moroso->id_srv, 'accion' => 'a', 'contador' => '1']);
                if ($moroso->carac_plan == 1) {
                    $parent = "Asimetricos";
                } else if ($moroso->carac_plan == 2) {

                    $parent = "none";
                }
                $API = new RouterosAPI();

                if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
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
                    

                    $API->write('/ppp/secret/add',false);
                    $API->write('=name='.$cliente3."(".$moroso->id_srv.")",false);
                    $API->write('=password='.$moroso->dni,false);
                    $API->write('=service='."pppoe",false);
                    $API->write('=profile='.$moroso->name_plan,false);
                    $API->write('=remote-address='.$moroso->ip_srv,false);
                    $API->write('=local-address='.$moroso->ip_srvidor,true);
                
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);


                    $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
                    $servicios1->update(["stat_srv" => 1]);
                    historico_cliente::create(['history' => 'Servicio Activado', 'modulo' => 'Facturacion', 'cliente' => $moroso->cliente_srv, 'responsable' => '0']);
                    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'a')->delete();
                    $API->disconnect();

                }
            }

        }
    }
}

/*    $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
    $servicios1->update(["stat_srv"=>1]);
    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', 'a')->delete();
}
$API->disconnect();*/


function revisar_Balances($id)
{
    $facturacion=DB::select("SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
              where fac_controls.id_cliente = $id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas del cliente
    foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
        if ($factura->denominacion == '$'){
            $balance=balance_clientes_in::where('bal_cli_in', '=',$id)->where('bal_stat_in', 1)->where('bal_rest_in', '>', 0)->get();
        }else{
            $balance=balance_cliente::where('bal_cli', '=', $id)->where('bal_stat', 1)->where('bal_rest', '>', 0)->get();

        }
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        foreach ($balance as $restante1) {
            /*=====================================================================En caso de los balances en moneda nacional==============================================================*/
            if ($factura->denominacion != '$') { // para calcular el balance de facturas en bs
                if ($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11) {
                    //return +$restante1->bal_rest * $tasa;
                    $restante = (+$restante1->bal_rest * $tasa);
                    //echo $restante;
                } else {

                    $restante = $restante1->bal_rest;
                }


            }
            /*===================================================================================================================================*/

            /*=====================================================================En caso de los balances en moneda internacional==============================================================*/
      if ($factura->denominacion == '$') { // para calcular el balance de facturas en dolares
                            if ($restante1->bal_tip_in == 12 || $restante1->bal_tip_in == 13 || $restante1->bal_tip_in == 14 || $restante1->bal_tip_in == 16) {

                                $restante = $restante1->bal_rest_in;

                            } elseif (($restante1->bal_tip_in !=  12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16)&&$restante1->uso_bal_in ==1  ) {

                                $restante = $restante1->bal_rest_in;

                            }
                            else {
                                $restante = ((float)$restante1->bal_rest_in / (float)$restante1->tasa);

                            }
                        }
            /*===================================================================================================================================*/


            //echo $factura->denominacion;
            if($restante>0){
                if($factura->denominacion == 'BSF'){
                    $restante=round(+$restante*100000, 2);
                    $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                    if($factura->monto>$factura->pagado){//si no esta solvente
                        if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                            echo 'queda 0 \n';
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                            $factura->pagado=+$factura->pagado+$restante;
                            $restante=0;
                            revisar_pagado($factura->id);
                        }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                            echo 'pagable';
                            $restante=+$restante-$deuda;//calculo lo que quedara
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                            $factura->monto=0;
                            revisar_pagado($factura->id);
                        }
                        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                            $restante == round(+$restante / $tasa, 2);
                        }
                        $restante=round(+$restante/100000, 2);
                        $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                        $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                    }

                }elseif($factura->denominacion == 'Bs.S'){
                    $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                    if($factura->monto>$factura->pagado){//si no esta solvente
                        if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                            $factura->pagado=+$factura->pagado+$restante;
                            $restante=0;
                            revisar_pagado($factura->id);
                        }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                           $restante=round(($restante-$deuda),2);//calculo lo que quedara
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                            $factura->monto=0;
                            revisar_pagado($factura->id);
                        }
                        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                            //echo $restante;
                            $restante = round((+$restante / $tasa), 2);
                            echo $restante;
                            revisar_pagado($factura->id);
                        }

                        $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                        $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                    }

                }elseif($factura->denominacion == '$'){
                    $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                    if($factura->monto>$factura->pagado){//si no esta solvente
                        if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip_in, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//coloco todo el monto en un pago
                            $factura->pagado=+$factura->pagado+$restante;
                            $restante=0;
                            revisar_pagado($factura->id);
                        }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                           $restante=round(($restante-$deuda),2);//calculo lo que quedara
                            fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip_in, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//registro el pago con el monto de la deuda
                            $factura->monto=0;
                            revisar_pagado($factura->id);
                        }
                      /*if ($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16 ){
                                        $restante = (+$restante * (float)$restante1->tasa);
                                        echo $restante;
                                    }*/
                        $up = balance_clientes_in::where('id_bal_in','=', $restante1->id_bal_in);
                                    $up->update(['bal_rest_in'=>$restante, 'uso_bal_in' => 1]);//acualizo lo que quedo
                    }
                }



            }
        }
    }
    }

    function revisar_pagado($id){
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        $debe = 0; //variable para contador
        $monto = 0; //variable para contador
        $pagado = 0; //variable para contador
        $facturas=DB::select(
            "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
        where fac_controls.id= $id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");


        foreach ($facturas as $factura) {
            if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
                if ($factura->denominacion == 'BSF'){
                    if($factura->fac_status==1){
                        $monto=$monto+($factura->monto/100000);
                        $pagado=$pagado+($factura->pagado/100000);
                    }
                }elseif ($factura->denominacion == 'Bs' ||$factura->denominacion == '$') {
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
                }elseif ($factura->denominacion == 'Bs'||$factura->denominacion == '$') {
                    if($factura->fac_status==1){
                        $monto=$monto+$factura->monto;
                    }
                }
            }
            $debe= $factura->monto-$factura->pagado;
            if($factura->monto < $factura->pagado || $debe==0){ //reviso cuales estan pagadas


                fac_control::where('id', '=', $factura->id)->update(['tasa_pago'=>$tasa]);
            }




    }
    }


    function permisosMK($ip,$user_ser,$clave_ser,$user,$clave,$permiso){
        $API = new RouterosAPI();
        if ($API->connect($ip, $user_ser,$clave_ser)) {

            $API->write('/user/print',false);
            $API->write('?name='.$user,true);
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);

            if(count($ARRAY) > 0){
            $API->write('/user/remove', false); // en caso de existir lo eliminara
                    $API->write('=.id=' . $ARRAY[0]['.id']);
                    $READ = $API->read(false);

                    $API->write('/user/add',false); 
                    $API->write('=name='.$user,false);
                    $API->write('=password='.$clave,false);
                    $API->write('=group='.$permiso,true);      
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);     
            }else{
            
                $API->write('/user/add',false); 
                $API->write('=name='.$user,false);
                $API->write('=password='.$clave,false);
                $API->write('=group='.$permiso,true);      
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
            
            }

        }
        $API->disconnect();
    }


