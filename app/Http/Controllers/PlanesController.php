<?php

namespace App\Http\Controllers;

use App\historico;
use App\historico_config_admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\planes;
use App\Permissions;
use App\configuracion;
use App\fac_product;

class PlanesController extends Controller
{
  public function index(Request $request)
  {
    //return $request;
    $permissions=Permissions::where('user', $request->responsable)->where('perm', 'planes_esp')->get();
    $planes = planes::all();
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
    $mb=[];
    if(count($permissions)>=1){
      $tt=0; $td=0; $ts=0; $tt_a=0; $td_a=0; $ts_a=0;$tt_d=0; $td_d=0; $ts_d=0;
      if($request->plan == "p"){
        $mb = DB::select('SELECT name_plan, carac_plan, count(servicios.id_srv) as cantidad,
        count(servicios.id_srv) * planes.dmb_plan as descarga,
        count(servicios.id_srv) * planes.umb_plan as subida from planes
        JOIN servicios on servicios.plan_srv = planes.id_plan
        where servicios.stat_srv = 1 and planes.dmb_plan is not null
        GROUP by servicios.plan_srv');
        foreach ($mb as $key) {
          $tt+=$key->cantidad;
          $td+=$key->descarga;
          $ts+=$key->subida;
          if($key->carac_plan == 1){
            $tt_a+=$key->cantidad;
            $td_a+=$key->descarga;
            $ts_a+=$key->subida;
          }elseif ($key->carac_plan == 2) {
            $tt_d+=$key->cantidad;
            $td_d+=$key->descarga;
            $ts_d+=$key->subida;
          }
        }
        if($tt_a>=1 && $tt_a!=$tt){
          array_push($mb, ['name_plan'=>'Total Asimetricos', 'cantidad'=>$tt_a, 'descarga'=>$td_a, 'subida'=>$ts_a]);
        }
        if($tt_d>=1 && $tt_d != $tt){
          array_push($mb, ['name_plan'=>'Total Dedicados', 'cantidad'=>$tt_d, 'descarga'=>$td_d, 'subida'=>$ts_d]);
        }
      }elseif ($request->plan == "r") {
        $mb = DB::select('SELECT servidores.nombre_srvidor as name_plan, COUNT(servicios.id_srv) as cantidad, sum(planes.dmb_plan) as descarga, sum(planes.umb_plan) as subida
        from servidores
        JOIN celdas on celdas.servidor_celda = servidores.id_srvidor
        JOIN aps ON aps.celda_ap = celdas.id_celda
        join servicios on servicios.ap_srv = aps.id
        join planes on planes.id_plan = servicios.plan_srv
        where servicios.stat_srv = 1 and planes.dmb_plan is not null
        GROUP BY servidores.id_srvidor');
        foreach ($mb as $key) {
          $tt+=$key->cantidad;
          $td+=$key->descarga;
          $ts+=$key->subida;
        }
      }elseif ($request->plan == "c") {

        $mb = DB::select('SELECT celdas.nombre_celda as name_plan, COUNT(servicios.id_srv) as cantidad, sum(planes.dmb_plan) as descarga, sum(planes.umb_plan) as subida
        from celdas
        JOIN aps ON aps.celda_ap = celdas.id_celda
        join servicios on servicios.ap_srv = aps.id
        join planes on planes.id_plan = servicios.plan_srv
        where servicios.stat_srv = 1 and planes.dmb_plan is not null
        GROUP BY celdas.id_celda');

        foreach ($mb as $key) {
          $tt+=$key->cantidad;
          $td+=$key->descarga;
          $ts+=$key->subida;
        }
      }


      array_push($mb, ['name_plan'=>'Total General', 'cantidad'=>$tt, 'descarga'=>$td, 'subida'=>$ts]);
    }
    //return $planes;
    return collect(['planes'=>$planes,'planesn'=>$planes1, 'planes2'=>$planes2,'mb'=>$mb, 'planesd' =>$planesd,'planes3'=>$planes3,'planes6'=>$planes6,'planes7'=>$planes7]);
    //return collect(['planes'=>$planes]);

  }

  public function traerPlanes($id ){
    
            $result = DB::select('SELECT * FROM planes WHERE tipo_plan =:id',
                 ["id"=>$id]);
                
                return response()->json($result);

  }

    public function updatePrice( Request $request ){
        $configuracion=configuracion::where('nombre','=',"taza");

        $tasa =$configuracion->first()->valor;
        $planes=planes::all();
        foreach ($planes as $plan) {
            if ($plan->taza != null && $plan->taza > 0){
                $monto = round($plan->taza * $request->taza,2);
                $planes = planes::where('id_plan','=',$plan->id_plan);
                $planes->update(["cost_plan"=>$monto]);
            }
        }
        historico_config_admin::create(['history' => 'Actualizacion del valor de la tasa de cambio de : '.number_format($tasa, 2, '.', ',').' BS.S'.' a '.number_format($request->taza, 2, '.', ',').' BS.S'.' desde el modulo de planes' , 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Actualizacion del valor de la tasa de cambio de : '.number_format($tasa, 2, '.', ',').' BS.S'.' a'.number_format($request->taza, 2, '.', ',').' BS.S'.' desde el modulo de planes' ]);
        $configuracion->update(["valor"=>$request->taza]);
        return 200;
    }


    public function show($id)
  {
    $result=DB::table('planes')
    ->where('id_plan','=',$id)
    ->get();
    return response()->json($result);
  }

  public function store(Request $request)
  {
    return planes::create($request->all());
  }

  public function update(Request $request, $id)
  {
    $taza=DB::table('configuracions')
    ->where('nombre','=','taza')
    ->get()->first();
    $taza=$taza->valor;
    $planes = planes::where('id_plan','=',$id);
    $tmp=$planes->get()->first();
    //return response()->json($request->taza*$taza);
    if($taza && $request->taza != $tmp->taza ){
      $request->cost_plan=$request->taza*$taza;
      //$request = collect(['cost_plan'=>$request->taza*$taza,'name_plan'=>$request->name_plan, 'taza'=>$request->taza, 'id_plan'=>$request->id_plan, 'tipo_plan'=>$request->tipo_plan]);
      //return response()->json($request);
    }
    return $planes->update($request->all());


  }

  public function delete(Request $request, $id)
  {
    $planes = planes::where('id_plan','=',$id);
    $planes->delete();

    return 204;
  }
}
