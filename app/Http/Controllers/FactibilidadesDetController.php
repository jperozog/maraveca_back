<?php

namespace App\Http\Controllers;

use App\factibilidades_det;
use Illuminate\Http\Request;

class FactibilidadesDetController extends Controller
{
  public function index()
  {
      return factibilidades_det::all();
  }

  public function show($id)
  {
      return factibilidades_det::find($id);
  }

  public function store(Request $request)
  {
      return factibilidades_det::create($request->all());
  }

  public function update(Request $request, $id)
  {
      $factibilidades_det = factibilidades_det::findOrFail($id);
      $factibilidades_det->update($request->all());

      return $factibilidades_det;
  }

  public function delete(Request $request, $id)
  {
      $factibilidades_det = factibilidades_det::findOrFail($id);
      $factibilidades_det->delete();

      return 204;
  }
}
