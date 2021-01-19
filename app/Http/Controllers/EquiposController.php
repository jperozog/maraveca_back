<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\equipos;

class EquiposController extends Controller
{
  //
  public function index()
  {
      return equipos::all();
  }

  public function equit($id)
  {
      return equipos::where('tipo', $id)->get();
  }



  public function show($id)
  {
      return equipos::find($id);
  }

  public function store(Request $request)
  {
      return equipos::create($request->all());
  }

  public function update(Request $request, $id)
  {
      $equipos = equipos::findOrFail($id);
      $equipos->update($request->all());

      return $equipos;
  }

  public function delete(Request $request, $id)
  {
      $equipos = equipos::findOrFail($id);
      $equipos->delete();

      return 204;
  }
}
