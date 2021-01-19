<?php

namespace App\Http\Controllers;

use App\oinstall;
use App\oinstallpgo;
use Illuminate\Http\Request;

class oinstallpgoController extends Controller
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
        /*$example= $request->prueba;

        for ($i=0; $i< count($example); $i++){


        foreach($request->all() as $request->prueba) {*/
        $oinstalaciones = oinstall::where('ticket', $request->prueba );
        $oinstalaciones->update(['status_pgo'=>2]);
        return oinstallpgo::create($request->all());


        /*}
        }*/
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\oinstallpgo  $oinstallpgo
     * @return \Illuminate\Http\Response
     */
    public function show(oinstallpgo $oinstallpgo)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\oinstallpgo  $oinstallpgo
     * @return \Illuminate\Http\Response
     */
    public function edit(oinstallpgo $oinstallpgo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\oinstallpgo  $oinstallpgo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, oinstallpgo $oinstallpgo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\oinstallpgo  $oinstallpgo
     * @return \Illuminate\Http\Response
     */
    public function destroy(oinstallpgo $oinstallpgo)
    {
        //
    }
}
