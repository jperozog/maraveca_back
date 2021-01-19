<?php

namespace App\Http\Controllers;

use App\fac_det;
use Illuminate\Http\Request;

class FacDetController extends Controller
{
  public function index()
  {
      return fac_det::all();
  }

  public function store(Request $request)
  {
      return fac_det::create($request->all());
  }

  public function delete(Request $request, $id)
  {
      $fac = fac_det::findOrFail($id);
      $fac->delete();

      return 204;
  }
}
