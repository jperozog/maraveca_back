<?php

namespace App\Http\Controllers;

use App\cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\historico;
use App\historico_cliente;

class ClienteController extends Controller
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

    public function traerCliente($id){
        
        $result = DB::select("SELECT * FROM clientes AS c
                                /*INNER JOIN estados AS e ON c.estado = e.id_estado*/
                                /*INNER JOIN municipios AS m ON c.municipio = m.id_municipio*/
                                  WHERE c.id = ?",[$id]);

        return response()->json($result);
    }


    public function traerEstados(){
        $result = DB::select("SELECT * FROM estados ");

        return response()->json($result);
    }

    public function traerMunicipios(Request $request){
        
        $result = DB::select("SELECT * FROM municipios WHERE id_estado = ? ",[$request->id]);

        foreach ($result as $muni) {
            $result2 = DB::select("SELECT * FROM clientes WHERE municipio = ?",[$muni->id_municipio]);

            $cantidad = count($result2);

            $muni->cantidad = $cantidad;
        }
        

        return response()->json($result);
    }

    public function traerCMunicipios(Request $request){
        
        $result = DB::select("SELECT * FROM clientes AS c INNER JOIN municipios AS m ON c.municipio = m.id_municipio WHERE c.estado = ? AND c.municipio = ?  ",[$request->estado, $request->muni]);


        return response()->json($result);
    }

    public function traerParroquias(Request $request){
        $result = DB::select("SELECT * FROM parroquias WHERE id_municipio = ? ",[$request->id]);

        return response()->json($result);
    }

    public function traerCiudades(Request $request){
        $result = DB::select("SELECT * FROM ciudades WHERE id_estado = ? ",[$request->id]);

        return response()->json($result);
    }

    public function store(Request $request)
    {   
        $responsable = $request->id_user;
        if(($request->kni=='G'||$request->kni=='G')&&($request->social != 'null' && $request->kind != null)){
            $cliente= ucwords(strtolower($request->social));
          }else {
            $cliente= ucfirst($request->nombres)." ".ucfirst($request->apellidos);
          }



        $fecha = Carbon::createFromTimestamp($request->fecha)->toDateTimeString();
        $fecha2 = date("Y-m-d H:i:s");  

    if($request->facturable == true){
        $result = DB::insert("INSERT INTO clientes(kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,serie,phone1,phone2,social,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
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
        $result = DB::insert("INSERT INTO clientes(kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,phone1,phone2,social,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
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

      

    $result2 = DB::select("SELECT * FROM clientes ORDER BY id DESC LIMIT 1")[0];

    historico_cliente::create(['history'=>'Nuevo Cliente: '.$cliente, 'modulo'=>'Clientes', 'cliente'=>$result2->id, 'responsable'=>$responsable]);
    historico::create(['responsable'=>$responsable, 'modulo'=>'Clientes', 'detalle'=>'Registro cliente '.$cliente]);



       return response()->json($result);
    }

    public function datosEstado($id){

        if($id == 10){
            $result = DB::select("SELECT * FROM clientes as c 
                                    INNER JOIN servicios as s ON c.id = s.cliente_srv
                                    INNER JOIN aps as a ON s.ap_srv = a.id
                                    INNER JOIN celdas as ce ON a.celda_ap =ce.id_celda
                                    INNER JOIN servidores as se ON ce.servidor_celda = se.id_srvidor
                                        WHERE (se.id_srvidor = 3 OR se.id_srvidor = 6 OR se.id_srvidor = 10 OR se.id_srvidor = 11 OR se.id_srvidor = 13 OR se.id_srvidor = 25) AND s.stat_srv != 4 ");
        }else{
            $result = DB::select("SELECT * FROM clientes as c 
                                    INNER JOIN servicios as s ON c.id = s.cliente_srv
                                    INNER JOIN aps as a ON s.ap_srv = a.id
                                    INNER JOIN celdas as ce ON a.celda_ap =ce.id_celda
                                    INNER JOIN servidores as se ON ce.servidor_celda = se.id_srvidor
                                        WHERE (se.id_srvidor = 15 OR se.id_srvidor = 14 OR se.id_srvidor = 21 OR se.id_srvidor = 23 OR se.id_srvidor = 24 OR se.id_srvidor = 26 OR se.id_srvidor = 27) AND s.stat_srv != 4");
        }

        return response()->json($result);
    }

    public function editarDatosClientes(Request $request){
        $datos = $request["datos"];
        $actualizarDatos = DB::update("UPDATE clientes SET kind = ?,dni = ?,email = ?, nombre = ?, apellido = ?, direccion = ?, social = ?, phone1 = ?, day_of_birth = ?, serie = ?
                                            WHERE id = ?",
                                            [$datos["kind"],$datos["dni"],$datos["email"],$datos["nombre"],$datos["apellido"],$datos["direccion"],$datos["social"],$datos["phone1"],$datos["day_of_birth"],$datos["serie"],$datos["id"]]);

        return response()->json($request);
    }
}
