<?php

namespace App\Http\Controllers;

use App\transferencias_equipos;
use App\inventarios;
use Illuminate\Http\Request;

class TransferenciasEquiposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $equipos=implode(',', $request->equipos);
        $trans=['desde'=>$request->desde, 'hacia'=>$request->hacia, 'responsable'=>$request->responsable, 'equipos'=>$equipos];
        return transferencias_equipos::create($trans);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\transferencias_equipos  $transferencias_equipos
     * @return \Illuminate\Http\Response
     */
    public function show(transferencias_equipos $transferencias_equipos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\transferencias_equipos  $transferencias_equipos
     * @return \Illuminate\Http\Response
     */
    public function edit(transferencias_equipos $transferencias_equipos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\transferencias_equipos  $transferencias_equipos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transferencias_equipos = transferencias_equipos::findOrFail($id);
          $transferencias_equipos->update(['status'=>$request->status, 'confirma'=>$request->confirma]);
          if($transferencias_equipos->status == 2){
            $equipos=explode(',', $transferencias_equipos->equipos);
            foreach ($equipos as $e) {
              $t=inventarios::where('id', '=', $e)->update(['zona_inventario'=>$transferencias_equipos->hacia]);
            }
            return $equipos;
          }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\transferencias_equipos  $transferencias_equipos
     * @return \Illuminate\Http\Response
     */
    public function destroy(transferencias_equipos $transferencias_equipos)
    {
        //
    }
}
