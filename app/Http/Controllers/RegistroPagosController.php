<?php

namespace App\Http\Controllers;

use App\registroPagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Mikrotik\RouterosAPI;
use App\historico_cliente;
use App\historico;
use App\cola_de_ejecucion;

class RegistroPagosController extends Controller
{
   
    public function traerMetodos(){

        $result = DB::select("SELECT * FROM metodo_pagos");

        return response()->json($result);
    }

    public function traerTaza(){

        $result = DB::select("SELECT *  FROM `configuracions` WHERE `nombre` LIKE 'taza'");

        return response()->json($result);
    }

    public function store(Request $request)
    {   
        
        $metodo = $request->input("metodo");
        $referencia = $request->input("referencia");
        $fecha = Carbon::createFromTimestamp($request->input("fecha"))->toDateTimeString();
        $monto = $request->input("monto");
        $conversion = $request->input("conversion");
        $usuario = $request->input("usuario");
        $cliente = $request->input("cliente");
        $fecha2 = date("Y-m-d H:i:s");
        

        
        $result = DB::select("SELECT valor FROM configuracions where nombre = 'taza'");
        $taza = $result[0]->valor;

        if($metodo == 1 || $metodo == 2 || $metodo == 3 || $metodo == 6){
            $result2 = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,user_bal_mod_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                                [$cliente,$metodo,$conversion,$conversion,$monto,$referencia,$usuario,$taza,1,$fecha,$fecha]);
        }else{
            $result2 = DB::update("INSERT INTO balance_clientes_ins (bal_cli_in,bal_tip_in,bal_monto_in,bal_rest_in,conversion,bal_comment_in,user_bal_mod_in,tasa,uso_bal_in,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                                 [$cliente,$metodo,$monto,$monto,$monto,$referencia,$usuario,$taza,1,$fecha,$fecha]);
        }


        revisarBalance_in($cliente);
        revisar_in($cliente);

        if($metodo == 1 || $metodo == 2 || $metodo == 3 || $metodo ==6 ){
            $moneda = "Bs.S";
        }else{
            $moneda = "$";
        }
        
        $result = DB::update("INSERT INTO registro_pagos (responsable,cliente,monto,metodo_pago,moneda,comentario,fecha_pago,estatus_registro) VALUES (?,?,?,?,?,?,?,?)",
        [$usuario,
        $cliente,
        $monto,
        $metodo,
        $moneda,
        $referencia,
        $fecha2,
        1]);
        
     return response()->json($request);
    }


    public function editarPago(Request $request){
        $datos = $request->datos;
        $tipo = $request->tipo;

        if ($tipo == 1) {
            if($datos["bal_stat_in"] == 2 ){
                if($datos["bal_tip_in"] == 1 || $datos["bal_tip_in"] == 2 || $datos["bal_tip_in"] == 3 || $datos["bal_tip_in"] == 6){
                    $nuevoMonto = $datos["conversion"] / $datos["tasa"];
                    $actualizarPago = DB::select("UPDATE balance_clientes_ins SET bal_tip_in = ?,bal_comment_in = ?,bal_monto_in = ?,bal_rest_in = ?,conversion = ?,bal_comment_mod_in = ? WHERE id_bal_in = ?",[$datos["bal_tip_in"],$datos["bal_comment_in"],$nuevoMonto,0,$datos["conversion"],$datos["bal_comment_mod_in"],$datos["id_bal_in"]]);
                }else{
                    $nuevoMonto = $datos["bal_monto_in"] * $datos["tasa"];
                    $actualizarPago = DB::select("UPDATE balance_clientes_ins SET bal_tip_in = ?,bal_comment_in = ?,bal_monto_in = ?,bal_rest_in = ?,conversion = ?. bal_comment_mod_in = ? WHERE id_bal_in = ?",[$datos["bal_tip_in"],$datos["bal_comment_in"],$datos["bal_monto_in"],0,$nuevoMonto,$datos["bal_comment_mod_in"],$datos["id_bal_in"]]);
                }
                
            }
    
            if($datos["bal_stat_in"] == 1 ) {
                $eliminarPagoBalance = DB::delete("DELETE FROM balance_clientes_ins WHERE id_bal_in = ?",[$datos["id_bal_in"]]);
    
                $facturas = DB::select("SELECT * FROM fac_pagos WHERE balance_pago_in = ?",[$datos["id_bal_in"]]);
    
                $eliminarPagoFactura = DB::select("DELETE FROM fac_pagos WHERE balance_pago_in = ?",[$datos["id_bal_in"]]);
    
                foreach ($facturas as $fac) {
    
                    $montoFacturas = DB::select("SELECT fc.*,c.*,
                                            ROUND((SELECT SUM(fp.precio_articulo) FROM  fac_products AS fp where fc.id = fp.codigo_factura), 2) as monto,
                                            ROUND((SELECT SUM(fpa.pag_monto) from  fac_pagos AS fpa where fc.id = fpa.fac_id), 2) as pagado
                                                    FROM fac_controls AS fc
                                                        INNER JOIN clientes AS c ON fc.id_cliente = c.id
                                                        WHERE fc.id = ?",[$fac->fac_id]);
    
                    foreach ($montoFacturas as $factura) {
    
                        if ($factura->monto > $factura->pagado) {
                            //suspenderProvicional($factura->fac_serv);
                            $datosServicio = DB::select("SELECT * FROM servicios WHERE id_srv = ?",[$factura->fac_serv])["0"];
    
                            if( $factura->kind == 'V'|| $factura->kind =='E'){
                                $nombre4 = explode(" ",$factura->nombre);
                                $apellido4 = explode(" ",$factura->apellido);
                                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
            
                                $cliente1= ucfirst($factura->nombre)." ".ucfirst($factura->apellido);
                                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                            }else {
                                $cliente1= ucwords(strtolower($factura->social));
                                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                            }
                            
    
                            if ($datosServicio->tipo_srv == 1) {
                                $inalambrico = DB::select("SELECT * FROM aps AS a
                                                                INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                                                INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor 
                                                                    WHERE a.id = ?",[$datosServicio->ap_srv])["0"];
                                                                    
                                $datosServicio->masDatos = $inalambrico;    
    
                                $API = new RouterosAPI();
                                if ($API->connect($inalambrico->ip_srvidor, $inalambrico->user_srvidor, $inalambrico->password_srvidor)) {                                  //se conecta y verifica si el cliente exista en la lista
                                    $API->write('/ip/firewall/address-list/print',false);
                                    $API->write('?list=ACTIVOS',false);
                                    $API->write('?disabled=false',false);
                                    $API->write('?address='.$datosServicio->ip_srv,true);
                                    $READ = $API->read(false);
                                    $ARRAY = $API->parseResponse($READ);
                                    if(count($ARRAY)>0) {
                                        $API->write('/ip/firewall/address-list/remove', false); // en caso de existir lo eliminara
                                        $API->write('=.id=' . $ARRAY[0]['.id']);
                                        $READ = $API->read(false);
                                        //return $READ;
                                    }
    
                                            $API->write("/queue/simple/getall",false);                // verificara si existe en la lista queue
                                            $API->write('?name='.$cliente2."(".$factura->fac_serv.")",true);
                                            $READ = $API->read(false);
                                            $ARRAY = $API->parseResponse($READ);
                                            if(count($ARRAY)>0){
                                                $API->write("/queue/simple/remove",false);            // en caso de existir lo eliminara
                                                //  $API->write('=.name='.$cliente."(".$id_srv.")");
                                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                                $READ = $API->read(false);
                                                $ARRAY = $API->parseResponse($READ);
    
                                    }
    
                                        $API->write('/ppp/secret/print',false);
                                            $API->write('?remote-address='.$datosServicio->ip_srv,true);
                                            $READ = $API->read(false);
                                            $ARRAY = $API->parseResponse($READ);
                                            if(count($ARRAY)>0) {
                                                $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                                $READ = $API->read(false);
                                                //return $READ;
                                            }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                                                $API->write('/ppp/active/print',false);
                                                $API->write('?address='.$datosServicio->ip_srv,true);
                                                $READ = $API->read(false);
                                                $ARRAY = $API->parseResponse($READ);
                                                var_dump($ARRAY);
                                                
                                                if(count($ARRAY)>0) {
                                                    $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                                                    $API->write('=.id=' . $ARRAY[0]['.id']);
                                                    $READ = $API->read(false);
                                                    //return $READ;
                                                }  
                                    
    
                                    cola_de_ejecucion::join('servicios', 'servicios.id_srv', '=', 'cola_de_ejecucions.id_srv')->where('ip_srv', $datosServicio->ip_srv)->where('accion', 's')->delete();
                                    ;
                                }
                                $API->disconnect();
    
                                $actualizarServicio = DB::update("UPDATE servicios SET stat_srv = 3 WHERE id_srv = ?",[$factura->fac_serv]);
    
                                historico_cliente::create(['history'=>'Servicio Suspendido por Eliminacion de Pago', 'modulo'=>'Servicios', 'cliente'=>$factura->id_cliente, 'responsable'=>0]);
                                historico::create(['responsable'=>0, 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
    
                                
                            } else {
                                $alambrico = DB::select("SELECT * FROM caja_distribucion AS c
                                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                                WHERE c.id_caja = ?",[$datosServicio->ap_srv])["0"];
    
                                $datosServicio->masDatos = $alambrico;  
                                
                            
                                $API = new RouterosAPI();
                                if ($API->connect($alambrico->ip_srvidor, $alambrico->user_srvidor, $alambrico->password_srvidor)) {                                  //se conecta y verifica si el cliente exista en la lista
                                $API->write('/ppp/secret/print',false);
                                        $API->write('?name='.$cliente2."(".$factura->fac_serv.")",true);
                                        $READ = $API->read(false);
                                        $ARRAY = $API->parseResponse($READ);
                                        if(count($ARRAY)>0) {
                                            $API->write('/ppp/secret/remove', false); // en caso de existir lo eliminara
                                            $API->write('=.id=' . $ARRAY[0]['.id']);
                                            $READ = $API->read(false);
                                            //return $READ;
                                        }        //cola_de_ejecucion::where('soporte_pd', $id_soporte)->where('accion', 'r_p_i')->delete();
                                            $API->write('/ppp/active/print',false);
                                            $API->write('?name='.$cliente2."(".$factura->fac_serv.")",true);
                                            $READ = $API->read(false);
                                            $ARRAY = $API->parseResponse($READ);
                                            var_dump($ARRAY);
                                            
                                            if(count($ARRAY)>0) {
                                                $API->write('/ppp/active/remove', false); // en caso de existir lo eliminara
                                                $API->write('=.id=' . $ARRAY[0]['.id']);
                                                $READ = $API->read(false);
                                                //return $READ;
                                            }  
                                    }
                                    $API->disconnect();
                                    
                                    $actualizarServicio = DB::update("UPDATE servicios SET stat_srv = 3 WHERE id_srv = ?",[$factura->fac_serv]);
    
                                    historico_cliente::create(['history'=>'Servicio Suspendido por Eliminacion de Pago', 'modulo'=>'Servicios', 'cliente'=>$factura->id_cliente, 'responsable'=>0]);
                                    historico::create(['responsable'=>0, 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
    
                            }
    
                        } else {
                            # code...
                        }
                        
    
                    }
    
                    revisarBalance_in($datos["bal_cli_in"]);
                    revisar_in($datos["bal_cli_in"]);
    
                }
    
            }
        } else {
           $eliminarPagoFacPagos = DB::delete("DELETE FROM fac_pagos WHERE id = ?",[$datos["id"]]);

        }
        

        return response()->json($request);
    }


    public function traerCierresPendientes(){
        $result = DB::select("SELECT c.id_cierre, c.cierre_fecha, c.estatus, u.nombre_user, u.apellido_user  FROM `cierre_caja` as c
                                    INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                    INNER JOIN users as u ON r.responsable = u.id_user 
                                        GROUP BY id_cierre ORDER BY id DESC");

        return response()->json($result);
    }

    public function traerDatosCierrePendiente($id){
        $result = DB::select("SELECT cl.nombre, cl.apellido, c.*, r.*,m.nombre_metodo  FROM `cierre_caja` as c
                                INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                INNER JOIN clientes as cl ON r.cliente= cl.id
                                INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                        WHERE c.id_cierre = ? ORDER BY c.id DESC",[$id]);

        foreach ($result as $r) {
            $r->nombreCliente = $r->nombre." ".$r->apellido;
        }                                

        return response()->json($result);
    }

    public function traerEfectivoCierre($id){
        $result =DB::select('SELECT SUM(monto) as suma  FROM `cierre_caja` as c
                                    INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                    INNER JOIN clientes as cl ON r.cliente= cl.id
                                     INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                            WHERE c.id_cierre = ? AND metodo_pago = 14 ORDER BY c.id DESC',[$id]);

        return response()->json($result);                                    
    }

    public function traerNacionalesCierre($id){
        $result =DB::select('SELECT SUM(monto) as suma  FROM `cierre_caja` as c
                                    INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                    INNER JOIN clientes as cl ON r.cliente= cl.id
                                     INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                            WHERE c.id_cierre = ? AND (metodo_pago = 1 OR metodo_pago = 2 OR metodo_pago = 3 OR metodo_pago = 6) ORDER BY c.id DESC',[$id]);

        return response()->json($result);                                    
    }

    public function traerZelleCierre($id){
        $result =DB::select('SELECT SUM(monto) as suma  FROM `cierre_caja` as c
                                    INNER JOIN registro_pagos as r  ON c.pago = r.id_registro 
                                    INNER JOIN clientes as cl ON r.cliente= cl.id
                                     INNER JOIN metodo_pagos as m ON r.metodo_pago = m.id_metodo
                                            WHERE c.id_cierre = ? AND metodo_pago = 12 ORDER BY c.id DESC',[$id]);

        return response()->json($result);                                    
    }

    public function cancelarCierre(Request $request){

        $registros = $request->movimientos;

        foreach ($registros as $re) {
            $id_re = $re["id_registro"];
            $id_ci = $re["id_cierre"];
 
            $result = DB::update('UPDATE registro_pagos SET estatus_registro = 1 WHERE id_registro = ?',[$id_re]);
 
            $result2 = DB::update('UPDATE cierre_caja SET estatus = 0 WHERE id_cierre = ?',[$id_ci]);
         }

        return response()->json($result2);
    }

    public function confirmarCierre(Request $request){

        $registros = $request->movimientos;

        foreach ($registros as $re) {
            $id_ci = $re["id_cierre"];
 
            $result = DB::update('UPDATE cierre_caja SET estatus = 2 WHERE id_cierre = ?',[$id_ci]);
         }

        return response()->json($result);
    }

  

    public function conversion(){
    $balance = DB::select("SELECT * FROM balance_clientes_ins AS b WHERE b.bal_tip_in = 1 OR b.bal_tip_in = 2 OR b.bal_tip_in = 3 OR b.bal_tip_in = 6 ORDER BY `id_bal_in`  DESC");

    foreach ($balance as $bal) {
        

        $result = DB::update("UPDATE balance_clientes_ins
                                    SET conversion = ?
                                    WHERE id_bal_in = ?",[$bal->bal_monto_in,$bal->id_bal_in]);


    }

    return response()->json($balance);
    }
}
