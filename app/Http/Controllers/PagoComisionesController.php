<?php

namespace App\Http\Controllers;

use App\pago_comisiones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class PagoComisionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function traerInstalaciones($id,$mes,$anio){

        $facturas = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                    INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                                        WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ? AND f.fac_status = 1 AND f.denominacion = "$" GROUP BY s.id_srv  ORDER BY `s`.`id_srv`  DESC',[$id,$mes,$anio]);
                                        
        $totalComisionDl = 0;
        $totalComisionBs = 0;
        $totalComisionConversion = 0; 
        $disponibleComisionDl = 0;
        $disponibleComisionConversion = 0;
        $disponibleComisionBs = 0;
        $restanteComisionDl = 0;
        $restanteComisionConversion = 0;
        $restanteComisionBs = 0;
        
        foreach ($facturas as $fac) {
                $monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
                $datos = DB::select("SELECT b.tasa,b.bal_tip_in,m.moneda FROM fac_pagos AS f
                                         INNER JOIN balance_clientes_ins AS b ON f.balance_pago_in = b.id_bal_in
                                         INNER JOIN metodo_pagos AS m ON b.bal_tip_in = m.id_metodo
                                                  WHERE f.fac_id = ? AND b.bal_stat_in = 1 LIMIT 1",[$fac->id]); 
                
                $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac->id])[0]->pagado;
                $deuda = round($monto-$pagado,2);
                $porcentaje = $fac->porcentaje_comision_serv/100;
                $comision = $monto*$porcentaje;
                if(count($datos) > 0){
                    foreach ($datos as $dato) {
                            $tasa =$fac->tasa_generacion;
                            $fac->tasa= $tasa;
                            
                            if($dato->moneda == "Bs.S"){
                                $fac->montoBS=round($monto*$tasa,2);
                                $fac->pagadoBS=round($pagado*$tasa,2);
                                $fac->deudaBS=round($deuda*$tasa,2);
                                $fac->comisionBS=round($comision*$tasa,2);
                                $fac->moneda = "Bs.S";
                                $totalComisionConversion += round($comision*$tasa,2);
                                if($deuda > 0){
                                    $fac->estado="pendiente";
                                    $restanteComisionConversion += round($comision*$tasa,2);
                                }else{
                                    $fac->estado="pagado";
                                    $disponibleComisionConversion += round($comision*$tasa,2);
                                }
                            }else{
                                $fac->monto=$monto;
                                $fac->pagado=$pagado;
                                $fac->deuda=$deuda;
                                $fac->comision=$comision;
                                $fac->moneda = "$";
                                $totalComisionDl += $comision;
                                if($deuda > 0){
                                    $fac->estado="pendiente";
                                    $restanteComisionDl += $comision;
                                }else{
                                    $fac->estado="pagado";
                                    $disponibleComisionDl += $comision;
                                }
                            }



                        }
                }else{
                    $tasa = 2; 
                    $fac->tasa= $tasa;
                    $fac->monto="Sin Registro de Pago";
                    $fac->pagado="Sin Registro de Pago";
                    $fac->deuda="Sin Registro de Pago";
                    $fac->comision="Sin Registro de Pago";
                    $fac->estado="pendiente";
                }
            
        }

        $facturas2 = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                    INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                                        WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ? AND f.fac_status = 1 AND f.denominacion != "$" GROUP BY f.id_cliente  ORDER BY `s`.`id_srv`  DESC',[$id,$mes,$anio]);

        foreach ($facturas2 as $fac2) {
            $monto=DB::select("SELECT round(SUM(fac_products.precio_bs), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac2->id])[0]->monto;
            $fac2->tasa=1;

            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac2->id])[0]->pagado;
            $deuda = round($monto-$pagado,2);
            if($deuda < 0){
                $deuda = 0;
            }
            $porcentaje = $fac2->porcentaje_comision_serv/100;
            $comision = round($monto*$porcentaje,2);

            $fac2->montoBS=$monto;
            $fac2->pagadoBS=$pagado;
            $fac2->deudaBS=$deuda;
            $fac2->comisionBS=$comision;
            $totalComisionBs += $comision;
            $fac2->moneda = "Bs.S";
            if($deuda > 0){
                $fac2->estado="pendiente";
                $restanteComisionBs += $comision;
            }else{
                $fac2->estado="pagado";
                $disponibleComisionBs += $comision;
            }
            
        }
        
        $facturasFinal = array_merge($facturas,$facturas2);

        $index = collect([
            'facturas'=>$facturasFinal,
            'comisionesdl'=>$totalComisionDl,
            'comisionesConversion'=>$totalComisionConversion,
            'comisionesbs'=>$totalComisionBs ,
            'disponibleComisionDl'=>$disponibleComisionDl,
            'disponibleComisionConversion'=>$disponibleComisionConversion,
            'disponibleComisionBs'=>$disponibleComisionBs,
            'restanteComisionDl'=>$restanteComisionDl,
            'restanteComisionConversion'=>$restanteComisionConversion,
            'restanteComisionBs'=>$restanteComisionBs,
             ]);
             
        
        return response()->json($index);

    }








    public function traerInstalacionesPendientes($id,$mes,$anio){
        $pendiente= $mes-1;
        $comisionesPendientes = [];

        $facturas = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                                                WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ? AND f.fac_status = 1 AND f.denominacion = "$" GROUP BY s.id_srv  ORDER BY `s`.`id_srv`  DESC',[$id,$pendiente,$anio]);
                                                
            

            foreach ($facturas as $fac) {
            if($fac->denominacion == "$"){
            $monto=DB::select("SELECT round(SUM(fac_products.precio_dl), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
            $datos = DB::select("SELECT b.tasa,b.bal_tip_in,m.moneda FROM fac_pagos AS f
                        INNER JOIN balance_clientes_ins AS b ON f.balance_pago_in = b.id_bal_in
                        INNER JOIN metodo_pagos AS m ON b.bal_tip_in = m.id_metodo
                                WHERE f.fac_id = ? AND b.bal_stat_in = 1 LIMIT 1",[$fac->id]); 

            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac->id])[0]->pagado;
            $deuda = round($monto-$pagado,2);
            $porcentaje = $fac->porcentaje_comision_serv/100;
            $comision = round($monto*$porcentaje,2);
            if(count($datos) > 0){
            foreach ($datos as $dato) {
            $tasa =$fac->tasa_generacion;
            $fac->tasa= $tasa;

            if($dato->moneda == "Bs.S"){
                $fac->montoBS=round($monto*$tasa,2);
                $fac->pagadoBS=round($pagado*$tasa,2);
                $fac->deudaBS=round($deuda*$tasa,2);
                $fac->comisionBS=round($comision*$tasa,2);
                $fac->moneda = "Bs.S";
            
                if($deuda > 0){
                    $fac->estado="pendiente";
                }else{
                    $fac->estado="pagado";
                }
            }else{
                $fac->monto=$monto;
                $fac->pagado=$pagado;
                $fac->deuda=$deuda;
                $fac->comision=$comision;
                $fac->moneda = "$";
                if($deuda > 0){
                    $fac->estado="pendiente";
                    array_push($comisionesPendientes,$fac);
                }else{
                    $fac->estado="pagado";
                }
            }



            }
            }else{
            $tasa = 2; 
            $fac->tasa= $tasa;
            $fac->monto="Sin Registro de Pago";
            $fac->pagado="Sin Registro de Pago";
            $fac->deuda="Sin Registro de Pago";
            $fac->comision="Sin Registro de Pago";
            $fac->estado="pendiente";
            }
            } 
            }

            $facturas2 = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                    INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                        WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ? AND f.fac_status = 1 AND f.denominacion != "$" GROUP BY f.id_cliente  ORDER BY `s`.`id_srv`  DESC',[$id,$pendiente,$anio]);

            foreach ($facturas2 as $fac2) {
            $monto=DB::select("SELECT round(SUM(fac_products.precio_bs), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac2->id])[0]->monto;
            $fac2->tasa=1;

            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac2->id])[0]->pagado;
            $deuda = round($monto-$pagado,2);
            if($deuda < 0){
            $deuda = 0;
            }
            $porcentaje = $fac2->porcentaje_comision_serv/100;
            $comision = round($monto*$porcentaje,2);

            $fac2->montoBS=$monto;
            $fac2->pagadoBS=$pagado;
            $fac2->deudaBS=$deuda;
            $fac2->comisionBS=$comision;
            $fac2->moneda = "Bs.S";
            if($deuda > 0){
            $fac2->estado="pendiente";
            array_push($comisionesPendientes,$fac2);
            }else{
            $fac2->estado="pagado";
        
            }

            }
        /*
        $comisionesPendientes = [];
        $facturas = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                    INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                                        WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ?  ORDER BY `s`.`id_srv`  DESC',[$id,$pendiente,$anio]);
       
        
        foreach ($facturas as $fac) {
            if($fac->denominacion == "$"){
                $monto=DB::select("SELECT round(SUM(fac_products.precio_dl), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
            }else{
                $monto=DB::select("SELECT round(SUM(fac_products.precio_bs), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
            }

            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac->id])[0]->pagado;
            $deuda = round($monto-$pagado,2);
            $porcentaje = $fac->porcentaje_comision_serv/100;
            $comision = round($monto*$porcentaje,2);
            
        
           $fac->monto=$monto;
           $fac->pagado=$pagado;
           $fac->deuda=$deuda;
           $fac->comision=$comision;

           if($deuda > 0){
            $fac->estado="pendiente";
            array_push($comisionesPendientes,$fac);
            }else{
            $fac->estado="pagado";
            }
           
        }
        */
        return response()->json($comisionesPendientes);

    }

    public function busqueda($id,$mes,$anio,$dato){


        $facturas = DB::select('SELECT c.kind, c.dni, c.nombre,c.apellido,c.social,c.serie,s.id_srv,s.stat_srv, s.porcentaje_comision_serv, f.* FROM servicios AS s 
                                    INNER JOIN clientes AS c ON S.cliente_srv = c.id
                                    INNER JOIN fac_controls AS f ON c.id = f.id_cliente
                                        WHERE s.user_comision_serv = ? AND MONTH(f.created_at) = ? AND YEAR(f.created_at) = ? AND c.dni LIKE ?  ORDER BY s.id_srv  DESC',[$id,$mes,$anio,"%".$dato."%"]);
       
      
        foreach ($facturas as $fac) {
            if($fac->denominacion == "$"){
                $monto=DB::select("SELECT round(SUM(fac_products.precio_dl), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
            }else{
                $monto=DB::select("SELECT round(SUM(fac_products.precio_bs), 2) as monto from  fac_products where ? = fac_products.codigo_factura",[$fac->id])[0]->monto;
            }

            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where ? = fac_pagos.fac_id",[$fac->id])[0]->pagado;
            $deuda = round($monto-$pagado,2);
            $porcentaje = $fac->porcentaje_comision_serv/100;
            $comision = round($monto*$porcentaje,2);
            
            
           $fac->monto=$monto;
           $fac->pagado=$pagado;
           $fac->deuda=$deuda;
           $fac->comision=$comision;

           if($deuda > 0){
            $fac->estado="pendiente";  
            }else{
            $fac->estado="pagado";
            }
    }

         return response()->json($facturas);
}


    public function guardarComision(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $responsable = DB::select('SELECT * FROM users WHERE id_user = ?',[$request->responsable])[0];
        $responsable1 = $responsable->nombre_user." ".$responsable->apellido_user;
        $result = DB::insert('INSERT INTO pago_comisiones(monto,tipo_comision,usuario,emisor,receptor,referencia,responsable,mes,anio,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)',[$request->comision,$request->tipoComision,$request->id_user,$request->emisor,$request->receptor,$request->referencia,$responsable1,$request->mes,$request->anio,$request->fechaPago,$fecha]);


        return response()->json($result);
    }   

    public function pagosRealizadosComisionesDl($id,$mes,$anio){
        $result= DB::select("SELECT round(SUM(p.monto), 2) as monto from  pago_comisiones AS p WHERE p.usuario = ? AND p.mes = ? AND p.anio = ? AND p.tipo_comision = '$'",[$id,$mes,$anio]);

        return response()->json($result);
    }
    public function pagosRealizadosRecientesComisionesDl($id,$mes,$anio){
        $result= DB::select("SELECT * FROM pago_comisiones AS p WHERE p.usuario = ? AND p.mes = ? AND p.anio = ? AND p.tipo_comision = '$' ORDER BY p.id_pago DESC LIMIT 1",[$id,$mes,$anio]);

        return response()->json($result);
    }

    public function pagosRealizadosComisionesBs($id,$mes,$anio){
        $result= DB::select("SELECT round(SUM(p.monto), 2) as monto from  pago_comisiones AS p WHERE p.usuario = ? AND p.mes = ? AND p.anio = ? AND p.tipo_comision = 'Bs.S'",[$id,$mes,$anio]);

        return response()->json($result);
    }

    public function pagosRealizadosRecientesComisionesBs($id,$mes,$anio){
        $result= DB::select("SELECT * FROM pago_comisiones AS p WHERE p.usuario = ? AND p.mes = ? AND p.anio = ? AND p.tipo_comision = 'Bs.S' ORDER BY p.id_pago DESC LIMIT 1",[$id,$mes,$anio]);

        return response()->json($result);
    }


    public function traerListaPagosComisiones($id,$mes,$anio){
        $result= DB::select("SELECT * from  pago_comisiones AS p WHERE p.usuario = ? AND p.mes = ? AND p.anio = ?",[$id,$mes,$anio]);

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
     * @param  \App\pago_comisiones  $pago_comisiones
     * @return \Illuminate\Http\Response
     */
    public function show(pago_comisiones $pago_comisiones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\pago_comisiones  $pago_comisiones
     * @return \Illuminate\Http\Response
     */
    public function edit(pago_comisiones $pago_comisiones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\pago_comisiones  $pago_comisiones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pago_comisiones $pago_comisiones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\pago_comisiones  $pago_comisiones
     * @return \Illuminate\Http\Response
     */
    public function destroy(pago_comisiones $pago_comisiones)
    {
        //
    }
}
