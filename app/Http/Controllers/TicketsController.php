<?php

namespace App\Http\Controllers;

use App\tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
      $tickets = DB::select("SELECT c.kind, c.nombre,c.apellido,c.social,c.serie,se.tipo_srv,se.ap_srv,u.nombre_user,u.apellido_user,s.* FROM soportes AS s
                                    INNER JOIN servicios AS se ON s.servicio_soporte = se.id_srv
                                    INNER JOIN clientes AS c ON se.cliente_srv = c.id
                                    INNER JOIN users AS u ON s.user_soporte = u.id_user
                                     WHERE tipo_soporte = 2 ORDER BY status_soporte ASC, id_soporte DESC");

      
        foreach ($tickets as $ticket) {
            if ($ticket->tipo_srv == 1) {
                $masDatos = DB::select("SELECT * FROM aps AS a 
                                            INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                WHERE a.id = ?",[$ticket->ap_srv])["0"];
                
                $ticket->nombre_celda = $masDatos->nombre_celda;
                $ticket->nombre_srvidor = $masDatos->nombre_srvidor;
    
            }else{
                $masDatos = DB::select("SELECT * FROM caja_distribucion AS c 
                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                WHERE c.id_caja = ?",[$ticket->ap_srv])["0"];

                $ticket->nombre_celda = $masDatos->nombre_olt;
                $ticket->nombre_srvidor = $masDatos->nombre_srvidor;
            }
        }

        $averias = DB::select("SELECT * FROM averias AS a
                                INNER JOIN users AS u ON a.responsable = u.id_user
                                    ORDER BY status_averia ASC, id_averia DESC");

        foreach ($averias as $averia) {
            if ($averia->tipo_averia == 1) {
                $masDatos = DB::select("SELECT * FROM celdas AS c 
                                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                WHERE c.id_celda = ?",[$averia->lugar_averia])["0"];
                
                $averia->nombre_celda = $masDatos->nombre_celda;
                $averia->nombre_srvidor = $masDatos->nombre_srvidor;

            }else{
                $masDatos = DB::select("SELECT * FROM  manga_empalme AS m
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                WHERE m.id_manga = ?",[$averia->lugar_averia])["0"];

                $averia->nombre_celda = $masDatos->nombre_manga;
                $averia->nombre_srvidor = $masDatos->nombre_srvidor;
            }
        } 

        
        $reposiciones = DB::select("SELECT * FROM reposicion_equipos AS r
                                    INNER JOIN clientes AS c ON r.cliente_reposicion = c.id
                                    INNER JOIN servicios AS s ON c.id = s.cliente_srv
                                    INNER JOIN articulos AS a ON r.equipo_reposicion = a.id_articulo
                                    INNER JOIN users AS u ON r.responsable_reposicion = u.id_user
                                        ORDER BY status_reposicion ASC, id_reposicion DESC");
        
        foreach ($reposiciones as $key => $reposicion) {
            if ($reposicion->tipo_srv == 1) {
                $masDatos = DB::select("SELECT * FROM celdas AS c 
                                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                WHERE c.id_celda = ?",[$reposicion->lugar_reposicion])["0"];

                $reposicion->nombre_celda = $masDatos->nombre_celda;
                $reposicion->nombre_srvidor = $masDatos->nombre_srvidor;
            } else {
                $masDatos = DB::select("SELECT * FROM caja_distribucion AS c 
                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                WHERE c.id_caja = ?",[$reposicion->lugar_reposicion])["0"];

                $reposicion->nombre_celda = $masDatos->nombre_manga;
                $reposicion->nombre_srvidor = $masDatos->nombre_srvidor;
            }
            
        }
        

       $index = collect(['tickets' => $tickets, 'averias'=>$averias,"reposiciones"=>$reposiciones]);

      return response()->json($index);

    }

    public function ticketsActivos()
    {
        $tickets = DB::select("SELECT * FROM soportes WHERE tipo_soporte = 2 AND status_soporte = 1");

        $averias = DB::select("SELECT * FROM averias WHERE status_averia = 1");

        $reposiciones = DB::select("SELECT * FROM reposicion_equipos WHERE status_reposicion = 1");

        $cantidadTikets = count($tickets);

        $cantidadAverias = count($averias);

        $cantidadRepo = count($reposiciones);
        
        $index = collect([
        'tickets'=>$cantidadTikets,
        'averias'=>$cantidadAverias,
        'repo'=>$cantidadRepo]);

        return response()->json($index);
    }


    public function detallesTicket(Request $request){

        $ticket = DB::select("SELECT * FROM soportes AS s
                                INNER JOIN servicios AS se ON s.servicio_soporte = se.id_srv
                                INNER JOIN clientes AS c ON se.cliente_srv = c.id
                                INNER JOIN users AS u ON s.user_soporte = u.id_user
                                    WHERE id_soporte = ?",[$request->ticket])["0"];
    
            if ($ticket->tipo_srv == 1) {
                $masDatos = DB::select("SELECT * FROM aps AS a
                                            INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                WHERE a.id = ?",[$ticket->ap_srv])["0"];
                
                $ticket->nombre_celda = $masDatos->nombre_celda;
                $ticket->nombre_srvidor = $masDatos->nombre_srvidor;

            }else{
                $masDatos = DB::select("SELECT * FROM caja_distribucion AS c
                                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga 
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                WHERE c.id_caja = ?",[$ticket->ap_srv])["0"];

                $ticket->nombre_celda = $masDatos->nombre_manga;
                $ticket->nombre_srvidor = $masDatos->nombre_srvidor;
            }
        

        $historialTicket = DB::select("SELECT t.*,u.nombre_user, u.apellido_user FROM ticket_histories AS t
                                             INNER JOIN users AS u ON t.user_th = u.id_user
                                              WHERE t.ticket_th = ?",[$request->ticket]);

        $ticket->historial = $historialTicket;


        return response()->json($ticket);
    }

    
    public function store(Request $request)
    {
        $servicio =DB::select("SELECT * FROM servicios WHERE cliente_srv = ?",[$request->id]);
        $idServicio = $servicio[0]->id_srv;
        $fecha = date("Y-m-d H:i:s");
        $problema1 = "";
        $problema2 = "";
        $problema3 = "";
        $problema4 = "";

        $result = DB::select("INSERT INTO soportes(servicio_soporte, importancia_soporte,status_soporte,afectacion_soporte, tipo_soporte,comment_soporte,user_soporte,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",[$idServicio,1,1,1,2,NULL,$request->id_user,$fecha,$fecha]);

        $result2 = DB::select("SELECT * FROM soportes WHERE tipo_soporte = 2 ORDER BY id_soporte DESC LIMIT 1")[0]->id_soporte;

        $id_ticket = $result2;

        if($request->check1){
            $problema1 = "lentitud";
            $qproblema1 = DB::insert("INSERT INTO ticket_problems(ticket_pb,problem_pb,created_at,updated_at) VALUES (?,?,?,?)",[$id_ticket,$problema1,$fecha,$fecha]);
        }

        if($request->check2){
            $problema2 = "desconectado";
            $qproblema2 = DB::insert("INSERT INTO ticket_problems(ticket_pb,problem_pb,created_at,updated_at) VALUES (?,?,?,?)",[$id_ticket,$problema2,$fecha,$fecha]);
        }

        if($request->check3){
            $problema3 = "degradado";
            $qproblema3 = DB::insert("INSERT INTO ticket_problems(ticket_pb,problem_pb,created_at,updated_at) VALUES (?,?,?,?)",[$id_ticket,$problema3,$fecha,$fecha]);
        }

        if($request->check4){
            $problema4 = "inaccesible";
            $qproblema3 = DB::insert("INSERT INTO ticket_problems(ticket_pb,problem_pb,created_at,updated_at) VALUES (?,?,?,?)",[$id_ticket,$problema4,$fecha,$fecha]);
        }

        $result3 = DB::insert("INSERT INTO ticket_histories(ticket_th,user_th,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_ticket,$request->id_user,"Se realiza la apertura del ticket",$fecha,$fecha]);

        //historico_cliente::create(['history' => 'Ticket nuevo para el cliente: ' . $request->id . ' servicio: ' . $user->name_plan, 'modulo' => 'Soporte', 'cliente' => $user->cliente_srv, 'responsable' => $responsable]);
        //historico::create(['responsable' => $responsable, 'modulo' => 'Soporte', 'detalle' => 'Creo el ticket ' . $id->id . ' para el cliente ' . $cliente]);

         return response()->json($result3);
    }

    public function guardarAveria(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $guardarAveria = DB::insert("INSERT INTO averias(tipo_averia,status_averia,lugar_averia,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$request->tecnologia,1,$request->lugar,$request->usuario,$fecha,$fecha]);

        $id_averia = DB::select("SELECT * FROM averias ORDER BY id_averia DESC LIMIT 1")["0"]->id_averia;

        $guardarComentarios = DB::insert("INSERT INTO averia_histories(averia_av,user_av,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_averia,$request->usuario,"Creacion de Averia",$fecha,$fecha]);
        $guardarComentarios = DB::insert("INSERT INTO averia_histories(averia_av,user_av,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_averia,$request->usuario,$request->comentario,$fecha,$fecha]);

        return response()->json($request);
    }

    public function detallesAveria(Request $request){

        $averia = DB::select("SELECT * FROM averias AS a
                                INNER JOIN users AS u ON a.responsable = u.id_user
                                    WHERE id_averia = ?",[$request->averia])["0"];

            if ($averia->tipo_averia == 1) {
                $masDatos = DB::select("SELECT * FROM celdas AS c 
                                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                                WHERE c.id_celda = ?",[$averia->lugar_averia])["0"];
                
                $averia->nombre_celda = $masDatos->nombre_celda;
                $averia->nombre_srvidor = $masDatos->nombre_srvidor;

            }else{
                $masDatos = DB::select("SELECT * FROM  manga_empalme AS m
                                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                                WHERE m.id_manga = ?",[$averia->lugar_averia])["0"];

                $averia->nombre_celda = $masDatos->nombre_manga;
                $averia->nombre_srvidor = $masDatos->nombre_srvidor;
            }
        

        $historialAveria = DB::select("SELECT * FROM averia_histories AS a
                                             INNER JOIN users AS u ON a.user_av = u.id_user
                                              WHERE a.averia_av = ?",[$request->averia]);

        $averia->historial = $historialAveria;


        return response()->json($averia);
    }

    public function aggComentarioTicket(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $aggComentario = DB::insert("INSERT INTO ticket_histories(ticket_th,user_th,comment,created_at,updated_at) VALUES (?,?,?,?,?) ",[$request->id,$request->usuario,$request->comentario,$fecha,$fecha]);

        if ($request->tipo == 1) {
            $aggComentario = DB::insert("INSERT INTO ticket_histories(ticket_th,user_th,comment,created_at,updated_at) VALUES (?,?,?,?,?) ",[$request->id,$request->usuario,"Se cierra Ticket",$fecha,$fecha]);
            $actualizarAveria = DB::update("UPDATE soportes SET status_soporte = 2 WHERE id_soporte = ?",[$request->id]);
        }

        return response()->json($request);
    }

    public function aggComentarioAveria(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $aggComentario = DB::insert("INSERT INTO averia_histories(averia_av,user_av,comment,created_at,updated_at) VALUES (?,?,?,?,?) ",[$request->id,$request->usuario,$request->comentario,$fecha,$fecha]);

        if ($request->tipo == 1) {
            $aggComentario = DB::insert("INSERT INTO averia_histories(averia_av,user_av,comment,created_at,updated_at) VALUES (?,?,?,?,?) ",[$request->id,$request->usuario,"Se cierra Averia",$fecha,$fecha]);
            $actualizarAveria = DB::update("UPDATE averias SET status_averia = 2 WHERE id_averia = ?",[$request->id]);
        }

        return response()->json($request);
    }

    public function guardarReposicion(Request $request)
    {
        
        $fecha = date("Y-m-d H:i:s");
        
        $guardarReposicion = DB::insert("INSERT INTO reposicion_equipos(cliente_reposicion,servicio_reposicion,lugar_reposicion,equipo_reposicion,status_reposicion,comentario_reposicion,responsable_reposicion,created_at,updated_at)
                                             VALUES (?,?,?,?,?,?,?,?,?)",
                                             [$request->cliente,$request->servicio,$request->lugar,$request->id_equipo,1,$request->comentario,$request->usuario,$fecha,$fecha]);
        
        $actualizarEstatusEquipo = DB::select("UPDATE articulos SET estatus = 2 WHERE id_articulo = ?",[$request->id_equipo]);


        if($request->id_equipo != 0 ){
            $datosEquipo = DB::select("SELECT * FROM articulos WHERE id_articulo = ?",[$request->id_equipo])["0"];
        }

        $id_reposicion = DB::select("SELECT * FROM reposicion_equipos ORDER BY id_reposicion DESC LIMIT 1")["0"]->id_reposicion;

        if($request->id_equipo != 0 ){
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Apertura de Reposicion de Equipo por Modulo de Soporte para el equipo ".$datosEquipo->modelo_articulo.", con serial: ".$datosEquipo->serial_articulo,$fecha,$fecha]);
            $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_reposicion,$request->usuario,"Creacion de Reposicion",$fecha,$fecha]);
            $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_reposicion,$request->usuario,$request->comentario,$fecha,$fecha]);
            $historicoCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Apertura de Reposicion de Equipo","Soporte",$request->cliente,$request->usuario,null,$fecha,$fecha]);
        }else{
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Apertura por Modulo de Soporte para mudanza de equipo",$fecha,$fecha]);
            $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_reposicion,$request->usuario,"Creacion de Mudanza",$fecha,$fecha]);
            $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$id_reposicion,$request->usuario,$request->comentario,$fecha,$fecha]);
            $historicoCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Apertura de Mudanza de Equipo","Soporte",$request->cliente,$request->usuario,null,$fecha,$fecha]);
        }

        return response()->json($request);
    }

    public function detallesReposicion(Request $request)
    {
        $repo = DB::select("SELECT * FROM reposicion_equipos AS r
                                        INNER JOIN clientes AS c ON r.cliente_reposicion = c.id
                                        INNER JOIN servicios AS s ON c.id = s.cliente_srv
                                        INNER JOIN articulos AS a ON r.equipo_reposicion = a.id_articulo
                                        INNER JOIN users AS u ON r.responsable_reposicion = u.id_user
                                            WHERE id_reposicion = ?",[$request->repo])["0"];

       
            if ($repo->tipo_srv == 1) {
                $masDatos = DB::select("SELECT * FROM celdas AS c 
                            INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                WHERE c.id_celda = ?",[$repo->lugar_reposicion])["0"];

                $repo->nombre_celda = $masDatos->nombre_celda;
                $repo->nombre_srvidor = $masDatos->nombre_srvidor;
            } else {
                $masDatos = DB::select("SELECT * FROM caja_distribucion AS c 
                            INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                            INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                            INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                WHERE c.id_caja = ?",[$repo->lugar_reposicion])["0"];

                $repo->nombre_celda = $masDatos->nombre_caja;
                $repo->nombre_srvidor = $masDatos->nombre_srvidor;
            }

            $historialRepo = DB::select("SELECT * FROM reposicion_histories AS r
                                            INNER JOIN users AS u ON r.user_rh = u.id_user
                                                WHERE r.reposicion_rh = ?",[$request->repo]);

            $repo->historial = $historialRepo;

            return response()->json($repo);
    }

    public function cerrarReposicion(Request $request)
    {   
        $fecha = date("Y-m-d H:i:s");
        $datos = $request->datos;
        
        if($request->tipo == 1){

            $actualizarEstatusRepo = DB::update("UPDATE reposicion_equipos SET status_reposicion = 2 WHERE id_reposicion = ?",[$datos["id_reposicion"]]);
            $actualizasEstatusEquipo = DB::update("UPDATE articulos SET estatus = 3 WHERE id_articulo = ?",[$datos["id_articulo"]]);
          

            if($datos["id_articulo"] != 0){
                $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Aprobacion de Reposicion de Equipos, ID:".$datos["id_reposicion"],$fecha,$fecha]);

                $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$datos["id_reposicion"],$request->usuario,"Aprobacion de Reposicion",$fecha,$fecha]);
                $id_equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["modelo_articulo"]])["0"]->id_equipo;

                $actualizarServicio = DB::update("UPDATE servicios SET equipo_srv = ?, serial_srv = ? WHERE id_srv = ?",[$id_equipo,$datos["serial_articulo"],$datos["id_srv"]]);
                $historicoCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Aprobada Reposicion de Equipo","Soporte",$datos["cliente_srv"],$request->usuario,null,$fecha,$fecha]);
            }else{

                $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Aprobacion de Mudamza de Equipos, ID:".$datos["id_reposicion"],$fecha,$fecha]);

                $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$datos["id_reposicion"],$request->usuario,"Aprobacion de Mudanza",$fecha,$fecha]);
               
                $historicoCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Aprobada Mudanza de Equipo","Soporte",$datos["cliente_srv"],$request->usuario,null,$fecha,$fecha]);

            }


        }else{

            $actualizarEstatusRepo = DB::update("UPDATE reposicion_equipos SET status_reposicion = 3 WHERE id_reposicion = ?",[$datos["id_reposicion"]]);   
            $actualizasEstatusEquipo = DB::update("UPDATE articulos SET estatus = 1 WHERE id_articulo = ?",[$datos["id_articulo"]]);
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Rechazo de Reposicion de Equipos, ID:".$datos["id_reposicion"],$fecha,$fecha]);

            $guardarComentarios = DB::insert("INSERT INTO reposicion_histories(reposicion_rh,user_rh,comment,created_at,updated_at) VALUES(?,?,?,?,?)",[$datos["id_reposicion"],$request->usuario,"Rechazo de Reposicion",$fecha,$fecha]);

            $historicoCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Rechazada Reposicion de Equipo","Soporte",$datos["cliente_srv"],$request->usuario,null,$fecha,$fecha]);


        }
        
        

        return response()->json($datos);
    }


}
