<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\articulos;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ArticulosController extends Controller
{
    function index()
    {
        $result =  DB::select('SELECT * FROM articulos WHERE estatus != 0');

        return response()->json($result);
    }

    function traerDatoEquipo($id){

        $result = DB::select('SELECT * FROM articulos INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art  WHERE articulos.id_articulo =:id',
                 ["id"=>$id]
                );
                 

                return response()->json($result);
        
    }
    
    public function store(Request $request)
    { 
    
        $serial_caja = $request->cajaSerial;
        $modelo = $request ->modelo;
        $serial = $request ->serial;
        $estatus = 1;
        $id_tipo = $request ->tipo_articulo;
        $id_zona = $request ->tipo_zona;
        $proveedor = $request->proveedor;
        $notaEntrega = $request->notaEntrega;
        
        $result = DB::update('INSERT INTO articulos (serial_caja,proveedor,nota_entrega,modelo_articulo,serial_articulo,estatus,id_tipo_articulo,id_zona_articulo)
                                     VALUES (?,?,?,?,?,?,?,?)',
                                             [$serial_caja,$proveedor,$notaEntrega,$modelo,$serial,$estatus,$id_tipo,$id_zona]);
                                             
            
        return response()->json($request);
    }

    public function storeCola(Request $request)
    {   
      
        $equiposCola = $request->equiposCola;
        
        foreach ($equiposCola as $equipo) {
            $serial_caja = $equipo["id_caja_articulo"];
            $modelo = $equipo["modelo_articulo"];
            $serial = $equipo["serial_articulo"];
            $estatus = $equipo["estatus"];
            $id_tipo = $equipo["id_tipo_articulo"];
            $id_zona = $equipo["id_tipo_zona"];
            $proveedor = $equipo["proveedor"];
            $notaEntrega = $equipo["notaEntrega"];

            $result = DB::update('INSERT INTO articulos (serial_caja,proveedor,nota_entrega,modelo_articulo,serial_articulo,estatus,id_tipo_articulo,id_zona_articulo)
            VALUES (?,?,?,?,?,?,?,?)',
                    [$serial_caja,$proveedor,$notaEntrega,$modelo,$serial,$estatus,$id_tipo,$id_zona]);

        }
        
         return response()->json($request);         
    }

    public function articulosCategorias(Request $request){

        
        $modelo = $request->input('modelo');
        $id = $request->input('id_zona');
            
        $result =  DB::select('SELECT * FROM articulos AS a
                                 INNER JOIN tipo_articulos ON a.id_tipo_articulo = tipo_articulos.id_tipo_art
                                         WHERE estatus != 0 AND modelo_articulo = ? AND id_zona_articulo = ? ORDER BY a.estatus ASC',[$modelo,$id]);
        
        return response()->json($result);
        
    }

    public function articulosCategorias2(Request $request){

        
        $modelo = $request->modelo;
        $id = $request->id_zona;
        
        $result =  DB::select('SELECT * FROM articulos AS a
                                     INNER JOIN tipo_articulos AS t ON a.id_tipo_articulo = t.id_tipo_art
                                         WHERE estatus = 1 AND modelo_articulo = ? AND id_zona_articulo = ? ',[$modelo,$id]);
        
        return response()->json($result);
        
    }

    
    public function articulosCategorias3(Request $request){

        
        $modelo = $request->input('modelo');
        $id = $request->input('id_zona');
        
        $result =  DB::select('SELECT * FROM equipos_grupales AS i
		                            INNER JOIN  articulos AS a ON i.id_articulo_grupal = a.id_articulo
                                         WHERE estatus = 7 AND modelo_articulo = ? AND id_zona_articulo = ? ',[$modelo,$id]);
        
        return response()->json($result);
        
    }
  
    public function update(Request $request, $id)
    {   
        $modelo_articulo = $request ->input('modelo');
        $serial_articulo = $request ->input('serial');
        $id_tipo_articulo = $request ->input('id_tipo');
        $id_zona_articulo = $request ->input('id_zona');
        
        $result = DB::update('UPDATE articulos  SET modelo_articulo = :model, serial_articulo = :seria, estatus = :estatus,  id_tipo_articulo = :id_tipo, id_zona_articulo =:id_zona
                 WHERE id_articulo = :id',
                 ["model"=>$modelo_articulo,
                    "seria"=>$serial_articulo,
                    "estatus" => 1,
                        "id_tipo" =>$id_tipo_articulo,
                        "id_zona" =>$id_zona_articulo,
                        "id"=>$id]
                );
                
                
                return response()->json($result);
    }

    public function Transferir(Request $request,$id){
        
        $equipos = $request->equipos;
        $idzonaEmisor = $request->idzonaEmisor;
        $idtransferencia = rand();
        $usuario = $request->usuario;
        $fecha = date("Y-m-d H:i:s");
        $tipo = $request->tipo;

        $traerVehiculo = DB::select("SELECT * FROM vehiculos WHERE id_vehiculo = ?",[$request->vehiculo])["0"];

        
        if ($tipo == 1) {
            $contador = 0;
            foreach ($equipos as $e) {
                $id_equipo = $request->equipos[$contador];
                $result = DB::select("SELECT * FROM articulos WHERE id_articulo = ?",[$id_equipo])[0];
    
                if ($result->serial_caja != "0") {
                    $result2  = DB::update('UPDATE articulos  SET serial_caja = "0", estatus = 2 WHERE id_articulo = ?',[$id_equipo]);
                }else{
                     $result2 = DB::update('UPDATE articulos  SET estatus = 2 WHERE id_articulo = ?',[$id_equipo]);
                }
    
                $contador++;    
    
                $result3 =  DB::update('INSERT INTO transferencias_equipos (id_transferencia,responsable,desde,id_equipo,hacia,confirma,estatus_trans,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)', [$idtransferencia,$usuario,$idzonaEmisor,$id_equipo,$id,1,6,$fecha,$fecha]);
            }

            $zonaEmisor = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$idzonaEmisor])[0];
            $zonaReceptor = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$id])[0];

            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$usuario,'Inventario',"Genereada Transferencia de Equipos Desde: ".$zonaEmisor->nombre_zona." Hasta: ".$zonaReceptor->nombre_zona.", Codigo de transferencia: ".$idtransferencia,$fecha,$fecha]);



        } else {
            $contador = 0;
            foreach ($equipos as $e) {
                $id_equipo = $request->equipos[$contador]["id_consumible"];
                $cantidad = $request->equipos[$contador]["cantidad"];
                $result = DB::insert("INSERT INTO transferencias_consumibles(id_transferencia,responsable,desde,id_consumible,cantidad,hacia,confirma,estatus_trans,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)",[$idtransferencia,$usuario,$idzonaEmisor,$id_equipo,$cantidad,$id,1,6,$fecha,$fecha]);    
                $contador++;              
            }

            $zonaEmisor = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$idzonaEmisor])[0];
            $zonaReceptor = DB::select("SELECT * FROM zonas2 WHERE id_zona = ?",[$id])[0];

            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$usuario,'Inventario',"Genereada Transferencia de Consumibles Desde: ".$zonaEmisor->nombre_zona." Hasta: ".$zonaReceptor->nombre_zona.", Codigo de transferencia: ".$idtransferencia,$fecha,$fecha]);  

        }
        
        
        
        $agregarOrdenTraslado = DB::update('INSERT INTO orden_traslado (id_transferencia,chofer,cedula,vehiculo,color,placa,año_vehiculo)
                                    VALUES (?,?,?,?,?,?,?)',
                                        [$idtransferencia,$request->chofer,$request->cedula,$traerVehiculo->modelo,$traerVehiculo->color,$traerVehiculo->placa,$traerVehiculo->anio]);
                                        

        return response()->json($agregarOrdenTraslado);
    }


    public function destroy(Request $request,$id)
    {
        
        $result = DB::update('UPDATE articulos  SET estatus = :estatus WHERE id_articulo = :id',
                 [
                    "estatus" => 0,
                        "id"=>$id]
                );
                 

                return response()->json($result);
    }

    public function traerDisponibles($id){
        $result = DB::select('SELECT * FROM articulos INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art  WHERE articulos.id_zona_articulo = :id  AND estatus = 1 AND articulos.id_tipo_articulo != 8',
                 ["id"=>$id]
                );
                 

                return response()->json($result);
    }
    public function traerEnProceso($id){
        $result = DB::select('SELECT * FROM articulos INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art  WHERE tipo_articulos.nombre_tipo_art ="Router" AND articulos.id_zona_articulo = :id AND estatus = 2 AND articulos.id_tipo_articulo != 8',
                 ["id"=>$id]
                );
                 

                return response()->json($result);
    }
    public function traerNoDisponibles($id){
        $result = DB::select('SELECT a.*,t.*,s.id_srv, c.nombre, c.apellido, c.social FROM articulos AS a
                                 INNER JOIN tipo_articulos AS t ON a.id_tipo_articulo = t.id_tipo_art
                                 INNER JOIN servicios AS s ON a.serial_articulo = s.serial_srv
                                 INNER JOIN clientes AS c  ON s.cliente_srv  = c.id
                                      WHERE a.id_zona_articulo = ?  AND (estatus = 3 OR estatus = 4 OR estatus = 5 OR estatus = 6 OR estatus = 7) AND a.id_tipo_articulo != 8',
                 [$id]
                );
                 

                return response()->json($result);
    }

    

    public function busquedaEquipo($zona,$modelo,$id){
        $result = DB::select("SELECT * FROM articulos INNER JOIN tipo_articulos ON articulos.id_tipo_articulo = tipo_articulos.id_tipo_art  where id_zona_articulo = ? AND modelo_articulo = ? AND estatus != 0 AND serial_articulo LIKE ?", [$zona,$modelo,"%".$id."%"]);
        
        return response()->json($result); 
    }

    public function traerConsumibles($id){
        $result = DB::select('SELECT * FROM consumibles AS c
                                         INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo
                                         INNER JOIN zonas2 AS z ON c.id_zona = z.id_zona 
                                           WHERE  c.id_zona = ?',[$id]);

        return response()->json($result);
    }

    public function traerEquiposConsumibles(){
        $result = DB::select('SELECT * FROM equipos2 AS e  WHERE  tipo_equipo = 8');

        return response()->json($result);
    }

    public function agregarEquiposConsumibles(Request $request ){
           
        $result = DB::insert("INSERT INTO consumibles(consumible, cantidad, unidad, id_zona) VALUES (?,?,?,?)",[$request->equipo, $request->cantidad,$request->unidad,$request->id]);

        return response()->json($result);
    }

    public function modificarEquiposConsumibles(Request $request ){
        
        $consumible = DB::select("SELECT * FROM consumibles WHERE id_consumible = ?",[$request->id])["0"];

        $cantidadConsumible  = $consumible->cantidad;

        $nuevaCantidad = $consumible->cantidad + $request->cantidad;
        
        $result = DB::insert("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ? ",[$nuevaCantidad,$request->id]);

        return response()->json($request);
    }

    public function eliminarEquiposConsumibles(Request $request ){
           
        $result = DB::delete("DELETE FROM consumibles WHERE id_consumible = ? ",[$request->idE]);

        return response()->json($request);
    }

    public function traerGrupal(){

        $equiposGrupal = DB::select("SELECT * FROM equipos_grupales AS g 
                                        INNER JOIN articulos AS e ON g.id_articulo_grupal = e.id_articulo");

        return response()->json($equiposGrupal);
    }


    public function agregarGrupal(Request $request){
        $fecha = date("Y-m-d H:i:s");

        $actualizarStatusArticulo = DB::update("UPDATE articulos SET estatus = 7 WHERE id_articulo = ?",[$request->equipo]);

        $agregarGrupal = DB::insert("INSERT INTO equipos_grupales(id_articulo_grupal,comentario_grupal,created_at,updated_at) VALUES (?,?,?,?)",[$request->equipo,$request->comentario,$fecha,$fecha]);

        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Añadio en nuevo Equipo Grupal para el uso de Instalaciones Grupales",$fecha,$fecha]);

        return response()->json($request);
    }

}
