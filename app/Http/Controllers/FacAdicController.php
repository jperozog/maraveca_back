<?php

namespace App\Http\Controllers;

use App\fac_adic;
use App\clientes;
use App\planes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FacAdicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
      return DB::table('fac_products')
      ->select('*')
      ->orderBy('precio_articulo','DSC')
      ->where('codigo_factura', '=', $id)
      ->get();
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
        $plan=planes::where('id_plan', $request->codigo_articulo)->get()->first();
        $cliente=clientes::where('id', $request->id_cli)->get()->first();
        return fac_adic::create([
          'id_cli'=>$request->id_cli,
          'codigo_articulo'=>$plan->id_plan,
          'nombre_articulo'=>$plan->name_plan,
          'precio_unitario'=>$plan->cost_plan,
          'IVA'=>'16%',
          'cantidad'=>$request->cantidad,
          'precio_articulo'=>$plan->cost_plan*$request->cantidad,
          'comment_articulo'=>$request->comment_articulo, ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\fac_product  $fac_product
     * @return \Illuminate\Http\Response
     */
    public function show(fac_product $fac_product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\fac_product  $fac_product
     * @return \Illuminate\Http\Response
     */
    public function edit(fac_product $fac_product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\fac_product  $fac_product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, fac_product $fac_product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\fac_product  $fac_product
     * @return \Illuminate\Http\Response
     */
    public function delete(fac_product $fac_product, $id)
    {
        //
        return $producto = fac_product::where('id', $id)->delete();
    }
}
