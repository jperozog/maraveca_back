<?php

namespace App\Http\Controllers;

use App\comisionesVendedores;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
class ComisionesVendedoresController extends Controller
{
    public function traerVendedores(){

        $vendedores = DB::select("SELECT * FROM `instalaciones` AS i
                                    INNER JOIN users AS u ON i.user_insta = u.id_user		
                                        GROuP BY user_insta");

        return response()->json($vendedores);
    }

    public function traerComisionesVendedor(Request $request){
        $mes = $request["mes"];
        $anio=$request["anio"];
        $id_vendedor = $request["id_user"];
        
        $comisiones = DB::select("SELECT i.*,d.*,c.id,c.kind,c.dni,c.nombre,c.apellido,c.social FROM instalaciones AS i 
                                        INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                        INNER JOIN clientes AS c ON i.cliente_insta = c.id
                                        WHERE MONTH(i.created_at) = ? AND YEAR(i.created_at)= ? AND i.user_insta = ? AND i.status_insta != 3
                                        ORDER BY i.id_insta DESC",[$mes,$anio,$id_vendedor]);

        foreach ($comisiones as $comision) {
            $servicioActual = DB::select("SELECT * FROM servicios WHERE cliente_srv = ?",[$comision->cliente_insta]);
            if($servicioActual != []){
                $comision->estadoComision = 1;
            }else{
                $comision->estadoComision = 0;
            }
          
        }

        return response()->json($comisiones);
    }

    public function realizarPagoComisionVendedor(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $agregarPago = DB::select("INSERT INTO pago_comisiones_vendedores(tipo_pago_comision,monto,fecha_pago,id_pago_user,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$request->tipo_pago,$request->monto,$request->fecha,$request->id_vendedor,$request->id_user,$date,$date]);

        return response()->json($agregarPago);
    }

    public function traerPagosComisionesVendedor(Request $request){
        $pagos = DB::select("SELECT * FROM pago_comisiones_vendedores AS p INNER JOIN users AS u ON p.responsable = u.id_user WHERE id_pago_user = ? AND MONTH(fecha_pago) = ? AND YEAR(fecha_pago) = ?",[$request->id_user,$request->mes,$request->anio]);
        return response()->json($pagos);
    }

   
}
