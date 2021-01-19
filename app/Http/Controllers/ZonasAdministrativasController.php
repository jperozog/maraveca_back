<?php

namespace App\Http\Controllers;

use App\zonas_Administrativas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ZonasAdministrativasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result =  DB::select('SELECT * FROM zonas_administrativas');

        return response()->json($result);
    }


    public function traerDatosZona($id){

        if($id== 100){
            $result = DB::select('SELECT c.kind, c.dni, r.* , c.nombre, c.apellido, m.nombre_metodo, users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN clientes as c ON r.cliente = c.id
                                    INNER JOIN users ON r.responsable = users.id_user
                                    INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE r.estatus_registro = 1 ORDER BY r.id_registro DESC');
        }else{

        $result = DB::select('SELECT c.kind, c.dni, r.* , c.nombre, c.apellido, m.nombre_metodo, users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN clientes as c ON r.cliente = c.id
                                INNER JOIN users ON r.responsable = users.id_user
                                INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                     WHERE z.id_zonas_admi = ? AND r.estatus_registro = 1 ORDER BY r.id_registro DESC',[$id]);
        }

        return response()->json($result);

    }


    public function busqueda($dato,$zona){

        if($zona == 100){
            $result = DB::select('SELECT c.kind, c.dni, r.* , c.nombre, c.apellido,CONCAT(c.nombre," ",c.apellido) AS nombreCompleto, m.nombre_metodo, users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN clientes as c ON r.cliente = c.id
                                    INNER JOIN users ON r.responsable = users.id_user
                                    INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE r.estatus_registro = 1 AND (c.dni LIKE ? OR CONCAT(c.nombre," ",c.apellido) LIKE ?) ORDER BY r.id_registro DESC',["%".$dato."%","%".$dato."%"]);
        }else{

        $result = DB::select('SELECT c.kind, c.dni, r.* , c.nombre, c.apellido,CONCAT(c.nombre," ",c.apellido) AS nombreCompleto, m.nombre_metodo, users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN clientes as c ON r.cliente = c.id
                                INNER JOIN users ON r.responsable = users.id_user
                                INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                     WHERE z.id_zonas_admi = ? AND (c.dni LIKE ?  OR CONCAT(c.nombre," ",c.apellido) LIKE ?) AND r.estatus_registro = 1 ORDER BY r.id_registro DESC',[$zona,"%".$dato."%","%".$dato."%"]);
        }

        return response()->json($result);

    }







    public function traerDatosUser($id){

        $result = DB::select('SELECT c.kind, c.dni, r.* , c.nombre, c.apellido, m.nombre_metodo, users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN clientes as c ON r.cliente = c.id
                                    INNER JOIN users ON r.responsable = users.id_user
                                    INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE u.id_user = ? AND r.estatus_registro = 1',[$id]);

        return response()->json($result);

    }

    public function traerContadorEfectivo($id){

        if($id== 100){
            $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE metodo_pago = 14 AND r.estatus_registro = 1',[$id]);
        }else{

        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE metodo_pago = 14 AND z.id_zonas_admi = ? AND r.estatus_registro = 1',[$id]);
        }                                
          return response()->json($result);                               
    }

    public function traerContadorTransferencia($id){

        if($id== 100){
            $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
            INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
            INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                WHERE (metodo_pago = 14 OR metodo_pago = 1 OR metodo_pago = 2 OR metodo_pago = 3 OR metodo_pago = 6) AND r.estatus_registro = 1',[$id]);
        }else{

        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE (metodo_pago = 1 OR metodo_pago = 2 OR metodo_pago = 3 OR metodo_pago = 6) AND z.id_zonas_admi = ? AND r.estatus_registro = 1',[$id]);
        }                                
          return response()->json($result);                               
    }

    public function traerContadorZelle($id){
        if($id== 100){
            $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
            INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
            INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                    WHERE metodo_pago = 12 AND r.estatus_registro = 1',[$id]);
        }else{
        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE metodo_pago = 12 AND z.id_zonas_admi = ? AND r.estatus_registro = 1',[$id]);
        }
          return response()->json($result);       
          
    }

    public function traerUsersAdministrativos($id){
        if($id== 100){
            $result = DB::select('SELECT users.id_user,users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN users ON r.responsable = users.id_user
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        GROUP BY users.id_user',[$id]);
        }else{

        $result = DB::select('SELECT users.id_user,users.nombre_user, users.apellido_user FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN users ON r.responsable = users.id_user
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE z.id_zonas_admi = ? GROUP BY users.id_user',[$id]);
        }
        return response()->json($result);      
    }



    public function traerContadorEfectivoUser($id){
        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE metodo_pago = 14 AND u.id_user = ? AND r.estatus_registro = 1',[$id]);
          return response()->json($result);                               
    }

    public function traerContadorTransferenciaUser($id){
        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                    INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                    INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE (metodo_pago = 1 OR metodo_pago = 2 OR metodo_pago = 3 OR metodo_pago = 6) AND u.id_user = ? AND r.estatus_registro = 1',[$id]);
          return response()->json($result);                               
    }

    public function traerContadorZelleUser($id){
        $result = DB::select('SELECT SUM(monto) as suma FROM registro_pagos AS r
                                INNER JOIN users_administrativos AS u ON r.responsable = u.id_user
                                INNER JOIN zonas_administrativas AS z ON u.id_zona_admi = z.id_zonas_admi
                                        WHERE metodo_pago = 12 AND u.id_user = ? AND r.estatus_registro = 1',[$id]);
          return response()->json($result);                               
    }

    public function hacerCierreCaja( request $request){

        $registros = $request->movimientos;
        $id_cierre = rand();
        foreach ($registros as $re) {
           $id_re = $re["id_registro"];

           $result = DB::update('UPDATE registro_pagos SET estatus_registro = 2 WHERE id_registro = ?',[$id_re]);

           $result2 = DB::update('INSERT INTO cierre_caja (id_cierre,pago,estatus) VALUES (?,?,?)',[$id_cierre,$id_re,1]);
        }

        return response()->json($result2);
    }

    public function traerUltimoCierre(){
        $result = DB::select("SELECT c.id_cierre, c.cierre_fecha, c.estatus, u.nombre_user, u.apellido_user  FROM `cierre_caja` as c
                                INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                INNER JOIN users as u ON r.responsable = u.id_user 
                                    GROUP BY id_cierre ORDER BY id DESC LIMIT 1");

        return response()->json($result);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\zonas_Administrativas  $zonas_Administrativas
     * @return \Illuminate\Http\Response
     */
    public function show(zonas_Administrativas $zonas_Administrativas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\zonas_Administrativas  $zonas_Administrativas
     * @return \Illuminate\Http\Response
     */
    public function edit(zonas_Administrativas $zonas_Administrativas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\zonas_Administrativas  $zonas_Administrativas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, zonas_Administrativas $zonas_Administrativas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\zonas_Administrativas  $zonas_Administrativas
     * @return \Illuminate\Http\Response
     */
    public function destroy(zonas_Administrativas $zonas_Administrativas)
    {
        //
    }
}
