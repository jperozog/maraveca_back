<?php

namespace App\Http\Controllers;

use App\Permissions;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
  //
  public function index($user)
  {
      return clientes::all();

      //return $result;
      //return response()->json($result);
  }

  public function show($user, $grant)
  {
      return clientes::find($id);
  }

  public function store(Request $request)
  {
      return clientes::create($request->all());
  }

  public function update(Request $request, $id)
  {
      $clientes = clientes::findOrFail($id);
      $clientes->update($request->all());

      return $clientes;
  }

  public function delete(Request $request, $id)
  {
      $clientes = clientes::findOrFail($id);
      $clientes->delete();

      return 204;
  }
}
