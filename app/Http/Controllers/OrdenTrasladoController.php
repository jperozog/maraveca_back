<?php

namespace App\Http\Controllers;

use App\orden_Traslado;
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrdenTrasladoController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function store(Request $request)
    {
       
        $id_transferencia = $request->input("id_transferencia");
        $chofer = $request->input("chofer");
        $cedula = $request->input("cedula");
        $modelo = $request ->input('modelo');
        $color = $request ->input('color');
        $placa = $request ->input('placa');
        $ano = $request ->input('ano');
        
        $result = DB::update('INSERT INTO orden_traslado (id_transferencia,chofer,cedula,vehiculo,color,placa,año_vehiculo)
                        VALUES (?,?,?,?,?,?,?)',
                        [$id_transferencia,$chofer,$cedula,$modelo,$color,$placa,$ano]);
        
        return response()-> json($result);
    }

    public function traerDatos($id, $filtro){

        if ($filtro == 1) {
            $contador = 0; 
            $modelos = DB::select('SELECT * FROM orden_traslado AS o 
                                    INNER JOIN transferencias_equipos AS t ON o.id_transferencia = t.id_transferencia
                                    INNER JOIN articulos AS a ON t.id_equipo = a.id_articulo
                                    INNER JOIN zonas2 as z ON t.desde = z.id_zona
                                        WHERE o.id_transferencia = ? GROUP BY modelo_articulo',[$id]);

            $hacia = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$modelos["0"]->hacia])["0"];
            
            $modelos["0"]->sedeHacia = $hacia->nombre_zona; 

            foreach ($modelos as $modelo) {

                    $articulos = DB::select('SELECT * FROM transferencias_equipos AS t 
                                            INNER JOIN articulos AS a ON t.id_equipo = a.id_articulo
                                                WHERE t.id_transferencia = ? AND a.modelo_articulo = ?',[$id, $modelo->modelo_articulo]);

                    foreach ($articulos as $a) {
                            $contador++;
                    }
                    
                    $contador++;                            
                    $modelo->contador=$contador; 
                    $modelo->articulos=$articulos;     
            }
        } else {
            $contador = 0; 
            $modelos = DB::select('SELECT t.*,o.*,e.*,z.*,c.unidad FROM orden_traslado AS o 
                                    INNER JOIN transferencias_consumibles AS t ON o.id_transferencia = t.id_transferencia
                                    INNER JOIN consumibles AS c ON t.id_consumible = c.id_consumible
                                    INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo
                                    INNER JOIN zonas2 as z ON t.desde = z.id_zona
                                        WHERE o.id_transferencia = ? GROUP BY nombre_equipo',[$id]);
            
            $hacia = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$modelos["0"]->hacia])["0"];
            
            $modelos["0"]->sedeHacia = $hacia->nombre_zona; 
                                        
        } 
        return response()-> json($modelos);
    }

    public function traerDatosTraslado($id){

        $contador = 0; 
            $modelos = DB::select('SELECT * FROM traslado AS t
                                    INNER JOIN articulos AS a ON t.id_equipo = a.id_articulo
                                    INNER JOIN vehiculos AS v ON t.vehiculo = v.id_vehiculo
                                    INNER JOIN users AS u ON t.responsable = u.id_user
                                        WHERE t.id_traslado = ? GROUP BY modelo_articulo',[$id]);

            foreach ($modelos as $modelo) {

                    $articulos = DB::select('SELECT * FROM traslado AS t
                                            INNER JOIN articulos AS a ON t.id_equipo = a.id_articulo
                                                WHERE t.id_traslado = ? AND a.modelo_articulo = ?',[$id, $modelo->modelo_articulo]);

                    foreach ($articulos as $a) {
                            $contador++;
                    }
                    
                                
                    $modelo->contador=$contador; 
                    $modelo->articulos=$articulos;     
            }

       return response()->json($modelos);     
    }

    


    public function traerDatosChofer($id){
            $result = DB::select('SELECT * FROM users WHERE id_user =:id',['id'=>$id]);
    
            return response()-> json($result);
    }    

    public function traerOrdenTraslado(){
        $result = DB::select('SELECT * FROM orden_traslado ORDER BY id_traslado DESC LIMIT 1');
        

       return response()->json($result);
    }

    public function traerTraslados(){

        $result = DB::select("SELECT * FROM traslado AS t 
                                INNER JOIN vehiculos AS v ON t.vehiculo = v.id_vehiculo
                                INNER JOIN users AS u ON t.responsable = u.id_user
                                GROUP BY t.id_traslado");

        return response()->json($result);
    }

    public function traerDatosConductores(){

        $result = DB::select("SELECT * FROM conductores");

        return response()->json($result);
    }

    public function traerDatosVehiculos(){

        $result = DB::select("SELECT * FROM vehiculos");

        return response()->json($result);
    }

    public function realizarTraslado(Request $request){
        $idtransferencia = rand();
        $fecha = date("Y-m-d H:i:s");
        $equipos = $request->equipos;

        foreach ($equipos as $e) {

            $result3 =  DB::update('INSERT INTO traslado (id_traslado,responsable,id_equipo,chofer,vehiculo,desde,hasta,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)', [$idtransferencia,$request->usuario,$e["id_articulo"],$request->chofer,$request->vehiculo,$request->desde,$request->hasta,$fecha,$fecha]);

        }

        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Generado traslado, Codigo de transferencia: ".$idtransferencia,$fecha,$fecha]);

        return response()->json($equipos);
    }



   public function agregarVehiculo(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $agregarVehiculo = DB::select("INSERT INTO vehiculos(marca,modelo,color,placa,anio) VALUES (?,?,?,?,?)",[$request->marca,$request->modelo,$request->color,$request->placa,$request->año]);

        
        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Añadio en nuevo Vehiculo para el uso de traslados",$fecha,$fecha]);

       return response()->json($request);
   }
}
