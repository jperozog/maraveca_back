<?php

namespace App\Http\Controllers;

use App\Pclientes;
use App\clientes;
use App\equipos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;
use PDF;
class PclientesController extends Controller
{
  //
  public function index()
  {
    $result = DB::select('
  SELECT pclientes.*, count(presupuestos.id) as presupuesto
    FROM `pclientes`
    left join
    presupuestos on presupuestos.cliente = pclientes.id
    and presupuestos.tipo = "p"
    where pclientes.id_cli is null
    GROUP by pclientes.id order by pclientes.id DESC
    ');
    //return $result;
    return response()->json($result);
  }


  public function show($id)
  {
    return Pclientes::find($id);
  }

  public function store(Request $request)
  {
    return Pclientes::create($request->all());
  }
  public function pclientstc(Request $request)
  {
    
    $fecha2 = date("Y-m-d H:i:s");
    $result = DB::insert("INSERT INTO clientes(kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,phone1,phone2,social,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
    [
     $request->kind,
     $request->dni,
     $request->email,
     $request->nombre,
     $request->apellido,
     $request->direccion,
     $request->estado,
     $request->municipio,
     $request->parroquia,
     $request->day_of_birth,
     $request->phone1,
     $request->phon2,
     $request->social,
     $fecha2,
     $fecha2,
    ]);

    $result2 = DB::select("SELECT * FROM clientes ORDER BY id DESC LIMIT 1")[0];     
    //$result =  clientes::create($request->all());
    $post = ['id_cli'=>$result2->id];
    $Pclientes = Pclientes::findOrFail($request->id_pot);
    $Pclientes->update($post);
   // $Pclientes->delete();
      
    return response()->json($result2);

  }

  public function update(Request $request, $id)
  {
    $Pclientes = Pclientes::findOrFail($id);
    $Pclientes->update($request->all());

    return $Pclientes;
  }

  public function delete(Request $request, $id)
  {
    $Pclientes = Pclientes::findOrFail($id);
    $Pclientes->delete();

    return 204;
  }
}
