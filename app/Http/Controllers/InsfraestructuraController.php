<?php

namespace App\Http\Controllers;

use App\insfraestructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class InsfraestructuraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = DB::select('SELECT * FROM equipos_infraestructura AS e INNER JOIN users AS u ON e. responsable = u.id_user ORDER BY id_infra DESC');
        
        foreach ($result as $infra) {
            if($infra->status == 2){
                $incidencia = DB::select("SELECT i.*,u.nombre_user,u.apellido_user FROM incidencias_equipo AS i
                                            INNER JOIN users AS u ON i.responsable = u.id_user
                                                 WHERE i.equipo = ?",[$infra->id_equipo_infra])["0"];

                $infra->incidencia = $incidencia->incidencia;
                $infra->comentarioIncidencia = $incidencia->comentario;
                $infra->resposanbleIncidencia = $incidencia->nombre_user." ". $incidencia->apellido_user;
                $infra->creacionIncidencia = $incidencia->created_at;
            }   

            if ($infra->id_equipo_infra != 0) {
                $detallesEquipo = DB::select("SELECT * FROM articulos WHERE id_articulo = ?",[$infra->id_equipo_infra])["0"];
                $infra->modelo_articulo = $detallesEquipo->modelo_articulo;
                $infra->serial_articulo = $detallesEquipo->serial_articulo;
            } else {
               $detallesConsumible = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE id_consumible = ?",[$infra->id_consumible])["0"];
               $infra->modelo_articulo = $detallesConsumible->nombre_equipo;
               $infra->serial_articulo = $infra->cantidad." ".$detallesConsumible->unidad;
            }
            
        }

       return response()->json($result);
    }


    public function traerZonasPermisos(){
        $result = DB::select('SELECT * FROM zonas2');

        return response()->json($result);
    }

    public function traerZonas(){
        $result = DB::select('SELECT * FROM zonas2 WHERE prioridad = 0');

        return response()->json($result);
    }

    public function traerEquipos(Request $request){
        if ($request->tipo == 1) {
            $result = DB::select('SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art WHERE tipo_equipo != 8');
        } else {
            $result = DB::select("SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art WHERE tipo_equipo = 8");
        }
        

       

        return response()->json($result);
    }

    public function traerDisponibles(Request $request){
        if ($request->tipo == 1) {
            $result = DB::select('SELECT * FROM articulos WHERE modelo_articulo = ? AND id_zona_articulo = ? AND estatus = 1',[$request->modelo, $request->id]);
        }else{
            $result = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE c.id_zona = ? AND e.nombre_equipo = ?",[$request->id,$request->modelo]);
        }
       

        return response()->json($result);
    }


    public function guardarInfra(Request $request){
        
        $fecha =  date("Y-m-d H:i:s");
        if ($request->tipo == 1) {
            
            $result = DB::insert('INSERT INTO equipos_infraestructura(comentario,cantidad,responsable,id_consumible,id_equipo_infra,status, fecha_infra) VALUES (?,?,?,?,?,?,?)',[$request->comentario,1,$request->id_user,0,$request->id,1,$fecha]);
            $equipo = DB::select("SELECT * FROM articulos WHERE id_articulo = ?",[$request->id])[0];
    
            $result2 = DB::update('UPDATE articulos  SET  estatus = 6 WHERE id_articulo = ?',[$request->id]);
    
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Añadido ".$equipo->modelo_articulo." a infraestructura MARAVECA, serial: ".$equipo->serial_articulo,$fecha,$fecha]);  
            
        } else {
            $traerConsumible = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE id_consumible = ?",[$request->id])["0"];
            $nuevaCantidad = $traerConsumible->cantidad - $request->cantidad; 
            $nuevoComentario = $request->cantidad.$traerConsumible->unidad." ".$traerConsumible->nombre_equipo." ".$request->comentario;

            $actConsumible = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$nuevaCantidad,$request->id]);

            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Añadido ".$nuevoComentario.", a infraestructura MARAVECA",$fecha,$fecha]);  
            $result = DB::insert('INSERT INTO equipos_infraestructura(comentario,cantidad,responsable,id_consumible,id_equipo_infra, fecha_infra) VALUES (?,?,?,?,?,?)',[$nuevoComentario,$request->cantidad,$request->id_user,$request->id,0,$fecha]);
        }
        
        return response()->json($nuevoComentario);

    }
    

    public function agregarIncidencia(Request $request){
        $fecha =  date("Y-m-d H:i:s");
        $actEquipo = DB::update("UPDATE equipos_infraestructura SET status = 2 WHERE id_equipo_infra = ?",[$request->id_equipo]);

        $aggIncidencia = DB::insert("INSERT INTO incidencias_equipo(incidencia,comentario,equipo,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$request->incidencia,$request->comentario,$request->id_equipo,$request->id_user,$fecha,$fecha]);

        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Creacion de Incidencia en equipo de infraestructura MARAVECA",$fecha,$fecha]);

        return response()->json($request);
    }
}
