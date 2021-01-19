<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\servidores;

class ServidoresController extends Controller
{
  //
  public function index()
  {
      return servidores::all();
  }

  public function show($id)
  {
      return servidores::where('id_srvidor', '=', $id)->where('id_srvidor', '>', '1')->get();
  }

  public function store(Request $request)
  {
      return servidores::create($request->all());
  }

  public function update(Request $request, $id)
  {
      //$servidores = servidores::findOrFail($id);
      $servidores = servidores::where('id_srvidor', '=', $id);
      $servidores->update($request->all());

      return $request;
  }

  public function delete(Request $request, $id)
  {
      $servidores = servidores::where('id_srvidor', '=', $id);
      $servidores->delete();

      return 204;
  }
}
