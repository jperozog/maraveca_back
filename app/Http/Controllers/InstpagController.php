<?php

namespace App\Http\Controllers;

use App\instinst;
use App\instpag;
use Illuminate\Http\Request;

class InstpagController extends Controller
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
        $instalaciones = instinst::where('installer', $request->installer)->take($request->n)->orderBy('created_at', 'desc');
        $instalaciones->update(['stat'=>2]);
        return instpag::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\instpag  $instpag
     * @return \Illuminate\Http\Response
     */
    public function show(instpag $instpag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\instpag  $instpag
     * @return \Illuminate\Http\Response
     */
    public function edit(instpag $instpag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\instpag  $instpag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, instpag $instpag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\instpag  $instpag
     * @return \Illuminate\Http\Response
     */
    public function destroy(instpag $instpag)
    {
        //
    }
}
