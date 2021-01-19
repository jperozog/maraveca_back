<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\buscardor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BuscardorController extends Controller
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
    }

    public function buscador(Request $request)
    {
        $result = DB::select("SELECT * FROM articulos AS a INNER JOIN zonas2 as z ON a.id_zona_articulo = z.id_zona WHERE serial_articulo = ?",[$request->serial]);

        return response()->json($result);
    }

    public function masDetalles(Request $request){
        $datos = $request->equipo;

        if($datos["estatus"] == 3) {
            $equipo = new \stdClass;
          
            $result2 = DB::select("SELECT c.kind,c.dni,c.nombre,c.apellido,c.social,s.serial_srv FROM servicios AS s
                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            WHERE serial_srv = ?",[$datos["serial_articulo"]]);
                                            
            if ($result2 != [] ) {
                $equipo->nombre = $result2["0"]->nombre;
                $equipo->apellido = $result2["0"]->apellido;
                $equipo->kind = $result2["0"]->kind;
                $equipo->dni = $result2["0"]->dni;
                $equipo->social = $result2["0"]->social;
                $equipo->estatus = $datos["estatus"];
            }else{
                $result3 = DB::select("SELECT * FROM instalaciones AS i
                                        INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                        INNER JOIN clientes AS c ON i.cliente_insta = c.id
                                            WHERE d.serial_det = ?",[$datos["serial_articulo"]]);
                $equipo->nombre = $result3["0"]->nombre;
                $equipo->apellido = $result3["0"]->apellido;
                $equipo->kind = $result3["0"]->kind;
                $equipo->dni = $result3["0"]->dni;
                $equipo->social = $result3["0"]->social;   
                $equipo->estatus = $datos["estatus"];                             
             }
             
             return response()->json($equipo);
        }

        if($datos["estatus"] == 5){
            $equipo = new \stdClass;
            $result = DB::select("SELECT * FROM venta_equipo AS v
                                     INNER JOIN articulos AS a on v.id_venta_articulo = a.id_articulo
                                     INNER JOIN users AS u ON v.responsable = u.id_user
                                        WHERE a.serial_articulo = ?",[$datos["serial_articulo"]]);

            $equipo->cliente =  $result["0"]->cliente;
            $equipo->tipo =  $result["0"]->tipo;
            $equipo->usuario =  $result["0"]->nombre_user." ".$result["0"]->apellido_user;
            $equipo->fecha =  $result["0"]->fecha_venta;
            $equipo->estatus = $datos["estatus"]; 

            return response()->json($equipo);            

        }

        if($datos["estatus"] == 6){
            $equipo = new \stdClass;
            $result = DB::select("SELECT * FROM equipos_infraestructura AS e
                                     INNER JOIN articulos AS a on e.id_equipo_infra = a.id_articulo
                                     INNER JOIN users AS u ON e.responsable = u.id_user
                                        WHERE a.serial_articulo = ?",[$datos["serial_articulo"]]);

            $equipo->comentario =  $result["0"]->comentario;
            $equipo->usuario =  $result["0"]->nombre_user." ".$result["0"]->apellido_user;
            $equipo->fecha =  $result["0"]->fecha_infra;
            $equipo->estatus = $datos["estatus"]; 

            return response()->json($equipo);            

        }

        if($datos["estatus"] == 2){
            $equipo = new \stdClass;
            $result = DB::select("SELECT * FROM transferencias_equipos AS t
                                     INNER JOIN articulos AS a on t.id_equipo = a.id_articulo
                                     INNER JOIN users AS u ON t.responsable = u.id_user
                                     INNER JOIN zonas2 AS z ON t.hacia = z.id_zona
                                        WHERE a.serial_articulo = ? ORDER BY t.id DESC",[$datos["serial_articulo"]]);

            $equipo->id_transferencia =  $result["0"]->id_transferencia;
            $equipo->usuario =  $result["0"]->nombre_user." ".$result["0"]->apellido_user;
            $equipo->destino =  $result["0"]->nombre_zona;
            $equipo->estatus = $datos["estatus"]; 

            return response()->json($equipo);            

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\buscardor  $buscardor
     * @return \Illuminate\Http\Response
     */
    public function show(buscardor $buscardor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\buscardor  $buscardor
     * @return \Illuminate\Http\Response
     */
    public function edit(buscardor $buscardor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\buscardor  $buscardor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, buscardor $buscardor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\buscardor  $buscardor
     * @return \Illuminate\Http\Response
     */
    public function destroy(buscardor $buscardor)
    {
        //
    }
}
