<?php

namespace App\Http\Controllers;

use App\cajaDistribucion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CajaDistribucionController extends Controller
{
   
    public function index()
    {
        $traerCajas = DB::select("SELECT * FROM caja_distribucion AS c
                                             INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                             INNER JOIN zonas2 AS z ON c.zona_caja = z.id_zona");

        return response()->json($traerCajas);
    }

    public function store(Request $request)
    {
        $fecha = date("Y-m-d H:i:s");
        $guardarOlt = DB::insert("INSERT INTO caja_distribucion(nombre_caja,puertos_caja,manga_caja,zona_caja,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$request->caja,$request->puertos,$request->manga,$request->zona,$fecha,$fecha]);

        $traerCaja = DB::select("SELECT * FROM caja_distribucion ORDER BY id_caja DESC LIMIT 1");

        $id_caja = $traerCaja["0"]->id_caja;

        $puertos = $request->puertos;

       for ($i=1; $i <= $puertos ; $i++) { 
           $guardarPuerto = DB::insert("INSERT INTO puertos_cajas(puerto,estado_puerto,cliente_puerto) VALUES (?,?,?)",[$i,1,0]);
       }


        return response()->json($request);
    }

    public function editarCaja(Request $request){
        $datos = $request->datos;

        $editarManga = DB::update("UPDATE caja_distribucion SET nombre_caja = ?,puertos_caja = ?, manga_caja = ?, zona_caja = ? WHERE id_caja = ?",[$datos["nombre_caja"],$datos["puertos_caja"],$datos["manga_caja"],$datos["zona_caja"],$datos["id_caja"]]);


        return response()->json($editarManga);
    }

 
}
