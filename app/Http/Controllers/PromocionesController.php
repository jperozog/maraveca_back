<?php

namespace App\Http\Controllers;

use App\promociones;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class PromocionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promociones = DB::select("SELECT * FROM promociones ORDER BY id_promocion DESC");

        return response()->json($promociones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function guardarPromocion(Request $request){
        
        $fecha = date("Y-m-d H:i:s");
        $guardar = DB::insert("INSERT INTO promociones(nombre_promocion,meses,mbGratis,mbAdicionales,duracion,equipoAdi,created_At,updated_at) VALUES (?,?,?,?,?,?,?,?)",[$request->nombre,$request->meses,$request->mbGratis,$request->mbAdicionales,$request->duracion,$request->equipoAdi,$fecha,$fecha]);
        

        return response()->json($guardar);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\promociones  $promociones
     * @return \Illuminate\Http\Response
     */
    public function show(promociones $promociones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\promociones  $promociones
     * @return \Illuminate\Http\Response
     */
    public function edit(promociones $promociones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\promociones  $promociones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, promociones $promociones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\promociones  $promociones
     * @return \Illuminate\Http\Response
     */
    public function destroy(promociones $promociones)
    {
        //
    }
}
