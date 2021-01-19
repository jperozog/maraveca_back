<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\pagos_comisiones;

class pagos_comisionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $result=DB::table('pagos_comisiones')
            ->orderBy('pagos_comisiones.created_at','DSC')
            ->get();

        return response()->json($result);
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

    /**
     * Display the specified resource.
     *
     * @param  \App\pagos_comisiones  $pagos_comisiones
     * @return \Illuminate\Http\Response
     */
    public function show(pagos_comisiones $pagos_comisiones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\pagos_comisiones  $pagos_comisiones
     * @return \Illuminate\Http\Response
     */
    public function edit(pagos_comisiones $pagos_comisiones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\pagos_comisiones  $pagos_comisiones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pagos_comisiones $pagos_comisiones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\pagos_comisiones  $pagos_comisiones
     * @return \Illuminate\Http\Response
     */
    public function destroy(pagos_comisiones $pagos_comisiones)
    {
        //
    }

    public function comision_user(Request $request, $id)
    {
        if(isset($request->year)&&$request->year!=''){
            $year=$request->year;
        }else{
            $year=date('Y');
        }
        if(isset($request->month)&&$request->month!=''){
            $month=$request->month;
        }else{
            $month=date('n');
        }

        $result=DB::table('pagos_comisiones')
            ->where('pagos_comisiones.user_comision', $id)
            ->where('pagos_comisiones.month', $month)
            ->where('pagos_comisiones.year',  $year)
            ->orderBy('created_at', 'DESC')
           ->get();
        return collect(['datos'=>$result]);
    }


}
