<?php

namespace App\Http\Controllers;
use App\corte_prog;
use DateTime;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\servicios;
use App\configuracion;
use App\clientes;
use App\ap;
use App\fac_control;
use App\fac_pago;
use App\fac_product;
use App\equipos;
use App\celdas;
use App\planes;
use App\historico_cliente;
use App\historico;
use App\User;
use App\cola_de_ejecucion;
use App\lista_ip;
use App\notify;
use App\pendiente_servi;
//use App\Mikrotik\RouterosAPI;

class ServiciosController extends Controller
{
    //
    public function index()
    {
        //return servicios::all();
        $result1=DB::table('servicios')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            //->orderBy('clientes.nombre','ASC')
            ->orderByRaw(
                "CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC"
            )
            ->get();

        /*$result2=DB::table('soportes')
        ->select('soportes.servicio_soporte', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.dni', 'clientes.social', 'aps_ins.value as ap', 'equipo_ins.value as equipo', 'ip_ins.value as ip', 'serial_ins.value as serial')
        ->join('clientes','clientes.id','=','soportes.servicio_soporte')
        ->join('tipos_soportes as aps_ins', function($join) {
          $join->on('aps_ins.fac_id','=','soportes.id_soporte')
          ->where('aps_ins.nombre', '=', 'ap');
        })
        ->join('tipos_soportes as equipo_ins', function($join) {
          $join->on('equipo_ins.fac_id','=','soportes.id_soporte')
          ->where('equipo_ins.nombre', '=', 'equipo');
        })
        ->join('tipos_soportes as ip_ins', function($join) {
          $join->on('ip_ins.fac_id','=','soportes.id_soporte')
          ->where('ip_ins.nombre', '=', 'ip');
        })
        ->join('tipos_soportes as serial_ins', function($join) {
          $join->on('serial_ins.fac_id','=','soportes.id_soporte')
          ->where('serial_ins.nombre', '=', 'serial');
        })
        ->where('tipo_soporte', '=', '1')
        ->where('status_soporte', '=', '2')
        ->orderBy('soportes.status_soporte','ASC')
        ->orderBy('soportes.updated_at','DSC')
        ->get();*/
        //aqui empieza
        $result=DB::table('soportes')  //inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, clientes.kind, clientes.nombre, clientes.apellido,clientes.social,clientes.dni, clientes.phone1,clientes.phone2,clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor'))
            ->join('tipos_soportes as celdas_ins', function($join) {
                $join->on('celdas_ins.fac_id','=','soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas','celdas_ins.value','=','celdas.id_celda')
            ->join('servidores','servidores.id_srvidor','=','celdas.servidor_celda')
            ->join('clientes','soportes.servicio_soporte','=','clientes.id')
            ->join('users','users.id_user','=','soportes.user_soporte')
            ->orderBy('soportes.status_soporte','ASC')
            ->orderBy('soportes.updated_at','DSC')
            ->where('soportes.tipo_soporte', '1')// solo instalaciones
            ->where('soportes.status_soporte', '2');//solo cerradas
        $inst = $result->get(); //envio el query y almaceno el retorno en $ins
        foreach ($inst as $items) { // para cada instalacion
            $result=DB::table('tipos_soportes') //busco los detalles
            ->select(\DB::raw('tipos_soportes.nombre, tipos_soportes.value'))
                ->where('fac_id','=',$items->id_soporte)
                ->get();
            if($items->status_soporte==1){
                $pi+=1;
            }
            $router=0;
            //$adicionales;
            foreach ($result as $item) {//para cada detalle
                global $router;
                if ($item->nombre == "equipo"){
                    $items->equipo=$item->value;
                }elseif ($item->nombre == "ipP"){
                    $items->ipP=$item->value;
                }elseif ($item->nombre == "ip"){
                    $items->ip=$item->value;
                }
                elseif ($item->nombre == "ap"){
                    $items->ap=$item->value;
                }elseif ($item->nombre == 'SerialEquipo') {
                    $items->serial= $item->value;
                }elseif ($item->nombre == 'ModeloEquipo') {
                    $items->equipo=$item->value;
                }elseif ($item->nombre == 'ModeloAntena') {
                    $items->antena=$item->value;
                } elseif ($item->nombre == 'direccion') {
                    $items->direccion=$item->value;
                }elseif ($item->nombre == 'tipo_plan') {
                    $items->tipo_plan_srv=$item->value;
                }elseif ($item->nombre == 'plan') {
                    $items->plan=$item->value;
                }
            };
        };
        //aqui termina

        $result=collect(['servicios'=>$result1, 'soportes'=>$inst]);
        //return $result;
        return response()->json($result);
    }

    public function show($id_srv)
    {
        //return servicios::find($id_srv);
        //return servicios::all()->where('id_srv','=',$id_srv);
        $result=DB::table('servicios')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('id_srv','=',$id_srv)
            ->get();
        return response()->json($result);
    }
    public function solicitar_ip($ip_srv)
    {

        $result=DB::table('lista_ips')
            ->select('lista_ips.*')
            -> where('lista_ips.ip','=',$ip_srv)
            ->orderBy('lista_ips.created_at','DSC')
            ->get();

        return response()->json( $result);
    }
    public function solicitar_serial($serial)
    {
        $result=DB::table('servicios')
            ->select('servicios.serial_srv')
            -> where('servicios.serial_srv','=',$serial)
            ->orderBy('servicios.created_at','DSC')
            ->get();
        //return $result;
        return response()->json($result);
    }
    public function add_preload(){
        $clientes=clientes::all();

        $equipos=equipos::all();

        $aps=ap::all();
        $user_comisiones= DB::table('users')
            ->where('users.comision','=','1')
            ->get();

        $planes=planes::all();
        $planesd = DB::table('planes')
            ->where('tipo_plan','=','3')
            ->get();
        $planes1= DB::table('planes')
            ->where('tipo_plan','!=','3')
            ->where('tipo_plan','!=','4')
            ->get();
        $planes2= DB::table('planes')
            ->where('tipo_plan','=','4')
            ->get();
        $planes3= DB::table('planes')
            ->where('tipo_plan','=','5')
            ->get();
        $planes6= DB::table('planes')
            ->where('tipo_plan','=','6')
            ->get(); 
        $planes7= DB::table('planes')
            ->where('tipo_plan','=','7')
            ->get();        
        $result=collect(['clientes'=>$clientes, 'equipos'=>$equipos, 'aps'=>$aps, 'planes'=>$planes, 'planesd'=>$planesd ,'planes1'=>$planes1, 'planes2'=>$planes2, 'planes3'=>$planes3,'planes6'=>$planes6,'planes7'=>$planes7,'user_comisiones'=>$user_comisiones]);

        return $result;

    }
    public function serv_cliente($id)
    {
        //return servicios::all();
        $result1=DB::table('servicios')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            //->orderBy('clientes.nombre','ASC')
            -> where('servicios.cliente_srv','=',$id)
            -> where('servicios.stat_srv','=',1)
            ->orderByRaw(
                "CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC"
            )
            ->get();

        /*$result2=DB::table('soportes')
        ->select('soportes.servicio_soporte', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.dni', 'clientes.social', 'aps_ins.value as ap', 'equipo_ins.value as equipo', 'ip_ins.value as ip', 'serial_ins.value as serial')
        ->join('clientes','clientes.id','=','soportes.servicio_soporte')
        ->join('tipos_soportes as aps_ins', function($join) {
          $join->on('aps_ins.fac_id','=','soportes.id_soporte')
          ->where('aps_ins.nombre', '=', 'ap');
        })
        ->join('tipos_soportes as equipo_ins', function($join) {
          $join->on('equipo_ins.fac_id','=','soportes.id_soporte')
          ->where('equipo_ins.nombre', '=', 'equipo');
        })
        ->join('tipos_soportes as ip_ins', function($join) {
          $join->on('ip_ins.fac_id','=','soportes.id_soporte')
          ->where('ip_ins.nombre', '=', 'ip');
        })
        ->join('tipos_soportes as serial_ins', function($join) {
          $join->on('serial_ins.fac_id','=','soportes.id_soporte')
          ->where('serial_ins.nombre', '=', 'serial');
        })
        ->where('tipo_soporte', '=', '1')
        ->where('status_soporte', '=', '2')
        ->orderBy('soportes.status_soporte','ASC')
        ->orderBy('soportes.updated_at','DSC')
        ->get();*/
        //aqui empieza
        $result=DB::table('soportes')  //inicio el query con sus join y orders
        ->select(\DB::raw('soportes.*, clientes.kind, clientes.nombre, clientes.apellido,clientes.social,clientes.dni, clientes.phone1,clientes.phone2,clientes.direccion, users.nombre_user, users.apellido_user, servidores.nombre_srvidor'))
            ->join('tipos_soportes as celdas_ins', function($join) {
                $join->on('celdas_ins.fac_id','=','soportes.id_soporte')
                    ->where('celdas_ins.nombre', '=', 'celda');
            })
            ->join('celdas','celdas_ins.value','=','celdas.id_celda')
            ->join('servidores','servidores.id_srvidor','=','celdas.servidor_celda')
            ->join('clientes','soportes.servicio_soporte','=','clientes.id')
            ->join('users','users.id_user','=','soportes.user_soporte')
            ->orderBy('soportes.status_soporte','ASC')
            ->orderBy('soportes.updated_at','DSC')
            ->where('soportes.tipo_soporte', '1')// solo instalaciones
            ->where('soportes.status_soporte', '2');//solo cerradas
        $inst = $result->get(); //envio el query y almaceno el retorno en $ins
        foreach ($inst as $items) { // para cada instalacion
            $result=DB::table('tipos_soportes') //busco los detalles
            ->select(\DB::raw('tipos_soportes.nombre, tipos_soportes.value'))
                ->where('fac_id','=',$items->id_soporte)
                ->get();
            if($items->status_soporte==1){
                $pi+=1;
            }
            $router=0;
            //$adicionales;
            foreach ($result as $item) {//para cada detalle
                global $router;
                if ($item->nombre == "equipo"){
                    $items->equipo=$item->value;
                }elseif ($item->nombre == "ip"){
                    $items->ip=$item->value;
                }elseif ($item->nombre == "ap"){
                    $items->ap=$item->value;
                }elseif ($item->nombre == 'SerialEquipo') {
                    $items->serial= $item->value;
                }elseif ($item->nombre == 'ModeloEquipo') {
                    $items->equipo=$item->value;
                }elseif ($item->nombre == 'ModeloAntena') {
                    $items->antena=$item->value;
                }
            };
        };
        //aqui termina

        $result=collect(['servicios'=>$result1, 'soportes'=>$inst]);
        //return $result;
        return response()->json($result);
    }

    public function store(Request $request)
    {
        //return $request;
        $pro=$request->pro;
        $serie_serv=$request->serie_serv;
        $responsable=$request->responsable;
        unset($request['pro']);
        unset($request['serie_serv']);
        unset($request['responsable']);
        $id = servicios::create($request->all());
        $result=DB::table('servicios')
            ->select('clientes.*', 'servidores.*', 'servicios.*', 'planes.*')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')

            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->join('celdas','aps.celda_ap','=','celdas.id_celda')
            ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
            ->where('id_srv','=',$id->id)
            ->first();
        if ($result->tipo_plan_srv==1){
            clientes::where('id','=',  $result->cliente_srv)->update(['tipo_planes' => 1]);

        }elseif($result->tipo_plan_srv==3){

            clientes::where('id','=',  $result->cliente_srv)->update(['tipo_planes' => 2]);
        }elseif($result->tipo_plan_srv=4 && $result->modo_pago_srv==1){

            clientes::where('id','=',  $result->cliente_srv)->update(['tipo_planes' => 4]);
        }elseif($result->tipo_plan_srv=4 && $result->modo_pago_srv==2){
            clientes::where('id','=',  $result->cliente_srv)->update(['tipo_planes' => 5]);

        }elseif($result->tipo_plan_srv=5){

            clientes::where('id','=',  $result->cliente_srv)->update(['tipo_planes' => 2]);
        }
        if($result->serie_srv= 1){
            clientes::where('id','=',  $result->cliente_srv)->update(['serie' => 1]);
        }

        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        if($pro==1){
            //aqui comienza el prorateo
            $iva=configuracion::where('nombre','=','iva')->first();
            $iva1=($iva->valor+100)/100;
            $denominacion=configuracion::where('nombre','=','denominacion')->first();
            $today = new Carbon();
            $start = new Carbon('first day of this month');
            $end = new Carbon('first day of next month');
            $lastDayOfThisMonth = new Carbon('last day of this month');
            $totalofdays=$end->diff($start)->format('%a');
            $nbOfDaysRemainingThisMonth =  $lastDayOfThisMonth->diff($today)->format('%a');
            $pastdays = $totalofdays - $nbOfDaysRemainingThisMonth;
            //echo "este mes tiene $totalofdays dias <br />";
            //echo "han transcurrido $pastdays dias <br />";
            //echo "aun faltan $nbOfDaysRemainingThisMonth dias para el proximo mes <br />";
            $month=date('n');
            $year=date('Y');
            $factura=fac_control::where('id_cliente', $request->cliente_srv)
                ->whereRaw('MONTH(created_at) = '.$month)
                ->whereRaw('YEAR(created_at) = '.$year)
                ->where('fac_status', 1)
                ->get()->first();

            /*if($serie_serv == 1 && isset($factura)){
              DB::table('fac_products')->insert(
                [
                  'codigo_factura'=>$factura->id,
                  'codigo_articulo'=>$result->id_plan,
                  'nombre_articulo'=>$result->name_plan,
                  'precio_articulo'=>round((($result->cost_plan*$iva1)/$totalofdays)*$nbOfDaysRemainingThisMonth, 2),
                  'IVA'=>$iva->valor,
                  'comment_articulo'=>$request->comment_srv,
                  "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                  "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                ]
              );
            }elseif ($serie_serv == 0 && isset($factura)) {
              DB::table('fac_products')->insert(
                [
                  'codigo_factura'=>$factura->id,
                  'codigo_articulo'=>$result->id_plan,
                  'nombre_articulo'=>$result->name_plan,
                  'precio_articulo'=>round((($result->cost_plan)/$totalofdays)*$nbOfDaysRemainingThisMonth, 2),
                  'IVA'=>$iva->valor,
                  'comment_articulo'=>$request->comment_srv,
                  "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                  "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                ]
              );
            }*/
            if(isset($factura) && ($result->serie_srv == 1 )){


                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>round((($result->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1, 2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        "precio_dl" =>round(((($result->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1)/$tasa, 2),

                    ]
                );


            }elseif (isset($factura) &&($result->serie_srv  == 0 && $result->tipo_plan_srv != 4 && $result->tipo_plan_srv != 3 && $result->tipo_plan_srv != 5)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura' => $factura->id,
                        'codigo_articulo' => $result->id_plan,
                        'nombre_articulo' => $result->name_plan,
                        'precio_articulo' => round((($result->taza / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                        'IVA' => $iva->valor,
                        'comment_articulo' => $request->comment_srv,
                        "created_at" => \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_dl' => round(((($result->cost_plan / $totalofdays) * $nbOfDaysRemainingThisMonth) / $tasa), 2),

                    ]
                );

            }elseif (isset($factura)&& ($result->serie_srv  == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv== 1)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura' => $factura->id,
                        'codigo_articulo' => $result->id_plan,
                        'nombre_articulo' => $result->name_plan,
                        'precio_articulo'=>round(((($result->taza)/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_dl'=>round((((($result->cost_plan+ (2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth)/$tasa), 2),

                    ]
                );

            } elseif (isset($factura)&&($result->serie_srv  == 0 && $result->tipo_plan_srv == 3)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura' => $factura->id,
                        'codigo_articulo' => $result->id_plan,
                        'nombre_articulo' => $result->name_plan,
                        'precio_articulo' => round((($result->taza / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                        'IVA' => $iva->valor,
                        'comment_articulo' => $request->comment_srv,
                        "created_at" => \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        "precio_bs" => round(((($result->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),

                    ]
                );

            } elseif (isset($factura)&&($result->serie_srv  == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv== 2)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura' => $factura->id,
                        'codigo_articulo' => $result->id_plan,
                        'nombre_articulo' => $result->name_plan,
                        'precio_articulo'=>round((($result->taza/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        "precio_bs" => round(((($result->taza/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),

                    ]
                );
            }
            //aqui termina el prorateo
        }else if($pro==2){
            //aqui comienza el reemplazo
            $iva=configuracion::where('nombre','=','iva')->first();
            $iva1=($iva->valor+100)/100;
            $denominacion=configuracion::where('nombre','=','denominacion')->first();
            $month=date('n');
            $year=date('Y');
            $factura=fac_control::where('id_cliente', $request->cliente_srv)
                ->whereRaw('MONTH(created_at) = '.$month)
                ->whereRaw('YEAR(created_at) = '.$year)
                ->where('fac_status', '1')
                ->get()->first();

            /* if($serie_serv == 1 && isset($factura)){
               DB::table('fac_products')->insert(
                 [
                   'codigo_factura'=>$factura->id,
                   'codigo_articulo'=>$result->id_plan,
                   'nombre_articulo'=>$result->name_plan,
                   'precio_articulo'=>round($result->cost_plan*$iva1, 2),
                   'IVA'=>$iva->valor,
                   'comment_articulo'=>$request->comment_srv,
                   "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                   "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                 ]
               );
             }elseif ($serie_serv== 0 && isset($factura)) {
               DB::table('fac_products')->insert(
                 [
                   'codigo_factura'=>$factura->id,
                   'codigo_articulo'=>$result->id_plan,
                   'nombre_articulo'=>$result->name_plan,
                   'precio_articulo'=>round($result->cost_plan, 2),
                   'IVA'=>$iva->valor,
                   'comment_articulo'=>$request->comment_srv,
                   "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                   "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                 ]
               );
             }*/
            if(isset($factura) && ($result->serie_srv == 1 && $result->tipo_plan_srv != 4)){


                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>round($result->cost_plan*$iva1, 2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        "precio_dl" =>round(($result->cost_plan*$iva1)/$tasa, 2),

                    ]
                );


            }elseif (isset($factura) &&($result->serie_srv == 1  && $result->tipo_plan_srv == 4 && $result->modo_pago_srv== 1)) {


                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>round(($result->cost_plan+ (2*$tasa))*$iva1, 2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        "precio_dl" =>round((($result->cost_plan+ (2*$tasa))*$iva1)/$tasa, 2),
                    ]
                );

            }elseif (isset($factura) &&($result->serie_srv  == 0 && $result->tipo_plan_srv != 4 && $result->tipo_plan_srv != 3)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>($result->cost_plan),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_dl'=>round((($result->cost_plan)/$tasa), 2),

                    ]
                );

            }elseif (isset($factura)&& ($result->serie_srv  == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv== 1)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>round(($result->cost_plan + (2*$tasa)),2),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_dl'=>round((($result->cost_plan + (2*$tasa))/$tasa),2),


                    ]
                );

            } elseif (isset($factura)&&($result->serie_srv  == 0 && $result->tipo_plan_srv == 3)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>($result->taza),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_bs' =>round(($result->taza *$tasa),2),

                    ]
                );

            } elseif (isset($factura)&&($result->serie_srv  == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv== 2)) {

                DB::table('fac_products')->insert(
                    [
                        'codigo_factura'=>$factura->id,
                        'codigo_articulo'=>$result->id_plan,
                        'nombre_articulo'=>$result->name_plan,
                        'precio_articulo'=>($result->taza ),
                        'IVA'=>$iva->valor,
                        'comment_articulo'=>$request->comment_srv,
                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        'precio_bs' =>round(($result->taza *$tasa),2),

                    ]
                );
            }

            //aqui termina el prorateo
        }

        // $mensaje='Se ha agregado a su facturacion mensual el plan: '.$result->name_plan.', el cual se le cobrara a partir de su proxima facturacion';
        // if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
        //   $cliente= ucwords(strtolower($result->social));
        //   $message= "MARAVECA: Srs. ".ucwords(strtolower($result->social)).", ".$mensaje;
        // }else {
        //   $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
        //   $message= "MARAVECA: Sr(a) ".ucfirst($result->nombre)." ".ucfirst($result->apellido).", ".$mensaje;
        // }
        // historico_cliente::create(['history'=>'Servicio nuevo: '.$result->name_plan, 'modulo'=>'Servicios', 'cliente'=>$id->cliente_srv, 'responsable'=>$responsable]);
        // historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Creo el servicio '.$result->name_plan.' para el cliente '.$cliente]);
        //
        // sendsms($result->phone1, $message);

        /*===================================================== para activar o exonerar cliente==================================================================================================*/
        if ($request->stat_srv==1 || $request->stat_srv==5) {
            if ((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null))
            {


                $cliente1= ucwords(strtolower($result->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }

            else {

                $cliente1= ucfirst($result->nombre) . " " . ucfirst($result->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            if ($request->stat_srv==1) {
                cola_de_ejecucion::create(['id_srv' => $result->id_srv, 'accion' => 'a', 'contador' => '1']);
            } elseif ($request->stat_srv==5){
                cola_de_ejecucion::create(['id_srv' => $result->id_srv, 'accion' => 'e', 'contador' => '1']);

            }

            if ($result->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($result->carac_plan == 2) {

                $parent = "none";
            }
            if ($request->stat_srv==1){
                activar($result->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor, $result->dmb_plan, $result->umb_plan, $parent, $result->id_srv);

            } elseif ($request->stat_srv==5){
                exonerar($result->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor, $result->dmb_plan, $result->umb_plan, $parent, $result->id_srv);
            }
            if ($request->stat_srv==1) {
                historico_cliente::create(['history' => 'Activo por inicio de servicio', 'modulo' => 'Servicios', 'cliente' => $result->cliente_srv, 'responsable' => $responsable]);
            }elseif ($request->stat_srv==5) {
                historico_cliente::create(['history' => 'Activo por inicio de servicio (exonerado)', 'modulo' => 'Servicios', 'cliente' => $result->cliente_srv, 'responsable' => $responsable]);
            }
            historico::create(['responsable' => $responsable, 'modulo' => 'Servicios', 'detalle' => 'Activa al cliente por inicio de servicio: ' . $cliente]);
            $adicionales = servicios::where('id_srv', $result->id_srv);
            $adicionales->update(['stat_srv' => 1]);

        }
        /*==========================================================================================================================================================================*/
        lista_ip::where('cliente_ip', $request->cliente_srv)->where('ip', $request->ip_srv)->update(['status_ip' => 1, 'ip_servicio'=>$id->id]);
        pendiente_servi::where('pendiente_servis.ip_pd', $request->ip_srv)->update(['status_pd' => 1]);

        $list_ip = lista_ip :: where('lista_ips.ip',$request->ip_srv)->get();

        if (count($list_ip)<=0) {
            $insert = new lista_ip;
            $insert->ip = $request->ip_srv;
            $insert->cliente_ip = $request->cliente_srv;
            $insert->status_ip = 1;
            $insert->save();
        }


        return $id;
    }

    public function update(Request $request, $id)
    {

        //$servicios = servicios::findOrFail($id);
        $pro=$request->pro;
        $responsable=$request->responsable;
        unset($request['responsable']);
        unset($request['pro']);
        $servicios = servicios::where('id_srv', '=', $id);
        $result=DB::table('servicios')
            ->select('clientes.*', 'planes.*', 'servidores.*', 'servicios.*')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->join('celdas','aps.celda_ap','=','celdas.id_celda')
            ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
            ->where('id_srv','=',$id)
            ->get();
        $result=$result->first();

        $newplan= planes::where('id_plan', '=', $request->plan_srv)->first();
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        if($result->plan_srv!=$request->plan_srv) {
            if ($pro == 1) {
                //aqui comienza el prorateo
                $iva = configuracion::where('nombre', '=', 'iva')->first();
                $iva1 = ($iva->valor + 100) / 100;
                $denominacion = configuracion::where('nombre', '=', 'denominacion')->first();
                $today = new Carbon();
                $start = new Carbon('first day of this month');
                $end = new Carbon('first day of next month');
                $lastDayOfThisMonth = new Carbon('last day of this month');
                $totalofdays = $end->diff($start)->format('%a');
                $nbOfDaysRemainingThisMonth = $lastDayOfThisMonth->diff($today)->format('%a');
                $pastdays = $totalofdays - $nbOfDaysRemainingThisMonth;
                //echo "este mes tiene $totalofdays dias <br />";
                //echo "han transcurrido $pastdays dias <br />";
                //echo "aun faltan $nbOfDaysRemainingThisMonth dias para el proximo mes <br />";
                $month = date('m');
                $year = date('Y');
                $factura = fac_control::where('id_cliente', $request->cliente_srv)
                    ->whereRaw('MONTH(created_at) = ' . $month)
                    ->whereRaw('YEAR(created_at) = ' . $year)
                    ->where('fac_status', '1')
                    ->get()->first();
                //return response()->json($factura);

                $producto_pre = fac_product::where('codigo_factura', $factura->id)
                   // ->where('codigo_articulo', $result->plan_srv)
                    //->where('comment_articulo', $result->comment_srv)
                    ->orderBy('precio_articulo', 'DESC');
                $producto = $producto_pre->get()->first();
                $precio_calculado = ($producto->precio_articulo / $totalofdays) * $pastdays;
                if (isset($producto)) {
                    $producto_pre->where('id', $producto->id)->update(['precio_articulo' => $precio_calculado]);
                }

                /* if($result->serie == 1 && isset($factura)){
                   DB::table('fac_products')->insert(
                     [
                       'codigo_factura'=>$factura->id,
                       'codigo_articulo'=>$newplan->id_plan,
                       'nombre_articulo'=>$newplan->name_plan,
                       'precio_articulo'=>round((($newplan->cost_plan*$iva1)/$totalofdays)*$nbOfDaysRemainingThisMonth, 2),
                       'IVA'=>$iva->valor,
                       'comment_articulo'=>$request->comment_srv,
                       "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                       "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                     ]
                   );
                 }elseif ($result->serie == 0 && isset($factura)) {
                   DB::table('fac_products')->insert(
                     [
                       'codigo_factura'=>$factura->id,
                       'codigo_articulo'=>$newplan->id_plan,
                       'nombre_articulo'=>$newplan->name_plan,
                       'precio_articulo'=>round((($newplan->cost_plan)/$totalofdays)*$nbOfDaysRemainingThisMonth, 2),
                       'IVA'=>$iva->valor,
                       'comment_articulo'=>$request->comment_srv,
                       "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                       "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                     ]
                   );
                 }*/

                /*if (isset($factura) && ($result->serie_srv == 1 && $result->tipo_plan_srv != 4)) {


                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->cost_plan / $totalofdays) * $nbOfDaysRemainingThisMonth) * $iva1, 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" => round(((($newplan->cost_plan / $totalofdays) * $nbOfDaysRemainingThisMonth) * $iva1) / $tasa, 2),

                        ]
                    );


                } elseif (isset($factura) && ($result->serie_srv == 1 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv == 1)) {


                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->cost_plan/ $totalofdays) * $nbOfDaysRemainingThisMonth) * $iva1, 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" => round((((($newplan->cost_plan ) / $totalofdays) * $nbOfDaysRemainingThisMonth) * $iva1) / $tasa, 2),

                        ]
                    );

                }*/
                if($result->serie == 1 && isset($factura)) {
                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->cost_plan * $iva1) / $totalofdays) * $nbOfDaysRemainingThisMonth, 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" => round((((($newplan->cost_plan ) / $totalofdays) * $nbOfDaysRemainingThisMonth) * $iva1) / $tasa, 2),
                            "precio_bs" => round(((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),
                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv != 4 && $result->tipo_plan_srv != 5 && $result->tipo_plan_srv != 3)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_dl' => round(((($newplan->cost_plan / $totalofdays) * $nbOfDaysRemainingThisMonth) / $tasa), 2),
                            "precio_bs" => round(((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),
                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && ($result->tipo_plan_srv == 4 || $result->tipo_plan_srv == 5) && $result->modo_pago_srv == 1)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round(((($newplan->taza) / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_dl' => round((((($newplan->cost_plan ) / $totalofdays) * $nbOfDaysRemainingThisMonth) / $tasa), 2),
                            "precio_bs" => round(((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),
                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv == 3)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_bs" => round(((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),
                            'precio_dl' => round((((($newplan->cost_plan ) / $totalofdays) * $nbOfDaysRemainingThisMonth) / $tasa), 2),
                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && ($result->tipo_plan_srv == 4||$result->tipo_plan_srv == 5) && $result->modo_pago_srv == 2)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth), 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_bs" => round(((($newplan->taza / $totalofdays) * $nbOfDaysRemainingThisMonth) * $tasa), 2),
                            'precio_dl' => round((((($newplan->cost_plan ) / $totalofdays) * $nbOfDaysRemainingThisMonth) / $tasa), 2),
                        ]
                    );
                }


                //aqui termina el prorateo
            } else if ($pro == 2) {
                //aqui comienza el reemplazo
                $iva = configuracion::where('nombre', '=', 'iva')->first();
                $iva1 = ($iva->valor + 100) / 100;
                $denominacion = configuracion::where('nombre', '=', 'denominacion')->first();
                $month = date('n');
                $year = date('Y');
                $factura = fac_control::where('id_cliente', $request->cliente_srv)
                    ->whereRaw('MONTH(created_at) = ' . $month)
                    ->whereRaw('YEAR(created_at) = ' . $year)
                    ->where('fac_status', '1')
                    ->get()->first();
                echo $factura->all();
                $producto_pre = fac_product::where('codigo_factura', $factura->id)
                    ->where('codigo_articulo', $result->plan_srv)
                    ->where('comment_articulo', $result->comment_srv)
                    ->orderBy('precio_articulo', 'DESC');
                $producto = $producto_pre->get()->first();
                if (isset($producto)) {
                    $producto_pre->where('id', $producto->id)->delete();
                }

                /* if($result->serie == 1 && isset($factura)){
                   DB::table('fac_products')->insert(
                     [
                       'codigo_factura'=>$factura->id,
                       'codigo_articulo'=>$newplan->id_plan,
                       'nombre_articulo'=>$newplan->name_plan,
                       'precio_articulo'=>round($newplan->cost_plan*$iva1, 2),
                       'IVA'=>$iva->valor,
                       'comment_articulo'=>$request->comment_srv,
                       "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                       "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                     ]
                   );
                 }elseif ($result->serie == 0 && isset($factura)) {
                   DB::table('fac_products')->insert(
                     [
                       'codigo_factura'=>$factura->id,
                       'codigo_articulo'=>$newplan->id_plan,
                       'nombre_articulo'=>$newplan->name_plan,
                       'precio_articulo'=>round($newplan->cost_plan, 2),
                       'IVA'=>$iva->valor,
                       'comment_articulo'=>$request->comment_srv,
                       "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                       "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                     ]
                   );
                 }*/
                if (isset($factura) && ($result->serie_srv == 1 && $result->tipo_plan_srv != 4)) {


                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round($newplan->cost_plan * $iva1, 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" => round(($newplan->cost_plan * $iva1) / $tasa, 2),
                            'precio_bs' => round(($newplan->taza * $tasa), 2),

                        ]
                    );


                } elseif (isset($factura) && ($result->serie_srv == 1 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv == 1)) {


                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => round(($newplan->cost_plan ) * $iva1, 2),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" => round((($newplan->cost_plan) * $iva1) / $tasa, 2),
                            'precio_bs' => round(($newplan->taza * $tasa), 2),

                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv != 4 && $result->tipo_plan_srv != 3)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => ($newplan->cost_plan),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_dl' => round((($newplan->cost_plan) / $tasa), 2),
                            'precio_bs' => round(($newplan->taza * $tasa), 2),

                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv == 1)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' =>  ($newplan->taza),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_dl' => round((($newplan->cost_plan ) / $tasa), 2),
                            'precio_bs' => round(($newplan->taza * $tasa), 2),


                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv == 3)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => ($newplan->taza),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_bs' => round(($newplan->taza * $tasa), 2),
                            'precio_dl' => round((($newplan->cost_plan ) / $tasa), 2),
                        ]
                    );

                } elseif (isset($factura) && ($result->serie_srv == 0 && $result->tipo_plan_srv == 4 && $result->modo_pago_srv == 2)) {

                    DB::table('fac_products')->insert(
                        [
                            'codigo_factura' => $factura->id,
                            'codigo_articulo' => $newplan->id_plan,
                            'nombre_articulo' => $newplan->name_plan,
                            'precio_articulo' => ($newplan->taza),
                            'IVA' => $iva->valor,
                            'comment_articulo' => $request->comment_srv,
                            "created_at" => \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            'precio_bs' => round(($newplan->taza * $tasa), 2),
                            'precio_dl' => round((($newplan->cost_plan ) / $tasa), 2),
                        ]
                    );
                }

                //aqui termina el prorateo
            }
            if ($request->tipo_plan_srv==1){
                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 1]);

            }elseif($request->tipo_plan_srv==3){

                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 2]);
            }elseif($request->tipo_plan_srv=4 && $request->modo_pago_srv==1){

                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 4]);
            }elseif($request->tipo_plan_srv=4 && $request->modo_pago_srv==2){
                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 5]);

            }elseif($request->tipo_plan_srv=5){

                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 2]);
            }
            if($request->serie_srv= 1){
                clientes::where('id','=',  $request->cliente_srv)->update(['serie' => 1]);
            } elseif($request->serie_srv= 0){
                clientes::where('id','=',  $request->cliente_srv)->update(['serie' => 0]);
            }

            /*===================================================== para cambiar planes===========================================================*/
            $newplan = planes::where('id_plan', '=', $request->plan_srv)->first();

            $mensaje = 'Se ha cambiado el plan de su servicio ' . $result->name_plan . ', ahora posee el plan ' . $newplan->name_plan;
            if ((strtolower($result->kind) == 'g' || strtolower($result->kind) == 'j') && (strtolower($result->social) != 'null' && $result->kind != null)) {
                $message = "MARAVECA: Srs. " . ucwords(strtolower($result->social)) . ", " . $mensaje;
                $cliente1 = ucwords(strtolower($result->social));
                $remp_cliente = array('ñ', 'Ñ');
                $correct_cliente = array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            } else {
                $cliente1 = ucfirst($result->nombre) . " " . ucfirst($result->apellido);
                $remp_cliente = array('ñ', 'Ñ');
                $correct_cliente = array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $message = "MARAVECA: Sr(a) " . ucfirst($result->nombre) . " " . ucfirst($result->apellido) . ", " . $mensaje;
            }
            cola_de_ejecucion::create(['id_srv' => $id, 'accion' => 'cp', 'contador' => '1']);

            if ($newplan->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($newplan->carac_plan == 2) {

                $parent = "none";
            }
            Cambiar_plan($result->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor, $newplan->dmb_plan, $newplan->umb_plan, $parent, $result->id_srv);

            historico_cliente::create(['history' => 'Cambio de plan: ' . $result->name_plan . ' ahora tiene ' . $newplan->name_plan, 'modulo' => 'Servicios', 'cliente' => $result->cliente_srv, 'responsable' => $responsable]);
            historico::create(['responsable' => $responsable, 'modulo' => 'Servicios', 'detalle' => 'Cambio de plan: ' . $result->name_plan . ' ahora tiene ' . $newplan->name_plan . '. Cliente ' . $cliente]);

            // sendsms($result->phone1, $message);


        } // finaliza el proceso en caso de que el plan sea diferente
        /*===================================================== para Editar Ip del cliente===========================================================*/
        if($result->ip_srv!=$request->ip_srv){

            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente1= ucwords(strtolower($result->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);

            }

            if ($newplan->carac_plan == 1 ) {
                $parent = "Asimetricos";
            } else if ($newplan->carac_plan ==2 )  {

                $parent = "none";
            }
            suspender($result->ip_srv, $cliente,$result->ip_srvidor, $result->user_srvidor, $result->password_srvidor,$result->id_srv);
            activar($request->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor , $result->dmb_plan, $result->umb_plan, $parent, $result->id_srv);

            historico_cliente::create(['history'=>'Cambio de ip: '.$result->ip_srv.' ahora tiene '.$request->ip_srv, 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Cambio de ip: '.$result->ip_srv.' ahora tiene '.$request->ip_srv.'. Cliente '.$cliente]);

            lista_ip::where('ip_servicio', $result->id_srv)->update(['ip' => $request->ip_srv]);


        }
        /*===================================================== para suspender cliente===========================================================*/
        if($request->stat_srv==3&&($request->stat_srv!=$result->stat_srv)){
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente1= ucwords(strtolower($result->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            cola_de_ejecucion::create(['id_srv'=>$id, 'accion'=>'s', 'contador'=>'1']);

            suspender($result->ip_srv, $cliente,$result->ip_srvidor, $result->user_srvidor, $result->password_srvidor,$result->id_srv);
            historico_cliente::create(['history'=>'Servicio Suspendido', 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
            $adicionales=servicios::where('ip_srv', $result->ip_srv);
            $adicionales->update(['stat_srv'=>3]);
            /*===================================================== para activar cliente===========================================================*/
        }else if ($request->stat_srv==1&&($request->stat_srv!=$result->stat_srv)) {
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente1= ucwords(strtolower($result->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            cola_de_ejecucion::create(['id_srv'=>$id, 'accion'=>'a', 'contador'=>'1']);

            if ($result->carac_plan == 1 ) {
                $parent = "Asimetricos";
            } else if ($result->carac_plan ==2 )  {

                $parent = "none";
            }
            activar($result->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor , $result->dmb_plan, $result->umb_plan, $parent, $result->id_srv);
            historico_cliente::create(['history'=>'Servicio Activado', 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Activa al cliente: '.$cliente]);
            $adicionales=servicios::where('id_srv', $id);
            $adicionales->update(['stat_srv'=>1]);
            /*===================================================== para exonerar cliente===========================================================*/
        }else if ($request->stat_srv==5&&($request->stat_srv!=$result->stat_srv)) {
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente1= ucwords(strtolower($result->social));
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                $remp_cliente= array('ñ', 'Ñ');
                $correct_cliente= array('n', 'N');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            cola_de_ejecucion::create(['id_srv'=>$id, 'accion'=>'e', 'contador'=>'1']);

            if ($result->carac_plan == 1 ) {
                $parent = "Asimetricos";
            } else if ($result->carac_plan ==2 )  {

                $parent = "none";
            }
            exonerar($result->ip_srv, $cliente, $result->ip_srvidor, $result->user_srvidor, $result->password_srvidor, $result->dmb_plan, $result->umb_plan, $parent, $result->id_srv );
            historico_cliente::create(['history'=>'Servicio Exonerado', 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Exonera al cliente: '.$cliente]);
            $adicionales=servicios::where('id_srv', $result->id_srv);
            $adicionales->update(['stat_srv'=>5]);
        }
        /*===================================================== para retirar cliente===========================================================*/
        else if($request->stat_srv==4&&($request->stat_srv!=$result->stat_srv)){
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente= ucwords(strtolower($result->social));
            }else {
                $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
            }
            cola_de_ejecucion::create(['id_srv'=>$id, 'accion'=>'r', 'contador'=>'1']);

            retirar($result->ip_srv, $cliente,$result->ip_srvidor, $result->user_srvidor, $result->password_srvidor,$result->id_srv);

            historico_cliente::create(['history'=>'Servicio Suspendido', 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
            $adicionales=servicios::where('ip_srv', $result->ip_srv);
            $adicionales->update(['stat_srv'=>4]);
        }else{
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                $cliente= ucwords(strtolower($result->social));
            }else {
                $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
            }
            historico_cliente::create(['history'=>'Servicio modificado', 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
            historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Modifica servicio del cliente: '.$cliente]);

        }
        if($result->modo_pago_srv!=$request->modo_pago_srv){
            if($request->tipo_plan_srv=4 && $request->modo_pago_srv==1){

                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 4]);
            }elseif($request->tipo_plan_srv=4 && $request->modo_pago_srv==2){
                clientes::where('id','=',  $request->cliente_srv)->update(['tipo_planes' => 5]);

            }
        }

        $servicios->update($request->except(['a_search']));

        /*  return response()->json($servicios);*/
        $result=collect(['ip_srv'=>$result->ip_srv, 'cliente' =>$cliente, 'ip_servidor' => $result->ip_srvidor, 'user_servidor'=> $result->user_srvidor, 'password_servidor'=> $result->password_srvidor]);
        //return $result;
        return response()->json($result);
    }

    public function delete(Request $request, $id)
    {
        $responsable=$request->responsable;
        unset($request['responsable']);
        $servicios = servicios::where('id_srv', '=', $id);
        $result=DB::table('servicios')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->join('celdas','aps.celda_ap','=','celdas.id_celda')
            ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
            ->where('id_srv','=',$id)
            ->first();
        $mensaje='Se ha retirado de su facturacion mensual el plan: '.$result->name_plan;
        if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
            $cliente= ucwords(strtolower($result->social));
            $message= "MARAVECA: Srs. ".ucwords(strtolower($result->social)).", ".$mensaje;
        }else {
            $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
            $message= "MARAVECA: Sr(a) ".ucfirst($result->nombre)." ".ucfirst($result->apellido).", ".$mensaje;
        }
        historico_cliente::create(['history'=>'Retiro de plan: '.$result->name_plan, 'modulo'=>'Servicios', 'cliente'=>$result->cliente_srv, 'responsable'=>$responsable]);
        historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Retiro de plan: '.$result->name_plan.'. Cliente '.$cliente]);

        // sendsms($result->phone1, $message);
        $servicios->delete();
lista_ip::where('ip', $result->ip_srv)->delete();
        return 204;

    }


    public function Prog_corte(Request $request)
    {

        $cliente=clientes::where('id','=',$request->cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        //$date = DateTime::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');
        
            corte_prog::create(['id_cliente' => $request->cliente, 'id_servicio'=>$request->nservicio, 'fecha' => $request->fecha, 'responsable' => $request->responsable, 'contador' => 0, 'status' => 1]);

        
        historico::create(['responsable'=>$request->responsable, 'modulo'=>'Servicios', 'detalle'=>'Programación de corte por compromiso de pago para la fecha'.$request->fecha.', al cliente '.$cli]);

        historico_cliente::create(['history'=>'Programación de corte por compromiso de pago para la fecha '.$request->fecha, 'modulo'=>'Servicios', 'cliente'=>$request->cliente, 'responsable'=>$request->responsable]);



        return response()->json($cliente);

    }
    public function anular_corte_prog(Request $request, $id)
    {
        $corte = corte_prog::findOrFail($id);
        $corte->update(['status'=>'3']);

        $responsable = $request[1];
        $cliente=clientes::where('id','=',$corte->id_cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        historico::create(['responsable'=>$responsable, 'modulo'=>'Servicios', 'detalle'=>'Anulacion de Programación de corte por compromiso de pago para el cliente '.$cli]);

        historico_cliente::create(['history'=>'Anulacion de Programación de corte por compromiso de pago', 'modulo'=>'Servicios', 'cliente'=>$corte->id_cliente, 'responsable'=>$responsable]);

        return 200;
    }




    }
