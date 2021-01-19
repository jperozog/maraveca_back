<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\inventarios;
use App\zonas;
use App\transferencias_equipos;
use App\celdas;
use App\equipos;
use App\historico;
use Illuminate\Http\Request;

class InventariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventario = DB::select("SELECT `inventarios`.`model_inventario`, equipos.name FROM `inventarios` join equipos on equipos.id = inventarios.model_inventario group by inventarios.model_inventario");
        foreach ($inventario as $i) {
          $i->equipos = inventarios::select('inventarios.*', 'equipos.name', 'equipos.tipo' , 'zonas.nombre_zona')
          ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
          ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )
          ->where('model_inventario', $i->model_inventario)
          ->get();
        }
        /*inventarios::select('inventarios.*', 'equipos.name', 'zonas.nombre_zona')
        ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
        ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )->get();*/
        $zonas = $zonas = zonas::all();
        foreach ($zonas as $z) {
          $equipos=DB::select(
            "SELECT count(*) as numero, equipos.name, equipos.tipo, inventarios.zona_inventario
            FROM inventarios join equipos ON equipos.id = inventarios.model_inventario
            where inventarios.zona_inventario = $z->id
            GROUP by equipos.name, inventarios.zona_inventario
            order by equipos.tipo asc
            ");
          $z->equipos=$equipos;
        }
        return collect(['inventario'=>$inventario, 'zonas'=>$zonas]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create()
     {
         //
     }
     public function equipo(Request $request, $id)
     {
       if($id=='a'){
       $equipos=inventarios::select('inventarios.*', 'equipos.name', 'zonas.nombre_zona', 'zonas.routers')
       ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
       ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )
       ->where('equipos.tipo', '=', $request->e)
       ->where('inventarios.status', '=', '1')
       ->get();

       return $equipos;
     }else{
       $eq=[];
       $router=celdas::select('id_srvidor')
       ->join('servidores','servidores.id_srvidor','=','celdas.servidor_celda')
       ->where('celdas.id_celda', '=', $id)
       ->get()->first();
       $equipos=inventarios::select('inventarios.*', 'equipos.name', 'zonas.nombre_zona', 'zonas.routers')
       ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
       ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )
       ->where('equipos.tipo', '=', $request->e)
       ->where('inventarios.status', '=', '1')
       ->get();

       foreach ($equipos as $e) {
         $ab=explode(',', $e->routers);
         if(in_array($router->id_srvidor, $ab)){
           array_push($eq, $e);
         }
       }
       return $eq;
     }
   }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function PreloadInventarios(Request $request)
     {
       $equipos=equipos::all();
       $zonas=zonas::all();
       $result=collect(['equipos'=>$equipos, 'zonas'=>$zonas]);
       //return $result;
       return response()->json($result);
     }

     public function PreloadTransferencias(Request $request)
     {
       $transf = DB::select('select p.*, concat(r.nombre_user," " , r.apellido_user) as responsable_nombre, d.nombre_zona as nombre_desde, h.nombre_zona as nombre_hacia from transferencias_equipos as p join users as r on r.id_user = p.responsable join zonas as d on d.id = p.desde join zonas as h on h.id = p.hacia order by p.created_at desc');
       $inventario = inventarios::select('inventarios.*', 'equipos.name', 'zonas.nombre_zona')
       ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
       ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )
       ->get();
       $zonas=zonas::all();
       $result=collect(['inventarios'=>$inventario, 'zonas'=>$zonas, 'trans'=>$transf]);
       return response()->json($result);
     }

    public function store(Request $request)
    {
      $responsable = $request->responsable;
      unset($request['responsable']);
      foreach ($request->seriales as $key) {
        inventarios::create(['model_inventario'=>$request->model_inventario, 'serial_inventario'=>$key, 'zona_inventario'=>$request->zona_inventario, 'status'=>$request->status]);
      }
      historico::create(['responsable'=>$responsable, 'modulo'=>'Inventarios', 'detalle'=>'Nuevos equipos']);
      return 200;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\inventarios  $inventarios
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $eq=[];
        $router=celdas::select('id_srvidor')
        ->join('servidores','servidores.id_srvidor','=','celdas.servidor_celda')
        ->where('celdas.id_celda', '=', $id)
        ->get()->first();
        $equipos=inventarios::select('inventarios.*', 'equipos.name', 'zonas.nombre_zona', 'zonas.routers')
        ->join('equipos', 'equipos.id', '=', 'inventarios.model_inventario' )
        ->join('zonas', 'zonas.id', '=', 'inventarios.zona_inventario' )
        ->where('equipos.tipo', '=', '1')
        ->where('inventarios.model_inventario', '=', $request->e)
        ->where('inventarios.status', '=', '1')
        ->get();

        foreach ($equipos as $e) {
          $ab=explode(',', $e->routers);
          if(in_array($router->id_srvidor, $ab)){
            array_push($eq, $e);
          }
        }
        return $eq;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\inventarios  $inventarios
     * @return \Illuminate\Http\Response
     */
    public function edit(inventarios $inventarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inventarios  $inventarios
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, inventarios $inventarios)
    {
      $responsable = $request->responsable;
      $request=$request->all();
      unset($request['responsable']);
      $zonas = inventarios::findOrFail($request['id']);
      historico::create(['responsable'=>$responsable, 'modulo'=>'Inventarios', 'detalle'=>'Modifico Equipo']);
      $zonas->update($request);
      return $zonas;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inventarios  $inventarios
     * @return \Illuminate\Http\Response
     */
    public function destroy(inventarios $inventarios)
    {
        //
    }

    public function equiasig()
    {
        //return servicios::all();
        $result1=DB::table('servicios')
            ->select(\DB::raw('cliente_srv, clientes.kind, clientes.nombre, clientes.apellido,clientes.social,clientes.dni, equipos.name as name_equipo, serial_srv,stat_srv, aps.nombre_ap '))
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->join('equipos', 'equipos.id','=', 'servicios.equipo_srv')
           // ->join('aps', 'aps.id','=', 'servicios.ap_srv')
           // ->orderBy('clientes.nombre','ASC')
            ->orderByRaw(
                "CASE WHEN clientes.kind = 'V' OR clientes.kind = 'v' OR clientes.kind = 'E' OR clientes.kind = 'e' THEN clientes.nombre ELSE clientes.social END ASC"
            )
            ->get();
        $result=collect(['servicios'=>$result1]);
        return response()->json($result);

        /*$result=DB::table('soportes')  //inicio el query con sus join y orders
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
        //aqui termina*/

      /*  $result=collect(['servicios'=>$result1]);
        //return $result;
        return response()->json($result);*/
    }
}
