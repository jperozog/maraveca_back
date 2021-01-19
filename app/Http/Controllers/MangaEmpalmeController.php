<?php

namespace App\Http\Controllers;

use App\mangaEmpalme;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MangaEmpalmeController extends Controller
{
    
    public function index()
    {
        $mangaEmpalme = DB::select("SELECT * FROM manga_empalme as m
                                        INNER JOIN olts as o ON m.olt_manga = o.id_olt");

        return response()->json($mangaEmpalme);
    }

    
 
    public function store(Request $request)
    {
        $fecha = date("Y-m-d H:i:s");
        $puertoDisponible = DB::select("SELECT * FROM puertos_olt WHERE olt_puerto = ? AND estado_puerto = 1 ORDER BY id_puerto ASC LIMIT 1",[$request->olt])["0"];

        $guardarMangaEmpalme = DB::insert("INSERT INTO manga_empalme(nombre_manga,olt_manga,puerto_olt,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->manga,$request->olt,$puertoDisponible->puerto,$fecha,$fecha]);

        $mangaAgregada = DB::select("SELECT * FROM manga_empalme ORDER BY id_manga DESC LIMIT 1")["0"];

        $actualizarPuerto = DB::update("UPDATE puertos_olt SET estado_puerto = 2, manga_puerto = ? WHERE id_puerto = ?",[$mangaAgregada->id_manga,$puertoDisponible->id_puerto]);

        return response()->json($request);
    }
 
    public function traerPuertosDisponibles($id){
        $puertosDisponibles = DB::select("SELECT * FROM puertos_olt WHERE olt_puerto = ? AND estado_puerto = 1",[$id]);
        
        return response()->json($puertosDisponibles);
    }

    public function editarManga(Request $request){
        $datos = $request->datos;

        $editarManga = DB::update("UPDATE manga_empalme SET nombre_manga = ?,olt_manga = ? WHERE id_manga = ?",[$datos["nombre_manga"],$datos["olt_manga"],$datos["id_manga"]]);


        return response()->json($editarManga);
    }

  
}
