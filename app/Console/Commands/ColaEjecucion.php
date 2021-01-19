<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mikrotik\RouterosAPI;
use App\cola_de_ejecucion;
use App\servicios;

class ColaEjecucion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ColaEjecucion';

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
      $servicios=cola_de_ejecucion::select('cola_de_ejecucions.*', 'clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
      ->join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')
      ->join('aps','aps.id','=','servicios.ap_srv')
      ->join('clientes','clientes.id','=','servicios.cliente_srv')
      ->join('planes','planes.id_plan','=','servicios.plan_srv')
      ->join('celdas','aps.celda_ap','=','celdas.id_celda')
      ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
      //->where('servidores.id_srvidor', '!=', '11')
      ->get();

        $retirar_mk=cola_de_ejecucion::select('cola_de_ejecucions.*', 'clientes.*', 'servidores.*','planes.*')
            ->join('pendiente_servis', 'pendiente_servis.soporte_pd', '=', 'cola_de_ejecucions.soporte_pd')
            ->join('clientes','clientes.id','=','pendiente_servis.cliente_pd')
            ->join('planes','planes.id_plan','=','pendiente_servis.plan_pd')
            ->join('celdas','celdas.id_celda','=','pendiente_servis.celda_pd')
            ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
            ->get();


      foreach ($servicios as $moroso) {
          /*===================================================== para activar cliente===========================================================*/
        if($moroso->accion == 'a'){
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
              $API->write('=comment='.$cliente,true);
              $READ = $API->read(true);
              //return $READ;
            }
              $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
              $API->write('?name='.$cliente."(".$moroso->id_srv.")",true);
              $READ = $API->read(false);
              $ARRAY2 = $API->parseResponse($READ);

              if(count($ARRAY)==0){
                  // aqui valida que no exista el cliente registrado en la list ay lo agrega
                  $API->write("/queue/simple/add",false);
                  $API->write('=target='.$moroso->ip_srv,false);   // IP
                  $API->write('=name='.$cliente."(".$moroso->id_srv.")",false);       // nombre
                  $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                  $API->write('=parent='.$parent,true);         // comentario
                  $READ = $API->read(false);
                  $ARRAY = $API->parseResponse($READ);


                  if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                      $API->write('/queue/simple/getall', false);
                      $API->write('?name='.$cliente."(".$moroso->id_srv.")");
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
            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', $moroso->accion)->delete();
          }else{
            cola_de_ejecucion::where('id_srv', $moroso->id_srv)->where('accion', $moroso->accion)->update(['contador'=>$moroso->contador+=1]);
          }
          $API->disconnect();
            /*===================================================== para suspender cliente===========================================================*/
        }else if($moroso->accion == 's'){
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
            $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
            $servicios1->update(["stat_srv"=>3]);
            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', $moroso->accion)->delete();
          }else{
            cola_de_ejecucion::where('id_srv', $moroso->id_srv)->where('accion', $moroso->accion)->update(['contador'=>$moroso->contador+=1]);
          }
          $API->disconnect();
            /*===================================================== para retirar cliente===========================================================*/
        }else if($moroso->accion == 'r'){
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

            $servicios1 = servicios::where('ip_srv', $moroso->ip_srv);
            $servicios1->update(["stat_srv"=>4]);
            cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', $moroso->accion)->delete();
          }else{
            cola_de_ejecucion::where('id_srv', $moroso->id_srv)->where('accion', $moroso->accion)->update(['contador'=>$moroso->contador+=1]);
          }
          $API->disconnect();
            /*===================================================== para exonerar cliente===========================================================*/
        }if($moroso->accion == 'e'){
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
                      $API->write('=comment='.$cliente,true);
                      $READ = $API->read(true);
                      //return $READ;
                  }
                  $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                  $API->write('?name='.$cliente."(".$moroso->id_srv.")",true);
                  $READ = $API->read(false);
                  $ARRAY2 = $API->parseResponse($READ);

                  if(count($ARRAY)==0){
                      // aqui valida que no exista el cliente registrado en la list ay lo agrega
                      $API->write("/queue/simple/add",false);
                      $API->write('=target='.$moroso->ip_srv,false);   // IP
                      $API->write('=name='.$cliente."(".$moroso->id_srv.")",false);       // nombre
                      $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                      $API->write('=parent='.$parent,true);         // comentario
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);


                      if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                          $API->write('/queue/simple/getall', false);
                          $API->write('?name='.$cliente."(".$moroso->id_srv.")");
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
                  cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', $moroso->accion)->delete();
              }else{
                  cola_de_ejecucion::where('id_srv', $moroso->id_srv)->where('accion', $moroso->accion)->update(['contador'=>$moroso->contador+=1]);
              }
              $API->disconnect();

          /*===================================================== para Cambio de planes===========================================================*/
          } if($moroso->accion == 'cp'){
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
              if ($moroso->carac_plan == 1 ) {
                  $parent = "Asimetricos";
              } else if ($moroso->carac_plan ==2 )  {

                  $parent = "none";
              }
          $API = new RouterosAPI();
              if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
              $API->write('/ip/firewall/address-list/print',false); // aqui veo si el cliente existe en la lista de activos
              $API->write('?list=ACTIVOS',false);
              $API->write('?disabled=false',false);
              $API->write('?address='.$moroso->ip_srv,true);
              $READ = $API->read(false);
              $ARRAY = $API->parseResponse($READ);
              if(count($ARRAY)>0){

                  $API->write("/queue/simple/getall",false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                  $API->write('?name='.$cliente."(".$moroso->id_srv.")",true);
                  $READ = $API->read(false);
                  $ARRAY = $API->parseResponse($READ);

                  if(count($ARRAY)>0) {                                                                //  valida que  exista el cliente registrado en la lista y lo edita
                      $API->write("/queue/simple/set", false);
                      $API->write('=.id=' . $ARRAY[0]['.id'], false);
                      $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                      $API->write('=parent='.$parent,true);         // comentario
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);
                  }else{
                      // aqui valida que no exista el cliente registrado en la lista y lo agrega
                      $API->write("/queue/simple/add",false);
                      $API->write('=target='.$moroso->ip_srv,false);   // IP
                      $API->write('=name='.$cliente."(".$moroso->id_srv.")",false);       // nombre
                      $API->write('=max-limit='.$moroso->umb_plan."M". "/".$moroso->dmb_plan."M" ,false);   //   2M/2M   [TX/RX]
                      $API->write('=parent='.$parent,true);         // comentario
                      $READ = $API->read(false);
                      $ARRAY = $API->parseResponse($READ);

                  }
                  if ($parent=="none"){                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                      $API->write('/queue/simple/getall', false);
                      $API->write('?name='.$cliente."(".$moroso->id_srv.")");
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
              cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $moroso->ip_srv)->where('accion', $moroso->accion)->delete();
          }else{
              cola_de_ejecucion::where('id_srv', $moroso->id_srv)->where('accion', $moroso->accion)->update(['contador'=>$moroso->contador+=1]);
          }
          $API->disconnect();

      }
        /*===================================================== para activar servicios por intalar MK===========================================================*/
        foreach ($retirar_mk as $moroso) {
        if($moroso->accion == 'a_p_i') {
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
            if ($moroso->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($moroso->carac_plan == 2) {

                $parent = "none";
            }
            $status = "P_I";
            $API = new RouterosAPI();
            if ($API->connect($moroso->ip_srvidor, $moroso->user_srvidor, $moroso->password_srvidor)) {
                $API->write('/ip/firewall/address-list/print', false);
                $API->write('?list=ACTIVOS', false);
                $API->write('?disabled=false', false);
                $API->write('?address=' . $moroso->ip_pd, true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                if (count($ARRAY) == 0) {

                    $API->write('/ip/firewall/address-list/add', false);
                    $API->write('=list=ACTIVOS', false);
                    $API->write('=address=' . $moroso->ip_pd, false);
                    $API->write('=comment=' . $cliente, true);
                    $READ = $API->read(true);
                    //return $READ;
                }
                $API->write("/queue/simple/getall", false);  // aqui comienza el proceso de agregar el cliente activo en la lista queue, validando que no exista
                $API->write('?name=' . $cliente . "(" . $status . ")", true);
                $READ = $API->read(false);
                $ARRAY2 = $API->parseResponse($READ);

                if (count($ARRAY) == 0) {
                    // aqui valida que no exista el cliente registrado en la lista y lo agrega
                    $API->write("/queue/simple/add", false);
                    $API->write('=target=' . $moroso->ip_pd, false);   // IP
                    $API->write('=name=' . $cliente . "(" . $status . ")", false);       // nombre
                    $API->write('=max-limit=' . $moroso->umb_plan . "M" . "/" . $moroso->dmb_plan . "M", false);   //   2M/2M   [TX/RX]
                    $API->write('=parent=' . $parent, true);         // comentario
                    $READ = $API->read(false);
                    $ARRAY = $API->parseResponse($READ);


                    if ($parent == "none") {                                               // en esta seccion si el cliente es simetrico (parent= none) lo mueve de posicion "0"
                        $API->write('/queue/simple/getall', false);
                        $API->write('?name=' . $cliente . "(" . $status . ")");
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


                cola_de_ejecucion::where('soporte_pd', $moroso->soporte_pd)->where('accion', $moroso->accion)->delete();
            } else {
                cola_de_ejecucion::where('soporte_pd', $moroso->soporte_pd)->where('accion', $moroso->accion)->update(['contador' => $moroso->contador += 1]);
            }
            $API->disconnect();
            /*===================================================== para desactivar servicios por intalar MK===========================================================*/
        }if($moroso->accion == 'r_p_i'){
                if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
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



                cola_de_ejecucion::where('soporte_pd', $moroso->soporte_pd)->where('accion', $moroso->accion)->delete();
            } else {
                cola_de_ejecucion::where('soporte_pd', $moroso->soporte_pd)->where('accion', $moroso->accion)->update(['contador' => $moroso->contador += 1]);
            }
                $API->disconnect();

            }

    }
    }
}
