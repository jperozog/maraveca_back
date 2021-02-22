<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\celdas;
use App\servidores;

class CeldasController extends Controller
{
    //
    public function index()
    {
        //return celdas::all();
        $result=DB::table('celdas')
        ->join('servidores','servidores.id_srvidor','=','celdas.servidor_celda')
        ->where('id_celda', '>', '1')
        ->get();

        return response()->json($result);
    }

    public function show($id)
    {
        return celdas::where('id_celda', '=', $id)->get();
    }

    public function store(Request $request)
    {   
        $fecha = date("Y-m-d H:i:s");
        $result = DB::insert("INSERT INTO celdas(nombre_celda,servidor_celda,created_at,updated_at) VALUES (?,?,?,?)",[$request->celda,$request->mk,$fecha,$fecha]);

        return response()->json($result);
    }

    public function update(Request $request)
    {

        $datos = $request->datos;
        
    
         $actualizarCelda = DB::update("UPDATE celdas SET nombre_celda = ?,servidor_celda = ? WHERE id_celda = ?",
                                      [$datos["nombre_celda"],$datos["servidor_celda"],$datos["id_celda"]]);
        
        return response()->json($datos);
    }

    public function delete(Request $request, $id)
    {
        $celdas = celdas::where('id_celda', '=', $id);
        $celdas->delete();

        return 204;
    }
}