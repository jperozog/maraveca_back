<?php

namespace App\Http\Controllers;

use App\pago_clientes;
use Illuminate\Http\Request;

class PagoClientesController extends Controller
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
      return pago_clientes::create($request->all());
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\pago_clientes  $pago_clientes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return pago_clientes::where('cliente', $id);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\pago_clientes  $pago_clientes
     * @return \Illuminate\Http\Response
     */
    public function edit(pago_clientes $pago_clientes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\pago_clientes  $pago_clientes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pago_clientes $pago_clientes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\pago_clientes  $pago_clientes
     * @return \Illuminate\Http\Response
     */
    public function destroy(pago_clientes $pago_clientes)
    {
        //
    }
}
