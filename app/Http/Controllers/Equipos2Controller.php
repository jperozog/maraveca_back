<?php

namespace App\Http\Controllers;

use App\equipos2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equipos2Controller extends Controller
{
    
    public function index()
    {
        $result =  DB::select('SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art WHERE tipo_equipo != 8');

        return response()->json($result);
    }

    

    public function traerDatosEquipo($nombre)
    {

        $result =  DB::select('SELECT * FROM equipos2 where nombre_equipo =:nombre',["nombre"=>$nombre]);

        return response()->json($result);

    }

    public function traerEquiposInstalacion($id)
    {

        $result =  DB::select('SELECT * FROM equipos2 AS e
                                 INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art
                                     WHERE tipo_equipo != 8 AND e.tecnologia = ?',[$id]);

        return response()->json($result);

    }

    public function traerTiposEquipos()
    {

        $result =  DB::select('SELECT * FROM tipo_articulos');

        return response()->json($result);

    }

    public function agregarEquipo2(Request $request){

        $result = DB::select("INSERT INTO equipos2(nombre_equipo,tipo_equipo) VALUES (?,?)",[$request->nombre,$request->id]);

        return response()->json($result);
    }


    public function agregarCategoria(Request $request){

        $agregarCategoria = DB::select("INSERT INTO tipo_articulos(nombre_tipo_art) VALUES (?)",[$request->nombreCategoria]);

        return response()->json($agregarCategoria);

    }

}
