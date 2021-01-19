<?php

namespace App\Http\Controllers;

use App\olt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OltController extends Controller
{
  
    public function index()
    {
        $olts = DB::select("SELECT * FROM olts");

        return response()->json($olts);
    }

   
    public function store(Request $request)
    {
        $fecha = date("Y-m-d H:i:s");
        $guardarOlt = DB::insert("INSERT INTO olts(nombre_olt,ip_olt,puertos_olt,servidor_olt,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$request->olt,"0",$request->puertos,$request->mk,$fecha,$fecha]);

        $traerOLT = DB::select("SELECT * FROM olts ORDER BY id_olt DESC LIMIT 1");

        $id_olt = $traerOLT["0"]->id_olt;

        $puertos = $request->puertos;

       for ($i=1; $i <= $puertos ; $i++) { 
           $guardarPuerto = DB::insert("INSERT INTO puertos_olt(puerto,estado_puerto,manga_puerto,olt_puerto) VALUES (?,?,?,?)",[$i,1,0,$id_olt]);
       }


        return response()->json($request);
    }

    public function editarOlt(Request $request){
        $datos = $request->datos;

        $editarOlt = DB::update("UPDATE olts SET nombre_olt = ?, puertos_olt = ?, servidor_olt = ? WHERE id_olt = ?",[$datos["nombre_olt"],$datos["puertos_olt"],$datos["servidor_olt"],$datos["id_olt"]]);


        return response()->json($editarOlt);
    }

    
}
