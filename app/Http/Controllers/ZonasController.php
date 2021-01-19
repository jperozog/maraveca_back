<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\zonas;
use App\servidores;
use App\historico;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Console\Commands;

class ZonasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zonas = zonas::all();
        foreach ($zonas as $z) {
          $routers=explode(',', $z->routers);
          $detail=[];
          foreach ($routers as $a) {
            $detalles = servidores::where('id_srvidor', $a)->get()->first();
            array_push($detail, $detalles->nombre_srvidor);
          }
          $z->routers1=implode(', ', $detail);
        }
        return $zonas;

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
      $responsable = $request->responsable;
      unset($request['responsable']);
      $request=$request->all();
      $request['routers']=implode(',',$request['routers']);
      $id = zonas::create($request);
      historico::create(['responsable'=>$responsable, 'modulo'=>'Zonas', 'detalle'=>'Nueva Zona '.$request['nombre_zona']]);
      return $id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\zonas  $zonas
     * @return \Illuminate\Http\Response
     */
    public function show(zonas $zonas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\zonas  $zonas
     * @return \Illuminate\Http\Response
     */
    public function edit(zonas $zonas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\zonas  $zonas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      $responsable = $request->responsable;
      $request=$request->all();
      $request['routers']=implode(',',$request['routers']);
      unset($request['responsable']);
      $zonas = zonas::findOrFail($request['id']);
      historico::create(['responsable'=>$responsable, 'modulo'=>'Zonas', 'detalle'=>'Modifico Zona: '.$request['nombre_zona']]);
      $zonas->update($request);
      return $zonas;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\zonas  $zonas
     * @return \Illuminate\Http\Response
     */
    public function destroy(zonas $zonas)
    {
        //
    }
}
