<?php

namespace App\Http\Controllers;

use App\descuentos;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

class DescuentosController extends Controller
{
   
    public function index()
    {
      $descuentos = DB::select("SELECT d.*,c.kind,c.nombre,c.apellido,c.social,u.nombre_user,u.apellido_user FROM descuentos AS d
                                            INNER JOIN clientes AS c ON d.cliente_des = c.id
                                            INNER JOIN users AS u ON d.usuario = u.id_user
                                              ORDER BY id_descuento DESC");

      return response()->json($descuentos);
    }

    
    
    public function store(Request $request)
    {
        //
    }

    public function guardarDescuento(Request $request){

        $fecha = date("Y-m-d H:i:s");
        $taza = DB::select("SELECT * FROM configuracions WHERE nombre ='taza'")["0"]->valor;
    
        $datosFactura = DB::select("SELECT * FROM fac_controls WHERE id = ?",[$request->factura])["0"];
        $servicio = $datosFactura->fac_serv;

        $datosServicio = DB::select("SELECT * FROM servicios AS s
                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                        WHERE id_srv = ?",[$servicio])["0"];

        
        if($request->tipo == 1){
            $inicioMes = new Carbon("first day of this month");
            $inicioSiguienteMes = new Carbon('first day of next month');
            $diasCompletos=$inicioSiguienteMes->diff($inicioMes)->format('%a');

            $precioDiario= $datosServicio->taza / $diasCompletos;

            $precioDescuento = $request->dias * $precioDiario;
            /*
            $registroPago = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",
                                    [$datosFactura->id_cliente,20,$precioDescuento,$precioDescuento,$precioDescuento,$request->comentario." (Descuento ".$request->dias." dias)",$taza,1,$fecha,$fecha]);
            
            $regitroPago2 = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
                                        [$request->usuario,
                                        $datosFactura->id_cliente,
                                        $precioDescuento,
                                        20,
                                        "$",
                                        $request->comentario,
                                        $fecha,
                                        1]);

            revisarBalance_in($datosFactura->id_cliente);
            revisar_in($datosFactura->id_cliente);
            */
            $aggDescuento = DB::select("INSERT INTO descuentos(cliente_des,tipo,dias,monto,comentario,taza,usuario,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",
                                    [$datosFactura->id_cliente,
                                    $request->tipo,
                                    $request->dias,
                                    $precioDescuento,
                                    $request->comentario." (Descuento ".$request->dias." dias)",
                                    $taza,
                                    $request->usuario,
                                    $fecha,
                                    $fecha
                                    ]);
        }
        if($request->tipo == 2){

            if($request->dias < 7){
                $precio= $datosServicio->taza * 0.10;
                $comentarioDescuento = " (Descuento 10%)";
            }

            if($request->dias > 7 && $request->dias <= 14){
                $precio= $datosServicio->taza * 0.25;
                $comentarioDescuento = " (Descuento 25%)";
            }

            if($request->dias > 14){
                $precio= $datosServicio->taza * 0.40;
                $comentarioDescuento = " (Descuento 40%)";
            }
            /*
            $registroPago = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$datosFactura->id_cliente,20,$precio,$precio,$precio,$request->comentario." ".$comentarioDescuento,$taza,1,$fecha,$fecha]);

            $regitroPago2 = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
                                        [$request->usuario,
                                        $datosFactura->id_cliente,
                                        $precio,
                                        20,
                                        "$",
                                        $request->comentario,
                                        $fecha,
                                        1]);


            revisarBalance_in($datosFactura->id_cliente);
            revisar_in($datosFactura->id_cliente);
            */
            $aggDescuento = DB::select("INSERT INTO descuentos(cliente_des,tipo,dias,monto,comentario,taza,usuario,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",
                                    [$datosFactura->id_cliente,
                                    $request->tipo,
                                    $request->dias,
                                    $precio,
                                    $request->comentario." ".$comentarioDescuento,
                                    $taza,
                                    $request->usuario,
                                    $fecha,
                                    $fecha
                                    ]);
        }

        if($request->tipo == 3){
        
            $precio= $datosServicio->taza * 0.50;
            $comentarioDescuento = " (Descuento 50% Mitad de Mensualidad)";
            
            /*
            $registroPago = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$datosFactura->id_cliente,20,$precio,$precio,$precio,$request->comentario." ".$comentarioDescuento,$taza,1,$fecha,$fecha]);

            $regitroPago2 = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
                                            [$request->usuario,
                                            $datosFactura->id_cliente,
                                            $precio,
                                            20,
                                            "$",
                                            $request->comentario,
                                            $fecha,
                                            1]);


            revisarBalance_in($datosFactura->id_cliente);
            revisar_in($datosFactura->id_cliente);
            */
            $aggDescuento = DB::select("INSERT INTO descuentos(cliente_des,tipo,dias,monto,comentario,taza,usuario,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",
                                    [$datosFactura->id_cliente,
                                    $request->tipo,
                                    $request->dias,
                                    $precio,
                                    $request->comentario." ".$comentarioDescuento,
                                    $taza,
                                    $request->usuario,
                                    $fecha,
                                    $fecha
                                    ]);
        }

        if($request->tipo == 4){
        
            $precio= $request->monto;
            $comentarioDescuento = " (Otro tipo de Descuento)";
            
            /*
            $registroPago = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$datosFactura->id_cliente,20,$precio,$precio,$precio,$request->comentario." ".$comentarioDescuento,$taza,1,$fecha,$fecha]);

            $regitroPago2 = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
                                            [$request->usuario,
                                            $datosFactura->id_cliente,
                                            $precio,
                                            20,
                                            "$",
                                            $request->comentario,
                                            $fecha,
                                            1]);


            revisarBalance_in($datosFactura->id_cliente);
            revisar_in($datosFactura->id_cliente);
            */
            $aggDescuento = DB::select("INSERT INTO descuentos(cliente_des,tipo,dias,monto,comentario,taza,usuario,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",
                                    [$datosFactura->id_cliente,
                                    $request->tipo,
                                    $request->dias,
                                    $precio,
                                    $request->comentario." ".$comentarioDescuento,
                                    $taza,
                                    $request->usuario,
                                    $fecha,
                                    $fecha
                                    ]);
        }

         
        
        $historicoCliente = DB::select("INSERT INTO historico_clientes(history,modulo,cliente,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?)",["Creacion de descuento al cliente, a la espera de aprobacion","Descuentos",$datosFactura->id_cliente,$request->usuario,$fecha,$fecha]);
        
        

        return response()->json($request);
    }

    public function aprobarDescuento(Request $request){

        $fecha = date("Y-m-d H:i:s");
        $datos = DB::select("SELECT * FROM descuentos WHERE id_descuento = ?",[$request->id])["0"];

        $actualizarDecuento = DB::update("UPDATE descuentos SET status = 1 WHERE id_descuento = ?",[$request->id]);
        $historicoCliente = DB::select("INSERT INTO historico_clientes(history,modulo,cliente,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?)",["Aprobacion de descuento al cliente","Descuentos",$datos->cliente_des,0,$fecha,$fecha]);

    
        $registroPago = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",
                                    [$datos->cliente_des,20,$datos->monto,$datos->monto,$datos->monto,$datos->comentario,$datos->taza,1,$datos->created_at,$datos->updated_at]);

        
        $regitroPago2 = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
                                        [$datos->usuario,
                                        $datos->cliente_des,
                                        $datos->monto,
                                        20,
                                        "$",
                                        $datos->comentario,
                                        $datos->created_at,
                                        1]);

        revisarBalance_in($datos->cliente_des);
        revisar_in($datos->cliente_des);
            
        
         
      
        
        

        return response()->json($request);
    }

    public function cancelarDescuento(Request $request){

        $fecha = date("Y-m-d H:i:s");
        $datos = DB::select("SELECT * FROM descuentos WHERE id_descuento = ?",[$request->id])["0"];
         
        $actualizarDecuento = DB::update("UPDATE descuentos SET status = 2 WHERE id_descuento = ?",[$request->id]);
        $historicoCliente = DB::select("INSERT INTO historico_clientes(history,modulo,cliente,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?)",["Cancelacion de descuento al cliente","Descuentos",$datos->cliente_des,0,$fecha,$fecha]);
        
        

        return response()->json($request);
    }

}
