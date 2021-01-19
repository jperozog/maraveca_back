<?php

namespace App\Http\Controllers;

use App\Zonas2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class Zonas2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result =  DB::select('SELECT * FROM zonas2');

        return response()->json($result);
    }

    public function index2()
    {
        $result =  DB::select('SELECT * FROM zonas2 WHERE prioridad = 0');

        return response()->json($result);
    }

    public function traerCategorias()
    {

        $result =  DB::select('SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art AND e.tipo_equipo != 8 ');

        return response()->json($result); 

    }

    public function traerConsumibles()
    {

        $result =  DB::select('SELECT * FROM equipos2 AS e INNER JOIN tipo_articulos as t ON e.tipo_equipo = t.id_tipo_art AND e.tipo_equipo = 8 ');

        return response()->json($result); 

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sede = $request ->input('sede');
        $ubicacion =$request ->input('ubicacion');
        //return response()->json($sede);
        $res = $request -> all() ;
        DB::update('INSERT INTO zonas2 (nombre_zona,ubicacion) VALUES (?,?)', [$sede,$ubicacion]);

        return "Listo";
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Zonas2  $zonas2
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Zonas2 $zonas2)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Zonas2  $zonas2
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        
        DB::delete('DELETE FROM zonas2 WHERE id_zona = :id',
                 [
                        "id"=>$id]
                );
                 

       return "LISTO CAMBIO DE ESTATUS";
    }


    public function traerEquipos1($id)
    {
       
        
        $result = DB::select('SELECT * FROM articulos
                                 INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art
                                      WHERE id_zona_articulo = ?  AND estatus != 0 AND tipo_articulos.id_tipo_art != 8 GROUP BY modelo_articulo ORDER BY articulos.id_articulo DESC',
                 [$id]
                );
                 

                return response()->json($result);
    }



    public function traerEquipos($id,$id2)
    {
       
        
        $result = DB::select('SELECT * FROM articulos
                                 INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art  WHERE id_zona_articulo = ? AND modelo_articulo = ? AND estatus = 1',
                                        [$id,$id2]
                );
                 

                return response()->json($result);
    }

    public function traerEquiposAsignados($id,$id2)
    {
       
        
        $result = DB::select('SELECT * FROM articulos AS a
                                 INNER JOIN tipo_articulos AS t ON a.id_tipo_articulo = t.id_tipo_art
                                      WHERE id_zona_articulo = ? AND modelo_articulo = ? AND estatus = 3',
                                        [$id,$id2]
                );

        foreach ($result as $equipo) {
                $result2 = DB::select("SELECT c.kind,c.dni,c.nombre,c.apellido,c.social,s.serial_srv FROM servicios AS s
                                                 INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                     WHERE serial_srv = ?",[$equipo->serial_articulo]);
                if ($result2 != [] ) {
                    $equipo->nombre = $result2["0"]->nombre;
                    $equipo->apellido = $result2["0"]->apellido;
                    $equipo->kind = $result2["0"]->kind;
                    $equipo->dni = $result2["0"]->dni;
                    $equipo->social = $result2["0"]->social;
                }else{
                    $result3 = DB::select("SELECT * FROM instalaciones AS i
                                             INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                             INNER JOIN clientes AS c ON i.cliente_insta = c.id
                                                    WHERE d.serial_det = ?",[$equipo->serial_articulo]);
                        if ($result3 != [] ) {
                            $equipo->nombre = $result3["0"]->nombre;
                            $equipo->apellido = $result3["0"]->apellido;
                            $equipo->kind = $result3["0"]->kind;
                            $equipo->dni = $result3["0"]->dni;
                            $equipo->social = $result3["0"]->social;                                                 
                        }else{
                        }          
                }
        }
                 

        return response()->json($result);
    }

    public function traerEquiposVendidos($id,$id2)
    {
       
        
        $result = DB::select('SELECT * FROM venta_equipo AS v
                                 INNER JOIN articulos AS a ON v.id_venta_articulo = a.id_articulo
                                      WHERE a.id_zona_articulo = ? AND a.modelo_articulo = ?
                                      .                                                                                                                                              AND estatus = 5',
                                        [$id,$id2]);


       
                 

        return response()->json($result);
    }

    public function chequearConsumible(Request $request){

        $result = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo = ? AND c.id_zona = ?",[$request->equipo,$request->zona]);

        return response()->json($result);
    }

    public function traerHistorial(){
        $result = DB::select("SELECT h.*, u.nombre_user,u.apellido_user FROM historicos AS h INNER JOIN users AS u ON h.responsable = u.id_user WHERE modulo = 'Inventario' ORDER BY h.id DESC");

        return response()->json($result);
    }
}
