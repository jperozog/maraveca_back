<?php

namespace App\Http\Controllers;

use App\FacPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\historico;
use App\historico_cliente;

class FacPromoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promociones = DB::select("SELECT * FROM promociones");

        return response()->json($promociones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = DB::select("SELECT * FROM fac_promo AS f INNER JOIN clientes AS c ON f.id_cliente_p = c.id");

        foreach ($result as $cliente) {
            $result2 = DB::select("SELECT * FROM servicios WHERE cliente_srv = ? ",[$cliente->id])[0];

            $result3 = DB::update("UPDATE fac_promo SET id_servicio_p = ? WHERE id_promo = ?",[$result2->id_srv,$cliente->id_promo]);
        }

       return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $fecha = Carbon::createFromTimestamp($request->input("fecha"))->toDateTimeString();
        $fecha2 = date("Y-m-d H:i:s");
        $comentario = "Promocion ".$request->comentario;
       $result = DB::insert('INSERT INTO fac_promo(promocion,id_cliente_p,id_servicio_p,id_plan_p,fecha,comentario,responsable,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)',[$request->comentario,$request->id,$request->servicio,$request->plan,$fecha,"nuevo proceso",$request->id_user,1,$fecha2,$fecha2]);

       historico_cliente::create(['history'=>'Creacion de Promocion para Cliente', 'modulo'=>'Facturacion', 'cliente'=>$request->id, 'responsable'=>$request->id_user]);
       
       return response()->json($request);
    }


    public function traerDatosCliente($id){

        $result = DB::select("SELECT c.id, c.nombre, c.apellido,c.social,s.*,p.* FROM clientes AS c 
                                    INNER JOIN servicios AS s ON c.id = s.cliente_srv
                                    INNER JOIN planes AS p ON  s.plan_srv = p.id_plan
                                          WHERE id= ?",[$id]);

        return response()->json($result);
    }

    public function traerPlanesPromo($id){
        
        $result = DB::select("SELECT * FROM planes WHERE tipo_plan = ?",[$id]);

        return response()->json($result);
    }

    public function verificarPromo($id){

        $result = DB::select("SELECT * FROM fac_promo AS f
                                INNER JOIN promociones AS pr on f.promocion = pr.id_promocion
                                INNER JOIN planes AS p ON f.id_plan_p = p.id_plan
                                INNER JOIN users AS u ON f.responsable = u.id_user 
                                     WHERE f.id_servicio_p = ? AND f.status = 1",[$id]);

        $result != [] ? $result["existe"] = 1 : $result["existe"] = 0;

         

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FacPromo  $facPromo
     * @return \Illuminate\Http\Response
     */
    public function show(FacPromo $facPromo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FacPromo  $facPromo
     * @return \Illuminate\Http\Response
     */
    public function edit(FacPromo $facPromo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FacPromo  $facPromo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FacPromo $facPromo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FacPromo  $facPromo
     * @return \Illuminate\Http\Response
     */
    public function destroy(FacPromo $facPromo)
    {
        //
    }
}
