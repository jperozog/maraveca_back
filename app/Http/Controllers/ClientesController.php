<?php

namespace App\Http\Controllers;
use App\corte_prog;
use App\fac_prog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\soportes;
use App\servicios;
use App\clientes;
use App\historico_cliente;
use App\historico;
use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_adic;

class ClientesController extends Controller
{
  //
  public function index()
  {
    //return clientes::all()->orderBy('clientes.email','ASC');
    $result=DB::table('clientes')
    ->orderBy('clientes.created_at','DSC')
    ->get();
    //return $result;
    return response()->json($result);
  }

  public function index1($id)
  {
    $permisoSeniat = DB::select("SELECT * FROM permissions WHERE user = ? AND perm = 'seniat'",[$id]);

    if (count($permisoSeniat) > 0) {
      
      $result= DB::select("SELECT * FROM clientes WHERE serie = 1 ORDER BY created_at DESC");
    } else {
      $result=DB::table('clientes')
    ->orderBy('clientes.created_at','DSC')
    ->get();
    }
    

    $clientes=array();
    $pendientes=array();
    
    foreach ($result as $cliente) {
      $servicio=DB::table('servicios')
      ->where('cliente_srv','=',$cliente->id)
      ->get();
      $ns=$servicio->count();
      $cliente->servicios=$ns;
      if($ns>0){
        array_push($clientes, $cliente);
      }else{
        
        //$instalacionesPendientes = DB::select("SELECT * FROM instalaciones WHERE cliente_insta = ? AND (status_insta = 1 OR status_insta = 2) AND YEAR(created_at) >= 2020",[$cliente->id]);

        //if(count($instalacionesPendientes) > 0) {
          array_push($pendientes, $cliente);
        //}
       
  
      }
    }
    
    $index = collect(['clientes'=>$clientes,'pendientes'=>$pendientes]);
    return response()->json($index);
  }

  public function show($id)
  {
    return clientes::find($id);
  }
  public function overview($id)
  {
    //datos del cliente

    $cliente = clientes::find($id);
    //fin datos del cliente
    //comienzo facturacion
    $facturacion = DB::select(
      "SELECT fac_controls.*,
       (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
       (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
       (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
       (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
       (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
       (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
       (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
       where fac_controls.id_cliente = ".$id." ORDER BY created_at DESC;");

       //fin facturacion_w
       //comienzo tickets
       $adicionales = fac_adic::where('id_cli', $id)->get();
       $soportes=DB::table('soportes')
       ->select('soportes.*', 'users.nombre_user', 'users.apellido_user')
       ->join('servicios','servicios.id_srv','=','soportes.servicio_soporte')
       ->join('clientes','servicios.cliente_srv','=','clientes.id')
       ->join('users','users.id_user','=','soportes.user_soporte')
       ->where('clientes.id', '=', $id)
       ->where('tipo_soporte', '=', '2')
       ->orderBy('soportes.created_at', 'DSC')
       ->get();

       //fin ticket
       //comienzo de servicios
      

      $balance = DB::select("SELECT * FROM balance_clientes
                                            WHERE bal_cli = ? AND bal_tip != 20
                                              ORDER BY created_at DESC",[$id]);    


       //balance solo para pagos/deudas de planes internacionales 10/08/19
      $balance_in = DB::select("SELECT * FROM balance_clientes_ins
                                   WHERE bal_cli_in = ? AND bal_tip_in != 20
                                      ORDER BY created_at DESC",[$id]);


      $exoneraciones = DB::select("SELECT * FROM balance_clientes
                                WHERE bal_cli = ? AND bal_tip = 20
                                  ORDER BY created_at DESC",[$id]);    


      //exoneraciones clientes sin facturar
      $exoneraciones_in = DB::select("SELECT * FROM balance_clientes_ins
                                  WHERE bal_cli_in = ? AND bal_tip_in = 20
                                    ORDER BY created_at DESC",[$id]);




       $servicios = [];     
       /*
       $servicios=DB::table('servicios')
       ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan','aps.nombre_ap', 'aps.ip_ap', 'equipos2.nombre_equipo' )
       ->join('clientes','clientes.id','=','servicios.cliente_srv')
       ->join('aps','aps.id','=','servicios.ap_srv')
       ->join('equipos2','equipos2.id_equipo','=','servicios.equipo_srv')
       ->join('planes','planes.id_plan','=','servicios.plan_srv')
       ->where('clientes.id','=',$id)
       ->get();
      */
       $servicios1 = DB::select("SELECT * FROM servicios WHERE cliente_srv = ?",[$id]);


      
       foreach ($servicios1 as $ser) {
              if($ser->tipo_srv ==1 ){
                $servicio = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            INNER JOIN aps AS a ON s.ap_srv = a.id
                                            INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo
                                            INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                              WHERE s.id_srv = ?",[$ser->id_srv])["0"];
                
                $compromisoServicio = DB::select("SELECT * FROM compromisos_servicios WHERE id_servicio_com = ? AND status = 1",[$ser->id_srv]);

                if (count($compromisoServicio) > 0) {
                    $servicio->compromisoActivo = 1;
                }else{
                    $servicio->compromisoActivo = 0;
                }
                
                array_push($servicios,$servicio);
              }else{
                $servicio = DB::select("SELECT * FROM servicios AS s
                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            INNER JOIN caja_distribucion AS caj ON s.ap_srv = caj.id_caja
                                            INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo
                                            INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                              WHERE s.id_srv = ?",[$ser->id_srv])["0"];
                                              
                 $compromisoServicio = DB::select("SELECT * FROM compromisos_servicios WHERE id_servicio_com = ? AND status = 1",[$ser->id_srv]);

                 if (count($compromisoServicio) > 0) {
                     $servicio->compromisoActivo = 1;
                 }else{
                     $servicio->compromisoActivo = 0;
                 }

                array_push($servicios,$servicio);
              }

       }
       
       //fin de servicios
       //historico de movimiento del cliente
       $hist=historico_cliente::where('cliente', $id)
       ->select('historico_clientes.*', 'users.nombre_user', 'users.apellido_user')
       ->leftjoin('users','users.id_user','=','responsable')
       ->orderBy('created_at', 'DSC')
       ->get();
       //fin de historico de cliente

      // datos completos de las taablas clientes y servicios
      $srv_cli=DB::table('clientes')
          ->select('servicios.id_srv', 'clientes.*' )
          ->join('servicios','servicios.cliente_srv','=' ,'clientes.id')
          ->where('clientes.id','=',$id)
          ->get();
      $srv = json_decode($srv_cli);

      $fac_prog = DB::table('fac_progs')
          ->select('fac_progs.*','clientes.*', 'users.nombre_user', 'users.apellido_user','fac_progs.id as id_fac_prog')
          ->join('users','users.id_user','=','fac_progs.responsable')
          ->join('clientes','clientes.id','=', 'fac_progs.id_cliente')
          ->where('fac_progs.id_cliente', $id)
          ->where ('status','=','1' )
          ->get();
      $cort_prog = DB::table('corte_progs')
          ->select('corte_progs.*','clientes.*', 'users.nombre_user', 'users.apellido_user', 'planes.name_plan', 'corte_progs.id as id_prog')
          ->join('users','users.id_user','=','corte_progs.responsable')
          ->join('clientes','clientes.id','=', 'corte_progs.id_cliente')
          ->join('servicios','servicios.id_srv','=','corte_progs.id_servicio')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->where('corte_progs.id_cliente', $id)
          ->where ('status','=','1' )
          ->get();


      //fin datos completos de las taablas clientes y servicios
       $index = collect([
         'cliente'=>$cliente,
         'soporte'=>$soportes,
         'facturacion'=>$facturacion ,
         'servicios'=>$servicios,
         'history'=>$hist,
         'balance' => $balance,
         'balance_in'=>$balance_in,
         'exoneraciones' => $exoneraciones,
         'exoneraciones_in'=>$exoneraciones_in,
         'adicionales'=>$adicionales,
           'srv_cli' => $srv,
           'fac_prog'=>$fac_prog,
           'corte_prog' =>$cort_prog

          ]);
       return $index;
  }

  public function notificar(Request $request)
  {
  //return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8/*este ultimo numero es el limite*/);
    historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Notificacion Personal']);
    historico_cliente::create(['history'=>'Notificacion Personal', 'modulo'=>'Facturacion', 'cliente'=>$request->id, 'responsable'=>$request->responsable]);
  \Artisan::call('Mensajes:personales', [
    'id' => $request->id
]);
  return 200;
  }

  public function store(Request $request)
  {
    $responsable = $request->responsable;
    unset($request['responsable']);
    if((strtolower($request->kind)=='g'||strtolower($request->kind)=='j')&&(strtolower($request->social)!= 'null' && $request->kind != null)){
      $cliente= ucwords(strtolower($request->social));
    }else {
      $cliente= ucfirst($request->nombre)." ".ucfirst($request->apellido);
    }
    $id = clientes::create($request->all());
    historico_cliente::create(['history'=>'Nuevo Cliente: '.$cliente, 'modulo'=>'Clientes', 'cliente'=>$id->id, 'responsable'=>$responsable]);
    historico::create(['responsable'=>$responsable, 'modulo'=>'Clientes', 'detalle'=>'Registro cliente '.$cliente]);
    return $id;

      $oinstalaciones = oinstall::where('ticket', $request->prueba );
      $oinstalaciones->update(['status_pgo'=>2]);

  }

  public function update(Request $request, $id)
  {
    $responsable = $request->responsable;
    unset($request['responsable']);
    $clientes = clientes::findOrFail($id);
    $tmp = $clientes->first();
    if((strtolower($request->kind)=='g'||strtolower($request->kind)=='j')&&(strtolower($request->social)!= 'null' && $request->kind != null)){
      $cliente= ucwords(strtolower($request->social));
    }else {
      $cliente= ucfirst($request->nombre)." ".ucfirst($request->apellido);
    }

      historico_cliente::create(['history'=>'Actualizacion de datos', 'modulo'=>'Clientes', 'cliente'=>$id, 'responsable'=>$responsable]);
      historico::create(['responsable'=>$responsable, 'modulo'=>'Clientes', 'detalle'=>'Cambio datos al cliente '.$cliente]);




      $clientes->update($request->all());
    return $clientes;
  }

  public function delete(Request $request, $id)
  {
    return $request->responsable;
    $clientes = clientes::findOrFail($id);
    $servicios = servicios::where('cliente_srv', $id);
    foreach ($servicios->get() as $servicio) {
      $soportes = soportes::where('');
    }
    $responsable = $request->responsable;
    unset($request['responsable']);
    if(isset($responsable)){
      $tmp = $clientes->first();
      if((strtolower($request->kind)=='g'||strtolower($request->kind)=='j')&&(strtolower($request->social)!= 'null' && $request->kind != null)){
        $cliente= ucwords(strtolower($request->social));
      }else {
        $cliente= ucfirst($request->nombre)." ".ucfirst($request->apellido);
      }

        historico::create(['responsable'=>$responsable, 'modulo'=>'Clientes', 'detalle'=>'Borro al cliente '.$cliente]);

    }

    $clientes->delete();

    return 204;
  }

  public function facturables(){

  }


  public function activos(){
    $listaclientes = [];
    $listaclientes = DB::table('servicios')
                    ->select('clientes.kind','clientes.dni','clientes.nombre', 'clientes.apellido', 'clientes.social','planes.name_plan', 'servicios.*', 'planes.cost_plan')
                    ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
                    ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                    ->where('stat_srv', '=', '1')
                    ->get();
    $listaclientes1 = DB::table('servicios')
                        ->select('clientes.nombre', 'clientes.apellido', 'clientes.social','planes.name_plan', 'servicios.*', 'planes.cost_plan')
                        ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
                        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                        ->where('stat_srv', '=', '1')
                        ->get();
  // $listaclientes = [$listaclientes];

    // $activos = DB::table('clientes')
    //            ->select('servicios.*', 'planes.name_plan')
    //            ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
    //            ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
    //            ->where('stat_srv', '=', '1')
    //            ->get();
    // return->response
    //return response()->json($listaclientes);
    return view('activos',
    ['lista'=>$listaclientes, 'lista1' => $listaclientes1]);
  }
  public function act_tipoplan(Request $request,$id)
  {
      $clientes = clientes::where('id', '=', $id);
      $serviciosF = DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.serie_srv', '=', '1')

          ->get();
      $serviciosNF = DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.serie_srv', '=', '0')

          ->get();
      $servicios = DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.tipo_plan_srv', '!=', '4')

          ->get();
      $serviciosb = DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.tipo_plan_srv', '=', '1')

          ->get();
      $serviciosd = DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.tipo_plan_srv', '=', '3')

          ->get();
      $serviciosbn= DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.tipo_plan_srv', '=', '4')
          ->orWhere('servicios.tipo_plan_srv', '=', '5')
          ->where('servicios.modo_pago_srv', '=', '1')
          ->get();
      $serviciosdn= DB::table('servicios')//buscamos los servicios
      ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
          ->where('servicios.cliente_srv', '=', $id)
          ->where('servicios.stat_srv', '!=', '4')
          ->where('servicios.tipo_plan_srv', '=', '4')
          ->orWhere('servicios.tipo_plan_srv', '=', '5')
          ->where('servicios.modo_pago_srv', '=', '2')
          ->get();



          if (count($serviciosd) < 1 && count($serviciosb) >= 1 ) {
              $clientes->update(['tipo_planes' => 1]); //planes antiguos

          } elseif (count($serviciosb) < 1 && count($serviciosd) >= 1) {
              $clientes->update(['tipo_planes' => 2]); // planes exclusivos en dolares

          } elseif (count($servicios) > 1) {
              $clientes->update(['tipo_planes' => 3]); //planes ambos planes (antiguos y dolares)


          } elseif (count($serviciosdn) < 1){
              $clientes->update(['tipo_planes' => 4]); // planes nuevos en bs

      } elseif (count($serviciosbn) < 1){
              $clientes->update(['tipo_planes' => 5]); // planes nuevos en dolares


          }

          if (count($serviciosF)==1  ){
              $clientes->update(['serie' => 1]);

          }
          elseif (count($serviciosNF)>count($serviciosF)){
              $clientes->update(['serie' => 0]);

          }


      return 15200;


      }



}
