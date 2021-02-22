<?php

namespace App\Http\Controllers;

use App\clientePotencial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientePotencialController extends Controller
{
 
    public function index($id)
    {
        $permisoSeniat = DB::select("SELECT * FROM permissions WHERE user = ? AND perm = 'seniat'",[$id]);

        if (count($permisoSeniat) > 0) {
        $clientes= DB::select("SELECT * FROM pclientes WHERE (serie = 1 OR serie = 0 ) AND (kind = 'J' OR kind = 'G') AND id_cli IS NULL ORDER BY created_at DESC");
        } else {
        $clientes = DB::select("SELECT * FROM pclientes WHERE id_cli IS NULL ORDER BY id DESC");
        }

       

        return response()->json($clientes);
    }

    public function traerFactibilidadPCliente($id){
        
        $factibilidad = DB::select("SELECT * FROM factibilidades WHERE id_pot = ?",[$id]);

        foreach ($factibilidad as $f) {
            
            if($f->factible == 1 || $f->factible == 4){
                $detalles = DB::select("SELECT * FROM factibilidades_dets WHERE id_fac = ?",[$f->id]);
                foreach ($detalles as $d) {
                    if ($d->nombre == "celda") {
                        $celda = DB::select("SELECT * FROM celdas WHERE id_celda = ?",[$d->valor])["0"];
                        $f->nombreCelda = $celda->nombre_celda;
                        $f->idCelda = $celda->id_celda;
                    }
                    if ($d->nombre == "equipo") {
                        $equipo = DB::select("SELECT * FROM equipos2 WHERE id_equipo = ?",[$d->valor])["0"];
                        $f->nombreEquipo = $equipo->nombre_equipo;
                        $f->idEquipos = $equipo->id_equipo;
                    }
                    if ($d->nombre == "altura") {
                        $f->altura = $d->valor;
                    }/*
                    if ($d->nombre == "usuario") {
                        $usuario = DB::select("SELECT * FROM users WHERE id_user = ?",[$d->valor])["0"];
                        $f->usuario_responsable = $usuario->nombre_user." ".$usuario->apellido_user;
                    }
                    */
                    if ($d->nombre == "ptp") {
                        $equipo = DB::select("SELECT * FROM equipos2 WHERE id_equipo = ?",[$d->valor]);
                        if ($equipo != []) {
                            $f->nombrePtp = $equipo["0"]->nombre_equipo;
                            $f->idPtp = $equipo["0"]->id_equipo;
                        }else{
                            $f->nombrePtp = "No Requerido";
                            $f->idPtp = 0;
                        }
                    }
                }
            }
            
        }

        return response()->json($factibilidad);

    }

    
    public function store(Request $request)
    {
        $fecha = Carbon::createFromTimestamp($request->fecha)->toDateTimeString();
        $fecha2 = date("Y-m-d H:i:s");

        if($request->facturable == true){
            $result = DB::insert("INSERT INTO pclientes(kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,serie,phone1,phone2,social,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
             $request->kni,
             $request->dni,
             $request->email,
             $request->nombres,
             $request->apellidos,
             $request->direccion,
             $request->estado,
             $request->municipio,
             $request->parroquia,
             $fecha,
             1,
             $request->numero,
             $request->numero,
             $request->social,
             $fecha2,
             $fecha2,
            ]);
        }else{
            $result = DB::insert("INSERT INTO pclientes(kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,phone1,phone2,social,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
             $request->kni,
             $request->dni,
             $request->email,
             $request->nombres,
             $request->apellidos,
             $request->direccion,
             $request->estado,
             $request->municipio,
             $request->parroquia,
             $fecha,
             $request->numero,
             $request->numero,
             $request->social,
             $fecha2,
             $fecha2,
            ]);
        }    

       

    return response()->json($result);
    }

    public function editarPcliente(Request $request){
        $datos = $request["datos"];
        
        $editarCliente = DB::update("UPDATE pclientes SET kind = ?, dni = ?, email = ?, nombre = ?, apellido = ?, direccion = ?, estado = ?, municipio = ?, parroquia = ?, day_of_birth = ?, phone1 = ?, phone2 = ?, social = ? WHERE id = ?",[
                                        $datos["kind"],
                                        $datos["dni"],
                                        $datos["email"],
                                        $datos["nombre"],
                                        $datos["apellido"],
                                        $datos["direccion"],
                                        $datos["estado"],
                                        $datos["municipio"],
                                        $datos["parroquia"],
                                        $datos["day_of_birth"],
                                        $datos["phone1"],
                                        $datos["phone1"],
                                        $datos["social"],
                                        $datos["id"]
        ]);
            

        return response()->json($editarCliente);
    }

  
}