<?php

namespace App\Http\Controllers;

use App\cuentasIncobrables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class CuentasIncobrablesController extends Controller
{
   
    public function index(Request $request)
    {
       $cuentas = DB::select("SELECT c.*,cl.kind,cl.nombre,cl.apellido,cl.social FROM cuentas_incobrables AS c
                                INNER JOIN fac_controls AS f ON c.factura = f.id
                                INNER JOIN clientes as cl ON f.id_cliente = cl.id
                                WHERE MONTH(c.created_at) = ? AND YEAR(c.created_at) = ?",[$request->mes,$request->año]);

       return response()->json($cuentas);
    }

    public function traerClientesExonerados()
    {   
        $cuentas = DB::select("SELECT * FROM servicios AS s
                                INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                INNER JOIN clientes as c ON s.cliente_srv = c.id
                                    WHERE s.stat_srv = 5");

            return response()->json($cuentas);
    }


    public function graficaCuentasIncobrables()
    {
        $month=[];
        $month[0] = date('d-m-Y', strtotime('0 month'));
        $month[1] = date('d-m-Y', strtotime('-1 month'));
        $month[2] = date('d-m-Y', strtotime('-2 month'));

        $status=[];
        $fechas=[];
        foreach ($month as $mes) {
            $m=date('n', strtotime($mes));
            $y=date('Y', strtotime($mes));
            $monto=0;
            $cuentas = DB::select("SELECT * FROM cuentas_incobrables WHERE MONTH(created_at) = $m and YEAR(created_at) = $y ORDER BY created_at DESC ;");

            foreach ($cuentas as $cuenta) {
                    $monto=$monto+$cuenta->monto;
            }


            array_push($status, ['monto'=>round($monto, 2),'fecha'=>date('n-Y', strtotime($mes))]);
            array_push($fechas, ['fecha'=>date('n-Y', strtotime($mes))]);
        }
        return collect(['datos'=>$status, 'fechas'=>$fechas]);
    }


    public function graficaCuentasExonerados()
    {
        
        $month= date('d-m-Y', strtotime('0 month'));
        $result = [];
        $m=date('n', strtotime($month));
        $y=date('Y', strtotime($month));
        $facturado=0;
        $pagado = 0;
        $montoExonerado = 0;
        $montoExoneraciones = 0;
        $facturas = DB::select(
                "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
        from fac_controls where fac_controls.fac_status = 1 and fac_controls.denominacion = '$' and MONTH(fac_controls.created_at) = $m and YEAR(fac_controls.created_at) = $y  ORDER BY created_at DESC ;");

            if(count($facturas)>0){
                $den=$facturas[0]->denominacion;
            }
            foreach ($facturas as $factura) {
                if($den=='$'){
                    $facturado=$facturado+$factura->monto;
                    $pagado=$pagado+$factura->pagado;
                }
            }

        $exonerados = DB::select("SELECT * FROM servicios AS s
                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                    INNER JOIN clientes as c ON s.cliente_srv = c.id
                                        WHERE s.stat_srv = 5");    

        foreach ($exonerados as $e) {
            $montoExonerado = $montoExonerado + $e->taza;
            
        }

        $exoneraciones = DB::select("SELECT * FROM balance_clientes_ins WHERE MONTH(created_at) = $m AND YEAR(created_at) = $y AND bal_tip_in = 20");

        foreach ($exoneraciones as $exo) {
            $montoExoneraciones = $montoExoneraciones + $exo->bal_monto_in;
        }

        $objeto = new \stdClass();
        $objeto->facturado = round($facturado,2);;
        $objeto->monto = round($montoExonerado,2);
        $objeto->exoneraciones = round($montoExoneraciones,2);
        array_push($result,$objeto);

        return response()->json($result);
    }   
    
}
