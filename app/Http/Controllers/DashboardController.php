<?php

namespace App\Http\Controllers;

use App\dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $result = [];
        $month = date('d-m-Y', strtotime('-1 month'));
        
        $y=date('Y', strtotime($month));
        $m=date('n', strtotime($month));

        $objeto = new \stdClass();
        $resultado = DB::select('SELECT COUNT(id_soporte) AS cerrados FROM soportes WHERE tipo_soporte = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND status_soporte = 2',[$m,$y]);
        $resultado2 = DB::select('SELECT COUNT(id_soporte) AS abiertos FROM soportes WHERE tipo_soporte = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND status_soporte = 1',[$m,$y]);
        $objeto->cerrados =$resultado[0]->cerrados;
        $objeto->abiertos =$resultado2[0]->abiertos;

        array_push($result,$objeto);
        return response()->json($result);
    }

    public function traerDatosGraficaClientesInactivos(){
        $mes = date('d-m-Y', strtotime('0 month'));
        $y=date('Y', strtotime($mes));
        $m=date('n', strtotime($mes));

        $result = [];
        $clientes = DB::select("SELECT COUNT(id) AS clientes FROM clientes WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?",[$m,$y])[0]->clientes;
        $potenciales = DB::select("SELECT COUNT(id) AS potenciales FROM pclientes WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND id_cli IS NULL",[$m,$y])[0]->potenciales;

        
        $objeto = new \stdClass();
        $objeto->clientes = $clientes;
        $objeto->potenciales = $potenciales;
        array_push($result,$objeto);
        return response()->json($result);
    }

    public function traerDatosGraficaClientesActivos(){

        $result = [];
        $suspendidos = DB::select("SELECT COUNT(id_srv) AS conServicio FROM servicios WHERE stat_srv = 3")[0]->conServicio;
        $activos = DB::select("SELECT COUNT(id_srv) AS activos FROM servicios WHERE stat_srv = 1")[0]->activos;
        $retirados = DB::select("SELECT COUNT(id_srv) AS retirados FROM servicios WHERE stat_srv = 4")[0]->retirados;
        $exonerados = DB::select("SELECT COUNT(id_srv) AS exonerados FROM servicios WHERE stat_srv = 5")[0]->exonerados;

        $objeto = new \stdClass();
        $objeto->activos = $activos;
        $objeto->suspendidos = $suspendidos;
        $objeto->exonerados = $exonerados;
        $objeto->retirados = $retirados;
        array_push($result,$objeto);
        return response()->json($result);
    }

    public function traerDatosGraficaFactibilidades(){
        $result = [];
        $month = date('d-m-Y', strtotime('-1 month'));
        $y=date('Y', strtotime($month));
        $m=date('n', strtotime($month));
        $objeto = new \stdClass();
        $resultado = DB::select('SELECT COUNT(id) AS pendientes FROM factibilidades WHERE status = 1 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND factible IS NULL',[$m,$y]);
        $resultado2 = DB::select('SELECT COUNT(id) AS noFactibles FROM factibilidades WHERE status = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND factible = 2',[$m,$y]);
        $resultado3 = DB::select('SELECT COUNT(id) AS Factibles FROM factibilidades WHERE status = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND factible = 1',[$m,$y]);
        $resultado4 = DB::select('SELECT COUNT(id) AS Visita FROM factibilidades WHERE status = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND factible = 3',[$m,$y]);
        $resultado5 = DB::select('SELECT COUNT(id) AS Coordenadas FROM factibilidades WHERE status = 2 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND factible = 4',[$m,$y]);
        $objeto->pendientes =$resultado[0]->pendientes;
        $objeto->noFactibles =$resultado2[0]->noFactibles;
        $objeto->Factibles =$resultado3[0]->Factibles;
        $objeto->Visita =$resultado4[0]->Visita;
        $objeto->Coordenadas =$resultado5[0]->Coordenadas;
        //$objeto->abiertos =$resultado2[0]->abiertos;

        array_push($result,$objeto);
        return response()->json($result);
    }

    public function traerDatosGraficaInstalaciones(){
        $result = [];
        $month = date('d-m-Y', strtotime('-1 month'));
        $y=date('Y', strtotime($month));
        $m=date('n', strtotime($month));
        $objeto = new \stdClass();
        $resultado = DB::select('SELECT COUNT(id_soporte) AS cerrados FROM soportes WHERE tipo_soporte = 1 AND MONTH(created_at) >= ? AND YEAR(created_at) = ? AND (status_soporte = 2 OR status_soporte = 4)',[$m,$y]);
        $resultado2 = DB::select('SELECT COUNT(id_insta) AS abiertos FROM instalaciones WHERE MONTH(created_at) >= ? AND YEAR(created_at) = ? AND status_insta = 1',[$m,$y]);
        $objeto->cerrados =$resultado[0]->cerrados;
        $objeto->abiertos =$resultado2[0]->abiertos;

        array_push($result,$objeto);
        return response()->json($result);
    }

    public function traerDatosGraficaOperaciones(){
        $datos=[];
        $datos2=[];
        $fechas=[];
        $month=[];
        $month[0] = date('d-m-Y', strtotime('0 month'));
        $month[1] = date('d-m-Y', strtotime('-1 month'));
        $month[2] = date('d-m-Y', strtotime('-2 month'));
        $month[3] = date('d-m-Y', strtotime('-3 month'));
        foreach ($month as $mes) {
            $m=date('n', strtotime($mes));
            $y=date('Y', strtotime($mes));
            $tickets = DB::select("SELECT COUNT(id_soporte) AS value FROM soportes WHERE tipo_soporte = 2 AND MONTH(created_at) = ? AND YEAR(created_at) = ? AND status_soporte = 2",[$m,$y])[0]->value;
            $factibilidades = DB::select("SELECT COUNT(id) AS value FROM factibilidades WHERE status != 1 AND MONTH(created_at) = ? AND YEAR(created_at) = ?",[$m,$y])[0]->value;

            
            
            array_push($datos, [$tickets]);
            array_push($datos2, [$factibilidades]);
            array_push($fechas, ['fecha'=>date('n-Y', strtotime($mes))]);
        }
        return collect(['datos'=>$datos,'datos2'=>$datos2, 'fechas'=>$fechas]);
    }


    public function traerDatosGraficaClientesZonas(){

        $servidores = DB::select("SELECT id_srvidor,nombre_srvidor FROM servidores");

        foreach ($servidores as $s) {
            $contador = DB::select("SELECT COUNT(s.id_srv) AS cantidad FROM servicios AS s 
                                        INNER JOIN aps AS a ON s.ap_srv = a.id
                                        INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                        INNER JOIN servidores AS se ON c.servidor_celda = se.id_srvidor
                                        WHERE se.id_srvidor = ? AND s.stat_srv = 1",[$s->id_srvidor])[0]->cantidad;
            $s->cantidad = $contador;                       
        }
        return response()->json($servidores);
    }

    public function traerDatosGraficaCuentasDl(){
        $mes = date('d-m-Y', strtotime('0 month'));
       
        $m=date('n', strtotime($mes));
        $y=date('Y', strtotime($mes));

        $facturado = 0;  
        $facturas = DB::select(
                "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
        from fac_controls where fac_controls.fac_status = 1 and fac_controls.denominacion = '$' and MONTH(fac_controls.created_at) = $m and YEAR(fac_controls.created_at) = $y ORDER BY created_at DESC ;");

            
        foreach ($facturas as $factura) {
            $facturado=$facturado+$factura->monto;     
        }

        return response()->json(round($facturado,2));
        //return response()->json($facturas);
    }

    public function traerDatosGraficaCuentasBs(){
        $mes = date('d-m-Y', strtotime('0 month'));
       
        $m=date('n', strtotime($mes));
        $y=date('Y', strtotime($mes));

        $facturado = 0;  
        $facturas = DB::select(
                "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
        from fac_controls where fac_controls.fac_status = 1 and fac_controls.denominacion != '$' and MONTH(fac_controls.created_at) = $m and YEAR(fac_controls.created_at) = $y ORDER BY created_at DESC ;");

            
        foreach ($facturas as $factura) {
            $facturado=$facturado+$factura->monto;     
        }

        return response()->json(round($facturado,2));
        //return response()->json($facturas);
    }

    public function traerDatosGraficaServicio(){
        $month=[];
        $month[0] = date('d-m-Y', strtotime('0 month'));
        $month[1] = date('d-m-Y', strtotime('-1 month'));
        $month[2] = date('d-m-Y', strtotime('-2 month'));
        $status=[];
        $fechas=[];
        foreach ($month as $mes) {
            $m=date('n', strtotime($mes));
            $y=date('Y', strtotime($mes));
            $facturado=0;
            $pagado = 0;
            $factura = DB::select(
                "SELECT  COUNT(id_srv) as cantidad FROM servicios WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?",[$m,$y])["0"];

                $facturado=$facturado+$factura->cantidad;
    
            
            array_push($status, ['facturado'=>$facturado, 'fecha'=>date('n-Y', strtotime($mes))]);
            array_push($fechas, ['fecha'=>date('n-Y', strtotime($mes))]);
        }
        return collect(['datos'=>$status, 'fechas'=>$fechas]);
        //return response()->json($facturas);
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
     * @param  \App\dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function show(dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(dashboard $dashboard)
    {
        //
    }
}
