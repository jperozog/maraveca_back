<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\transferencia_equipos;
use Illuminate\Http\Request;

class TransferenciaEquiposController extends Controller
{
    public function index($id)
    {
      if ($id == 1) {
        $result =  DB::select('SELECT t.*, u.nombre_user, u.apellido_user,z.nombre_zona FROM transferencias_equipos as t
            INNER JOIN users AS u ON t.responsable = u.id_user
            INNER JOIN zonas2 AS z ON t.hacia = z.id_zona
              GROUP BY id_transferencia ORDER BY id DESC');
      } else {
          $result =  DB::select('SELECT t.*, u.nombre_user, u.apellido_user,z.nombre_zona FROM transferencias_consumibles as t
            INNER JOIN users AS u ON t.responsable = u.id_user
            INNER JOIN zonas2 AS z ON t.hacia = z.id_zona
              GROUP BY id_transferencia ORDER BY id DESC');
      }
      
      return response()->json($result);

    }

    public function obtenerData($id,$filtro){
      
      if ($filtro == 1) {
        $result =  DB::select('SELECT * FROM transferencias_equipos INNER JOIN articulos on transferencias_equipos.id_equipo = articulos.id_articulo WHERE id_transferencia =:id',["id"=>$id]);
      } else {
        $result =  DB::select('SELECT t.*,e.* FROM transferencias_consumibles AS t
                                 INNER JOIN consumibles AS c ON t.id_consumible = c.id_consumible
                                 INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo
                                     WHERE id_transferencia =:id',["id"=>$id]);
      }


      return response()->json($result);
    }

    
    public function traerEmisor($id){
      

        $result =  DB::select('SELECT * FROM zonas2 WHERE id_zona =:id',["id"=>$id]);
  
        return response()->json($result);
      }

      public function traerResponsable($id){
      

        $result =  DB::select('SELECT * FROM users WHERE id_user =:id',["id"=>$id]);
  
        return response()->json($result);
      }
      
      public function traerConfirmante($id){
      

        $result =  DB::select('SELECT * FROM users WHERE id_user =:id',["id"=>$id]);
  
        return response()->json($result);
      }

      public function traerReceptor($id){
      

        $result =  DB::select('SELECT * FROM zonas2 WHERE id_zona =:id',["id"=>$id]);
  
        return response()->json($result);
      }

      public function autorizar($id,$filtro){
        if ($filtro == 1) {
          $result =  DB::update('UPDATE transferencias_equipos SET estatus_trans = 1 WHERE id_transferencia = ? ',[$id]);
        } else {
          $result =  DB::update('UPDATE transferencias_consumibles SET estatus_trans = 1 WHERE id_transferencia = ? ',[$id]);
        }
        
  
        return response()->json($result);
      }

      public function aceptarTransferir(Request $request){
        $filtro = $request->filtro;

        $equipos = $request->equipos;
        $id = $request->id;
        $limite = count($equipos);
        $id_transferencia = $request->get('equipos')[0]['id_transferencia'];
        $confirmante = $request ->input('confirmante');
        
        if ($filtro == 1) {
          for ($i=0 ; $i <= $limite ; $i++ ) { 
            $id_equipo = $request->get('equipos')[$i]['id_articulo'];
             $result = DB::update('UPDATE articulos  SET id_zona_articulo =:id_zona, estatus =:sta
                  WHERE id_articulo = :id',
                  ["id_zona"=> $id,"sta"=>1,"id"=>$id_equipo]
                 );
                 $result2 = DB::update('UPDATE transferencias_equipos  SET estatus_trans =:sta, confirma =:confirma ,comentario =:comentario WHERE id_transferencia = :id',
                 ["sta"=>2,"confirma"=>$confirmante,"comentario"=>"Transferencia Exitosa!","id"=>$id_transferencia]
                );    
         }
        } else {
          foreach ($equipos as $e) {
                $equipo = DB::select("SELECT * FROM consumibles WHERE id_consumible = ?",[$e["id_consumible"]])[0];
                $cantidadInicial = $equipo->cantidad;
                $cantidad = $e["cantidad"];
                $cantidadFinal = $cantidadInicial - $cantidad;

                $equipo2 = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadFinal,$e["id_consumible"]]);

                $equipo2 = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE c.id_zona = ? AND e.nombre_equipo = ?",[$e["hacia"],$e["nombre_equipo"]])[0];
                $cantidadInicial2 = $equipo2->cantidad;
                $cantidad2 = $e["cantidad"];
                $cantidadFinal2 = $cantidadInicial2 + $cantidad2;
                
                $equipo3 = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadFinal2,$equipo2->id_consumible]);

                $result2 = DB::update('UPDATE transferencias_consumibles  SET estatus_trans =:sta, confirma =:confirma ,comentario =:comentario WHERE id_transferencia = :id',
                 ["sta"=>2,"confirma"=>$confirmante,"comentario"=>"Transferencia Exitosa!","id"=>$id_transferencia]
                );  
          }
        }
                
        return response()->json($request);
      }




      public function aceptarTransferirDetalles(Request $request){
        
        $equipos = $request->input('equipos');
        $texto = $request->input('texto');
        $id = $request->input('id');
        $limite = count($equipos);
        $id_transferencia = $request->get('equipos')[0]['id_transferencia'];
        $confirmante = $request ->input('confirmante');
        
      
       
        
        for ($i=0 ; $i <= $limite ; $i++ ) { 
           $id_equipo = $equipos[$i]["id_articulo"];
            $result = DB::update('UPDATE articulos  SET id_zona_articulo =:id_zona, estatus =:sta
                 WHERE id_articulo = :id',
                 ["id_zona"=> $id,"sta"=>1,"id"=>$id_equipo]
                );
                $result2 = DB::update('UPDATE transferencias_equipos  SET estatus_trans =:sta, confirma =:confirma, comentario=:comment WHERE id_transferencia = :id',
                ["sta"=>3,"confirma"=>$confirmante,"comment"=>$texto,"id"=>$id_transferencia]
               );       
        } 
        
        return response()->json($result2);
      }

      
      public function aceptarTransferirDetalles2(Request $request){
        
        $equipos = $request->input('equipos');
        $id = $request->input('id');
        $limite = count($equipos);
        $confirmante = $request ->input('confirmante');
 
        for ($i=0 ; $i <= $limite ; $i++ ) { 
           $id_equipo = $equipos[$i]["id_articulo"];
            $result = DB::update('UPDATE articulos  SET estatus =:sta
                 WHERE id_articulo = :id',
                 ["sta"=>4,"id"=>$id_equipo]
               );       
        } 
        
        return response()->json($request);
      }




        
        public function negarTransferir(Request $request){
        
          $equipos = $request->input('equipos');
          $id = $request->input('id');
          $limite = count($equipos);
          $id_transferencia = $request->get('equipos')[0]['id_transferencia'];
          $confirmante = $request ->input('confirmante');
          
          for ($i=0 ; $i <= $limite ; $i++ ) { 
             $id_equipo = $request->get('equipos')[$i]['id_articulo'];
              $result = DB::update('UPDATE articulos  SET id_zona_articulo =:id_zona, estatus =:sta
                   WHERE id_articulo = :id',
                   ["id_zona"=> $id,"sta"=>1,"id"=>$id_equipo]
                  );
                  $result2 = DB::update('UPDATE transferencias_equipos  SET estatus_trans =:sta, confirma =:confirma, comentario =:comentario WHERE id_transferencia = :id',
                  ["sta"=>0,"confirma"=>$confirmante,"comentario"=>"Transferencia Cancelada!","id"=>$id_transferencia]
                 );    
          } 
        
        
        return response()->json($result2);
    }  

    public function traerComentario($id){
          $result =  DB::select('SELECT * FROM transferencias_equipos WHERE id_transferencia =:id',["id"=>$id]);
  
        return response()->json($result);
    }

    public function traerInstalador(){
      $result =  DB::select('SELECT * FROM users where installer = 1');

      return response()->json($result);
    }

    public function traerUltimaTransferencia(){
      $result =  DB::select('SELECT * FROM `transferencias_equipos` GROUP by transferencias_equipos.id_transferencia ORDER BY id DESC limit 1');

      return response()->json($result);
    }

    public function traspasoEquipo(request $request){

      $sede = $request->input('sede');
      $id = $request->input('id');


      $result = DB::update('UPDATE articulos  SET id_zona_articulo = ?, estatus = 1 WHERE id_articulo = ?',[$sede,$id]);


      return response()->json($request);
    }

    public function datosCajas($id){

      $result = DB::select("SELECT * FROM `articulos` WHERE serial_caja != '0' AND id_zona_articulo = ? GROUP BY serial_caja",[$id]);

      return response()->json($result);
    }

    public function equiposCajas($id){

      $result = DB::select("SELECT * FROM `articulos` WHERE serial_caja = ?",[$id]);

      return response()->json($result);
    }

    public function modificarSedesTranferencia(Request $request){
      $date = date("Y-m-d H:i:s"); 
      if ($request->tipo == 1) {
        $transferencias = DB::select("SELECT * FROM transferencias_equipos WHERE id_transferencia = ?",[$request->id]);
      }else{
        $transferencias = DB::select("SELECT * FROM transferencias_consumibles WHERE id_transferencia = ?",[$request->id]);
      }

      foreach ($transferencias as $t) {
        if ($request->tipo == 1) {
          $t = DB::select("UPDATE transferencias_equipos SET desde = ?, hacia = ? WHERE id_transferencia = ?",[$request->sede,$request->sede2,$t->id_transferencia]); 
        } else {
          $t = DB::select("UPDATE transferencias_consumibles SET desde = ?, hacia = ? WHERE id_transferencia = ?",[$request->sede,$request->sede2,$t->id_transferencia]); 
        }
        
      }

      $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Modificacion de sedes en transferencia ".$request->id,$date,$date]);

      return response()->json($transferencias);
    }

    public function aggEquiposTranferencia(Request $request){
      $date = date("Y-m-d H:i:s"); 
      $transferencia = DB::select("SELECT * FROM transferencias_equipos WHERE id_transferencia = ?",[$request->id])["0"];

      $desde = $transferencia->desde;
      $hacia = $transferencia->hacia;
      $status = $transferencia->estatus_trans;
      
      foreach ($request->datos as $equipo) {
          $t = DB::insert("INSERT INTO transferencias_equipos(id_transferencia,responsable,desde,id_equipo,hacia,estatus_trans,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?)",[$request->id,$request->usuario,$desde,$equipo["id_articulo"],$hacia,$status,$date,$date]); 

          $e = DB::update("UPDATE articulos SET estatus = 2 WHERE id_articulo = ?",[$equipo["id_articulo"]]);

      }

      $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Equipos Agregados en transferencia ".$request->id,$date,$date]);
      
      return response()->json($request);
    }

    public function delEquiposTranferencia(Request $request){
      $date = date("Y-m-d H:i:s"); 
      if ($request->tipo == 1) {
        $transferencias = DB::select("SELECT * FROM transferencias_equipos WHERE id_transferencia = ?",[$request->id]);
      }else{
        $transferencias = DB::select("SELECT * FROM transferencias_consumibles WHERE id_transferencia = ?",[$request->id]);
      }

 
      foreach ($request->datos as $equipo) {
        if ($request->tipo == 1) {
          $t = DB::delete("DELETE FROM transferencias_equipos WHERE id_transferencia = ? AND id_equipo = ?",[$request->id,$equipo["id_articulo"]]); 

          $e = DB::update("UPDATE articulos SET estatus = 1 WHERE id_articulo = ?",[$equipo["id_articulo"]]);
        }else{
          $t = DB::delete("DELETE FROM transferencias_consumibles WHERE id_transferencia = ? AND id_consumible = ?",[$request->id,$equipo["id_consumible"]]); 
        }
      }

      if ($request->tipo == 1) {
      $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Equipos Eliminados en transferencia ".$request->id,$date,$date]);
      }else{
        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Consumibles Eliminados en transferencia ".$request->id,$date,$date]);
      }
      return response()->json($request);
    }


    public function modificarOrdenTranferencia(Request $request){
      $date = date("Y-m-d H:i:s"); 
      
    
        $conductor = DB::select("SELECT * FROM conductores WHERE id_conductor = ?",[$request->chofer])["0"];
      
        $vehiculo = DB::select("SELECT * FROM vehiculos WHERE id_vehiculo = ?",[$request->vehiculo])["0"];
     
        $actualizarOrden = DB::update("UPDATE orden_traslado SET chofer = ? ,cedula = ? ,vehiculo = ? ,color = ? ,placa = ? ,año_vehiculo = ? WHERE id_transferencia = ?",[$conductor->nombre_conductor." ".$conductor->apellido_conductor,$conductor->cedula,$vehiculo->marca." ".$vehiculo->modelo,$vehiculo->color,$vehiculo->placa,$vehiculo->anio,$request->id]);

        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->usuario,'Inventario',"Datos de orden de traslado modificados en transferencia ".$request->id,$date,$date]);

      return response()->json($vehiculo);
    }
  
}
