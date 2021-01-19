<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\ap;

class aps extends Controller
{
 //
    public function index()
    {
        return DB::select("SELECT * FROM aps ORDER BY celda_ap ASC");
    }

    public function show($id)
    {
        return ap::find($id);
    }

    public function store(Request $request)
    {       
        $fecha = date("Y-m-d H:i:s");
        $result = DB::insert("INSERT INTO aps(nombre_ap,ip_ap,user_ap,password_ap,celda_ap,created_at,updated_at) VALUES (?,?,?,?,?,?,?) ",[$request->ap,$request->ip,$request->usuario,$request->clave,$request->celda,$fecha,$fecha]);

        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $ap = ap::findOrFail($id);
        $ap->update($request->all());

        return $ap;
    }

    public function delete(Request $request, $id)
    {
        $ap = ap::findOrFail($id);
        $ap->delete();

        return 204;
    }
    //
}
