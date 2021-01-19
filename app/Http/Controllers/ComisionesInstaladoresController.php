<?php

namespace App\Http\Controllers;

use App\comisionesInstaladores;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ComisionesInstaladoresController extends Controller
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
    public function traerInstaladores(){

        $vendedores = DB::select("SELECT * FROM instinsts AS i
                                    INNER JOIN users AS u ON i.installer = u.id_user		
                                        GROuP BY i.installer");

        return response()->json($vendedores);
    }

    public function traerComisionesInstalador(Request $request){
        $mes = $request["mes"];
        $anio=$request["anio"];
        $id_instalador = $request["id_user"];
        
        $comisiones = DB::select("SELECT * FROM instalaciones AS i 
                                        INNER JOIN  instinsts AS ins ON i.id_insta = ins.ticket
                                        INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                        INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                        INNER JOIN clientes AS c ON i.cliente_insta = c.id
                                        WHERE MONTH(i.created_at) = ? AND YEAR(i.created_at)= ? AND ins.installer = ? AND ins.tipo = 1
                                        ORDER BY i.id_insta DESC",[$mes,$anio,$id_instalador]);

        $comisionesMi = DB::select("SELECT * FROM migraciones AS m
                                        INNER JOIN instinsts AS i ON m.id_migracion = i.ticket
                                            WHERE MONTH(m.created_at) = ? AND YEAR(m.created_at)= ? AND i.installer = ? AND i.tipo = 2
                                                ORDER BY m.id_migracion DESC",[$mes,$anio,$id_instalador]); 
         
         $comisionesMu = DB::select("SELECT m.*,i.ncliente,i.tipo,i.ticket,a.nombre_ap,s.nombre_srvidor FROM mudanzas AS m
                                        INNER JOIN instinsts AS i ON m.id_mudanza = i.ticket
                                        INNER JOIN aps AS a ON m.lugar_muda = a.id
                                        INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                        INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                            WHERE MONTH(m.created_at) = ? AND YEAR(m.created_at)= ? AND i.installer = ? AND i.tipo = 3
                                                ORDER BY m.id_mudanza DESC",[$mes,$anio,$id_instalador]);  

        $cuota = DB::select("SELECT * FROM cuota_instaladores WHERE id_user = ?",[$id_instalador])["0"]->cuota;
        
        $tasa = DB::select("SELECT * FROM configuracions WHERE nombre = 'taza(BCV)'")["0"]->valor;

        foreach ($comisiones as $comision) {
            $instalacionActual = DB::select("SELECT * FROM instalaciones_histories WHERE comment = 'Se Cierra las Instalacion' AND instalacion_ih = ?",[$comision->id_insta]);
            if(count($instalacionActual) > 0){
                $comision->estadoComision = 1;

            }else{
                $comision->estadoComision = 0;
            }
            $montoCuota = $cuota * $tasa;

            $comision->montoCuota = $montoCuota;

        }

        foreach ($comisionesMi as $comision) {
            $migracionActual = DB::select("SELECT * FROM migraciones_histories WHERE comment = 'Se Cierra las Migracion' AND migraciones_mh = ?",[$comision->id_migracion]);
            if(count($migracionActual) > 0){
                $comision->estadoComision = 1;

            }else{
                $comision->estadoComision = 0;
            }
            $montoCuota = $cuota * $tasa;

            $comision->montoCuota = $montoCuota;

        }

        foreach ($comisionesMu as $comision) {
            $mudanzaActual = DB::select("SELECT * FROM mudanzas_histories WHERE comment = 'Se Cierra las Mudanza' AND mudanza_mh = ?",[$comision->id_mudanza]);
            if(count($mudanzaActual) > 0){
                $comision->estadoComision = 1;

            }else{
                $comision->estadoComision = 0;
            }
            $montoCuota = $cuota * $tasa;

            $comision->montoCuota = $montoCuota;

        }


        $index = collect(['comisiones'=>$comisiones,'comisionesMi'=>$comisionesMi,'comisionesMu'=>$comisionesMu]);

        return response()->json($index);
    }

    public function guardarCuotaInstalador(Request $request){
        $fecha = date("Y-m-d H:i:s");

        $verificarCuota = DB::select("SELECT * FROM cuota_instaladores WHERE id_user = ?",[$request->id_user]);

        if (count($verificarCuota) > 0) {
            $actualizarCuota = DB::update("UPDATE cuota_instaladores SET cuota = ? WHERE id_cuota = ?",[$request->cuota, $verificarCuota[0]->id_cuota]);
        } else {
            $añadirCuota = DB::insert("INSERT INTO cuota_instaladores(cuota,id_user,created_at,updated_at) VALUES (?,?,?,?)",[$request->cuota,$request->id_user,$fecha,$fecha]);
        }
        


        return response()->json($request);
    }


}
