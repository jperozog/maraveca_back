<?php

namespace App\Http\Controllers;

use App\factibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use \Carbon\Carbon;

class FactibilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $permisoSeniat = DB::select("SELECT * FROM permissions WHERE user = ? AND perm = 'seniat'",[$id]);

        if (count($permisoSeniat) > 0) {
            $result = DB::select("SELECT f.*,c.kind,c.dni,c.nombre,c.apellido,c.social FROM factibilidades AS f
                                     INNER JOIN pclientes AS c ON f.id_pot = c.id
                                     WHERE (serie = 1 OR serie = 0 ) AND (kind = 'J' OR kind = 'G') AND id_cli IS NULL
                                        ORDER BY f.status ASC, f.updated_at DESC");
        } else {
            $result = DB::select("SELECT f.*,c.kind,c.dni,c.nombre,c.apellido,c.social FROM factibilidades AS f INNER JOIN pclientes AS c ON f.id_pot = c.id ORDER BY f.status ASC, f.updated_at DESC");
        }
    
       return response()->json($result);
    }

    public function index2($id)
    {
       $result = DB::select("SELECT f.*,c.kind,c.dni,c.nombre,c.apellido,c.social FROM factibilidades AS f INNER JOIN pclientes AS c ON f.id_pot = c.id WHERE f.responsable = ? ORDER BY f.status ASC, f.updated_at DESC",[$id]);

       return response()->json($result);
    }

    public function traerFactibilidades(){
        $result = DB::select("SELECT f.*,c.kind,c.dni,c.nombre,c.apellido,c.social FROM factibilidades AS f INNER JOIN pclientes AS c ON f.id_pot = c.id  ORDER BY f.status ASC, f.updated_at DESC ");

        return response()->json($result);
      }


      public function verificarFac($id){
        $datos = new \stdClass();
        $result = DB::select("SELECT * FROM factibilidades_dets WHERE id_fac = ?",[$id]);
        
        if($result != []){
            foreach ($result as $r ) {
                if($r->nombre =="celda" ){
                    $celdaF = $r->valor;
                    if($celdaF == "Sin Importancia"){
                        $datos->celda = $celdaF;
                    }else{
                        $celda = DB::select("SELECT * FROM celdas WHERE id_celda = ?",[$celdaF])[0]->nombre_celda;
                        $datos->celda = $celda;
                    }
                }
                if($r->nombre =="equipo" ){
                    $equipoF = $r->valor;
                    if($equipoF == "Sin Importancia"){
                        $datos->equipo = $equipoF;
                    }else{
                    $equipo = DB::select("SELECT * FROM equipos2 WHERE id_equipo = ?",[$equipoF])[0]->nombre_equipo;
                    $datos->equipo = $equipo;
                    }
                }
                if($r->nombre =="usuario" ){
                    $usuarioF = $r->valor;
                    $usuarioQ = DB::select("SELECT * FROM users WHERE id_user = ?",[$usuarioF])[0]->nombre_user;
                    $usuarioQ2 = DB::select("SELECT * FROM users WHERE id_user = ?",[$usuarioF])[0]->apellido_user;
                    $usuario = $usuarioQ." ".$usuarioQ2;
                    $datos->usuario = $usuario;
                }
                if($r->nombre =="altura" ){
                    $altura = $r->valor;
                    $datos->altura=$altura;
                }
                if($r->nombre =="ptp" && $r->valor != null ){
                    $ptpF = $r->valor;
                    if($ptpF == "Sin Importancia"){
                        $datos->ptp = $ptpF;
                    }else{
                        $ptp = DB::select("SELECT * FROM equipos2 WHERE id_equipo = ?",[$equipoF])[0]->nombre_equipo;
                        $datos->ptp = $ptp;
                    }
                }        
            }   
                $datos->existe=1;
        }else{
            $datos->celda = 0;
            $datos->equipo = 0;
            $datos->usuario = 0;
            $datos->altura=0;
            $datos->existe=0;
            $datos->ptp=0;
        }

         

        return response()->json($datos);
    }  


    public function traerEquipos(){
        $result = DB::select("SELECT * FROM equipos2 WHERE tipo_equipo = 1");
        return response()->json($result);
    }

    public function traerCeldas(){
        $result = DB::select("SELECT * FROM celdas ");
        return response()->json($result);
    }

    public function guardarNuevaFac(Request $request){
        $fecha = date("Y-m-d H:i:s");
        $result = DB::insert("INSERT INTO factibilidades(id_pot,
                                                        coordenadaslat,
                                                        coordenadaslon,
                                                        status,
                                                        ptp,
                                                        factible,
                                                        comentario,
                                                        ciudad,
                                                        responsable,
                                                        created_at,
                                                        updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)"
                                                        ,[$request->id,
                                                          $request->latitud,
                                                          $request->longitud,
                                                          1,
                                                          null,
                                                          null,
                                                          $request->direccion,
                                                          $request->ciudad,
                                                          $request->id_user,
                                                          $fecha,
                                                          $fecha]);

        return response()->json($result);
    }

    public function guardarFac(Request $request){

        if($request->celda != 0){
            if($request->ptp == 0){
                $ptp = null;
            }else{
                $ptp = $request->ptp;
            }
            $fecha = date("Y-m-d H:i:s");
    
            $celda = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"celda",$request->celda,$fecha,$fecha]);
    
            $equipo = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"equipo",$request->equipo,$fecha,$fecha]);

            $usuario = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"usuario",$request->id_user,$fecha,$fecha]);
    
            $altura = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"altura",$request->altura,$fecha,$fecha]);

            $ptp = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"ptp",$ptp,$fecha,$fecha]);

            $result = DB::update("UPDATE factibilidades SET status = 2, factible = ?,updated_at = ? WHERE id = ?",[$request->estado,$fecha,$request->id]);
        }else{
            $fecha = date("Y-m-d H:i:s");
    
            $celda = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"celda","Sin Importancia",$fecha,$fecha]);
    
            $equipo = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"equipo","Sin Importancia",$fecha,$fecha]);

            $usuario = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"usuario",$request->id_user,$fecha,$fecha]);
    
            $altura = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"altura","Sin Importancia",$fecha,$fecha]);

            $ptp = DB::insert("INSERT INTO factibilidades_dets(id_fac,nombre,valor,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id,"ptp","Sin Importancia",$fecha,$fecha]);

            $result = DB::update("UPDATE factibilidades SET status = 2, factible = ?,updated_at = ? WHERE id = ?",[$request->estado,$fecha,$request->id]);
        }
        return response()->json($request);
    }

    public function editarFac(Request $request){

        if($request->estado == 1 || $request->estado == 4){
            if($request->ptp == 0){
                $ptp = null;
            }else{
                $ptp = $request->ptp;
            }
            $fecha = date("Y-m-d H:i:s");

            $result = DB::update("UPDATE factibilidades SET factible = ?,updated_at = ? WHERE id = ?",[$request->estado,$fecha,$request->id]);
    
            $celda = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'celda' AND id_fac = ?",[$request->celda,$request->id]);

            $equipo = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'equipo' AND id_fac = ?",[$request->equipo,$request->id]);
    
            $altura = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'altura' AND id_fac = ?",[$request->altura,$request->id]);
            
            $ptp = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'ptp' AND id_fac = ?",[$ptp,$request->id]);
        }else{
            $fecha = date("Y-m-d H:i:s");
            
            $result = DB::update("UPDATE factibilidades SET factible = ?,updated_at = ? WHERE id = ?",[$request->estado,$fecha,$request->id]);
    
            $celda = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'celda' AND id_fac = ?",["Sin Importancia",$request->id]);

            $equipo = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'equipo' AND id_fac = ?",["Sin Importancia",$request->id]);
    
            $altura = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'altura' AND id_fac = ?",["Sin Importancia",$request->id]);
            
            $ptp = DB::insert("UPDATE factibilidades_dets SET valor = ? WHERE nombre = 'ptp' AND id_fac = ?",["Sin Importancia",$request->id]);
        }
        
        return response()->json($request);
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\factibilidad  $factibilidad
     * @return \Illuminate\Http\Response
     */
    public function show(factibilidad $factibilidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\factibilidad  $factibilidad
     * @return \Illuminate\Http\Response
     */
    public function edit(factibilidad $factibilidad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\factibilidad  $factibilidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, factibilidad $factibilidad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\factibilidad  $factibilidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(factibilidad $factibilidad)
    {
        //
    }
}
