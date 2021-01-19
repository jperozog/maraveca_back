<?php

namespace App\Http\Controllers;

use App\venta_equipos;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaEquiposController extends Controller
{
    
    public function index()
    {
        $result = DB::select("SELECT * FROM venta_equipo
                                 INNER JOIN users ON venta_equipo.responsable = users.id_user
                                    INNER JOIN articulos ON venta_equipo.id_venta_articulo = articulos.id_articulo ORDER BY id_venta DESC");
        
        return response()->json($result); 
    }

    public function traerDataDetalles($id)
    {
        $result = DB::select("SELECT * FROM venta_equipo
                                    INNER JOIN users ON venta_equipo.responsable = users.id_user
                                    INNER JOIN articulos ON venta_equipo.id_venta_articulo = articulos.id_articulo
                                    INNER JOIN zonas2 ON articulos.id_zona_articulo = zonas2.id_zona 
                                            WHERE id_venta = ?",[$id]);
        
        return response()->json($result); 
    }

    public function store(Request $request)
    {
        $cliente = $request->input('cliente');
        $id_articulo = $request->input('id_articulo');
        $monto = $request->input('monto');
        $usuario = $request->input('usuario');
        $comentario = $request->comentario;
        $fecha = Carbon::createFromTimestamp($request->fecha)->toDateTimeString();
        $tipo =$request->tipo;
        
        $result =  DB::update('INSERT INTO venta_equipo (cliente,monto,responsable,tipo,comentario,id_venta_articulo,fecha_venta) VALUES (?,?,?,?,?,?,?)',[$cliente,$monto,$usuario,$tipo,$comentario,$id_articulo,$fecha]);
        $equipo = DB::select("SELECT * FROM articulos WHERE id_articulo = ?",[$id_articulo])[0];
        $result2 = DB::update('UPDATE articulos  SET  estatus = 5 WHERE id_articulo = ?',[$id_articulo]);

        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$usuario,'Inventario',"Venta de ".$equipo->modelo_articulo." por medio de ".$tipo.", serial: ".$equipo->serial_articulo,$fecha,$fecha]);
        
        return response()->json($result);
    }

    public function traerClientes($data)
    {   
      
        $result = DB::select("SELECT * FROM clientes  where nombre LIKE ? OR dni LIKE ? OR social LIKE ? ", ["%".$data."%","%".$data."%","%".$data."%"]);

        $result2 = DB::select("SELECT * FROM pclientes  where nombre LIKE ? OR dni LIKE ? ", ["%".$data."%","%".$data."%"]);

        $resultado = array_merge($result,$result2);
        
        return response()->json($resultado); 
    }

    public function traerClientesP($data)
    {   
      
        $result = DB::select("SELECT * FROM pclientes  where nombre LIKE ? OR dni LIKE ? ", ["%".$data."%","%".$data."%"]);
        
        return response()->json($result); 
    }

    public function traerSede()
    {
        $result = DB::select("SELECT * FROM zonas2");

        return response()->json($result);
    }

    public function traerEquipo()
    {
        $result = DB::select("SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art WHERE tipo_equipo != 8");

        return response()->json($result);
    }

    public function traerArticulos(Request $request){

        
        $modelo = $request->input('equipo');
        $id = $request->input('sede');
        
        $result =  DB::select('SELECT * FROM articulos INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art WHERE estatus = 1  AND modelo_articulo =:modelo AND id_zona_articulo =:id_zona',["modelo"=>$modelo,"id_zona"=>$id]);
        
        return response()->json($result);
        
    }

    
    public function show(venta_equipos $venta_equipos)
    {
        //
    }

    
    public function edit(venta_equipos $venta_equipos)
    {
        //
    }

   
    public function update(Request $request, venta_equipos $venta_equipos)
    {
        //
    }

   
    public function destroy(venta_equipos $venta_equipos)
    {
        //
    }
}
