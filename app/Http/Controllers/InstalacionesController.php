<?php

namespace App\Http\Controllers;

use App\Instalaciones;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\historico_cliente;
use App\historico;
use App\instinst;
use App\pendiente_servi;
use App\ticket_history;
use App\Mikrotik\RouterosAPI;
use App\lista_ip;
use \Carbon\Carbon;

class InstalacionesController extends Controller
{
    
    public function index(Request $request)
    {   
        if($request["nivel"] == 1){
            $usuario = DB::select("SELECT * FROM users WHERE id_user = ?",[$request["id"]])["0"];

            if($usuario->installer == 1){
                $instalaciones = [];
                $zonas = DB::select("SELECT * FROM user_zonas WHERE user = ?",[$usuario->id_user]);

                foreach ($zonas as $z) {
                    if( $request["tipo"] == 1){
                        $instalaciones2= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                        INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                                            WHERE ser.id_srvidor = ? AND s.status_insta = 1 ORDER BY s.status_insta ASC, s.id_insta  DESC',[$z->zona]);

                        foreach ($instalaciones2 as $i) {
                            array_push($instalaciones, $i);                          
                            } 
                    }else{
                        $instalaciones2= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                                        INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                                        INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                                        INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                                        INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                                                                 WHERE ser.id_srvidor = ? AND s.status_insta = 1 ORDER BY s.status_insta ASC, s.id_insta  DESC',[$z->zona]);

                        foreach ($instalaciones2 as $i) {
                            array_push($instalaciones, $i);                          
                            } 
                    }

                }  


            }else{
               
                if( $request["mk"] == 0){
                    if( $request["tipo"] == 1){
                        $instalaciones= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                        INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                                            WHERE s.tipo_insta = 1 ORDER BY s.status_insta ASC, s.id_insta  DESC');

                        foreach ($instalaciones as $i) {
                            $ventas = DB::select("SELECT * FROM ventas WHERE cliente_venta = ? AND status_venta = 2",[$i->cliente_insta]); 

                            if (count($ventas) > 0) {
                                $i->fechaVenta = $ventas[0]->created_at;
                            }
                        }
                                                          
                    }else{
                        if( $request["caja"] == 0){
                            $instalaciones= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                            INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                            INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                            INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                                                WHERE s.tipo_insta = 2  ORDER BY s.status_insta ASC, s.id_insta  DESC');
                        }else{
                            $instalaciones= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                            INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                            INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                            INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                                                WHERE s.tipo_insta = 2 AND caj.id_caja = ?  ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["caja"]]);
                        }
                    }
                }else{
                    if( $request["tipo"] == 1){
                        $instalaciones= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                        INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                                            WHERE s.tipo_insta = 1 AND ser.id_srvidor = ? ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["mk"]]);

                            foreach ($instalaciones as $i) {
                                $ventas = DB::select("SELECT * FROM ventas WHERE cliente_venta = ? AND status_venta = 1",[$i->cliente_insta]); 
                                    
                                if (count($ventas) > 0) {
                                    $i->fechaVenta = $ventas[0]->created_at;
                                }
                            }
                    }else{
                        if($request["caja"] == 0){
                            $instalaciones= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                            INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                            INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                            INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                                                WHERE s.tipo_insta = 2 AND ser.id_srvidor = ? ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["mk"]]);
                        }else{
                            $instalaciones= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                            INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                            INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                            INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                                                WHERE s.tipo_insta = 2 AND ser.id_srvidor = ? AND caj.id_caja = ? ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["mk"],$request["caja"]]);
                        }
                       
                    }

                }    
            }

        }else{
            if( $request["tipo"] == 1){
                $instalaciones= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                                INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                INNER JOIN users AS u ON s.user_insta = u.id_user
                                                    WHERE s.tipo_insta = 1 AND s.user_insta = ?  ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["id"]]);
            }else{
                $instalaciones= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,e.*,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                INNER JOIN users AS u ON s.user_insta = u.id_user
                                                    WHERE s.tipo_insta = 2 AND s.user_insta = ? ORDER BY s.status_insta ASC, s.id_insta  DESC',[$request["id"]]);
            }
           

        }

      
       return response()->json($instalaciones);
    }


    public function traerMigraciones(Request $request)
    {   
        if($request["nivel"] == 1){
            $usuario = DB::select("SELECT * FROM users WHERE id_user = ?",[$request["id"]])["0"];

            if($usuario->installer == 1){
                $instalaciones = [];
                $zonas = DB::select("SELECT * FROM user_zonas WHERE user = ?",[$usuario->id_user]);

                foreach ($zonas as $z) {
                        $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                                        INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                                        INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                                        INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                                        INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                                        INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                                        INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                                        INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                                        INNER JOIN users AS u ON m.user_migra = u.id_user
                                                                                 WHERE ser.id_srvidor = ? AND m.status_migra = 1 ORDER BY m.status_migra ASC, m.id_migracion  DESC',[$z->zona]);
                }  


            }else{
               
                if( $request["mk"] == 0){
                        if( $request["caja"] == 0){
                            $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                            INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                            INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                            INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                            INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                            INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                            INNER JOIN users AS u ON m.user_migra = u.id_user
                                                                ORDER BY m.status_migra ASC, m.id_migracion  DESC');
                        }else{
                            $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                          INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                          INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                            INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                            INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                            INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                            INNER JOIN users AS u ON m.user_migra = u.id_user
                                                                WHERE caj.id_caja = ?  ORDER BY m.status_migra ASC, m.id_migracion  DESC',[$request["caja"]]);
                        }
                }else{
                        if($request["caja"] == 0){
                            $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                           INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                           INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                            INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                            INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                            INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                            INNER JOIN users AS u ON m.user_migra = u.id_user
                                                                WHERE ser.id_srvidor = ? ORDER BY m.status_migra ASC, m.id_migracion  DESC',[$request["mk"]]);
                        }else{
                            $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                            INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                            INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                            INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                            INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                            INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                            INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                            INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                            INNER JOIN users AS u ON m.user_migra = u.id_user
                                                                WHERE ser.id_srvidor = ? AND caj.id_caja = ? ORDER BY m.status_migra ASC, m.id_migracion  DESC',[$request["mk"],$request["caja"]]);
                        }
                       
                    

                }    
            }

        }else{
            
                $migraciones= DB::select('SELECT m.*,a.*,ma.nombre_manga,caj.nombre_caja,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.id_plan,p.name_plan,p.tipo_plan,p.taza,p.dmb_plan,p.umb_plan,p.carac_plan,u.nombre_user,apellido_user  FROM migraciones AS m
                                                INNER JOIN planes as p ON m.plan_migra = p.id_plan
                                                INNER JOIN articulos as a ON m.equipo_migra = a.id_articulo
                                                INNER JOIN caja_distribucion as caj ON m.lugar_migra = caj.id_caja
                                                INNER JOIN manga_empalme as ma ON caj.manga_caja = ma.id_manga
                                                INNER JOIN olts as o ON ma.olt_manga = o.id_olt
                                                INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                                INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                INNER JOIN users AS u ON m.user_migra = u.id_user
                                                    WHERE s.user_insta = ? ORDER BY m.status_migra ASC, m.id_migracion  DESC',[$request["id"]]);
            
           

        }

      
       return response()->json($migraciones);
    }


    public function traerMudanzas(Request $request)
    {   
        if($request["nivel"] == 1){
            $usuario = DB::select("SELECT * FROM users WHERE id_user = ?",[$request["id"]])["0"];

            if($usuario->installer == 1){
                $mudanzas = [];
                $zonas = DB::select("SELECT * FROM user_zonas WHERE user = ?",[$usuario->id_user]);

                foreach ($zonas as $z) {
                   
                        $mudanzas2= DB::select('SELECT m.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,u.nombre_user,apellido_user  FROM mudanzas AS m
                                                        INNER JOIN celdas as cel ON m.lugar_muda = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN servicios as s ON m.servicio_muda = s.id_srv
                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                        INNER JOIN users AS u ON m.user_muda = u.id_user
                                                            WHERE ser.id_srvidor = ? AND m.status_muda = 1 ORDER BY m.status_muda ASC, m.id_mudanza  DESC',[$z->zona]);

                        foreach ($mudanzas2 as $i) {
                            array_push($mudanzas, $i);                          
                            } 

                }  

            }else{
               
                if( $request["mk"] == 0){
                   
                        $mudanzas= DB::select('SELECT m.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,u.nombre_user,apellido_user  FROM mudanzas AS m
                                                        INNER JOIN celdas as cel ON m.lugar_muda = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN servicios as s ON m.servicio_muda = s.id_srv
                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                        INNER JOIN users AS u ON m.user_muda = u.id_user
                                                            ORDER BY m.status_muda ASC, m.id_mudanza  DESC');
                   
                }else{
                    
                        $mudanzas= DB::select('SELECT m.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,u.nombre_user,apellido_user  FROM mudanzas AS m
                                                        INNER JOIN celdas as cel ON m.lugar_muda = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN servicios as s ON m.servicio_muda = s.id_srv
                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                        INNER JOIN users AS u ON m.user_muda = u.id_user
                                                            WHERE ser.id_srvidor = ? ORDER BY m.status_muda ASC, m.id_mudanza  DESC',[$request["mk"]]);
                    

                }    
            }

        }else{
                $mudanzas= DB::select('SELECT m.*,cel.nombre_celda,ser.nombre_srvidor,c.kind,c.dni,c.nombre,c.apellido,c.social,p.carac_plan,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                INNER JOIN celdas as cel ON m.lugar_muda = cel.id_celda
                                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN servicios as s ON m.servicio_muda = s.id_srv
                                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                        INNER JOIN users AS u ON m.user_muda = u.id_user
                                                    WHERE m.user_muda = ?  ORDER BY m.status_muda ASC, m.id_mudanza  DESC',[$request["id"]]);
            
        }

      
       return response()->json($mudanzas);
    }

    public function traerCuposActivos(Request $request)
    {   

        $date = date("Y-m-d"); 

        $dias = DB::select("SELECT * FROM instalaciones_cupos WHERE fecha_cupo >= ?  GROUP BY fecha_cupo ASC LIMIT 7",[$date]);


        foreach ($dias as $dia) {

            $diaDeLaSemana = Carbon::parse($dia->fecha_cupo)->format('l');

            if($diaDeLaSemana == "Monday"){
                $diaDeLaSemana = "Lunes";
            }
            if($diaDeLaSemana == "Tuesday"){
                $diaDeLaSemana = "Martes";
            }
            if($diaDeLaSemana == "Wednesday"){
                $diaDeLaSemana = "Miercoles";
            }
            if($diaDeLaSemana == "Thursday"){
                $diaDeLaSemana = "Jueves";
            }
            if($diaDeLaSemana == "Friday"){
                $diaDeLaSemana = "Viernes";
            }
            if($diaDeLaSemana == "Saturday"){
                $diaDeLaSemana = "Sabado";
            }
            if($diaDeLaSemana == "Sunday"){
                $diaDeLaSemana = "Domingo";
            }

            $cuposPF = DB::select("SELECT i.id_insta,i.tipo_insta,cl.nombre,cl.apellido,cl.social,cl.kind,cl.dni FROM instalaciones_cupos AS c 
                                    INNER JOIN instalaciones AS i ON c.id_insta = i.id_insta
                                    INNER JOIN clientes AS cl ON i.cliente_insta = cl.id 
                                        WHERE c.fecha_cupo = ? AND c.lugar_cupo = 4",[$dia->fecha_cupo]);

            $dia->cuposPF = $cuposPF; 
            
            $cuposC = DB::select("SELECT i.id_insta,i.tipo_insta,cl.nombre,cl.apellido,cl.social,cl.kind,cl.dni FROM instalaciones_cupos AS c 
                                    INNER JOIN instalaciones AS i ON c.id_insta = i.id_insta
                                    INNER JOIN clientes AS cl ON i.cliente_insta = cl.id 
                                        WHERE c.fecha_cupo = ? AND c.lugar_cupo = 2",[$dia->fecha_cupo]);

            $dia->cuposC = $cuposC; 

            $dia->diaSemana = $diaDeLaSemana;
            
        }
      
       return response()->json($dias);
    }

    public function traerTodosCuposActivos(Request $request)
    {   

        $cupos = DB::select("SELECT c.*,i.id_insta,i.tipo_insta,cl.nombre,cl.apellido,cl.social,cl.kind,cl.dni FROM instalaciones_cupos AS c 
                                INNER JOIN instalaciones AS i ON c.id_insta = i.id_insta
                                INNER JOIN clientes AS cl ON i.cliente_insta = cl.id WHERE c.id_insta != 0 AND estado_cupo = 1 ORDER BY fecha_cupo ASC");
      
       return response()->json($cupos);
    }



    public function ips(){
       
        $result = DB::select("SELECT ip_srv as ip FROM servicios WHERE ip_srv LIKE ?",["%".$id."%"]);

        $result2 = DB::select("SELECT value as ip FROM tipos_soportes WHERE nombre = 'ipP' AND value LIKE ?",["%".$id."%"]);

        $result3 = DB::select("SELECT ipP_det as ip FROM insta_detalles WHERE ipP_det LIKE ?",["%".$id."%"]);

         $ips2 = array_merge($result,$result2);

         $ips = array_merge($ips2,$result3);

        return response()->json($ips);
    }


    public function InstalacionesActivas(){

        $result= DB::select('SELECT *  FROM soportes WHERE status_soporte = 1 AND tipo_soporte = 1');

        
        return response()->json($result);

    }

    

    
    public function store(Request $request)
    {
        
        
        //variables para la creacion de una nueva instalacion//
        $id_usuario = $request ->input('id_user');
        $id_cliente = $request ->input('id_cliente');
        $modelo = $request ->input("modeloEquipo"); 
        $modelo2 = $request ->input("modeloEquipo2"); 
        $celda = $request ->input("celda");  
        $ip = $request ->input("ip");
        $plan = $request ->input("plan");  
        $tipoPlan = $request ->input("tipoPlan");  
        $serial = $request ->input("serial"); 
        $serial2 = $request ->input("serial2"); 
        $instalacion = $request->instalacion;
        $promocion = $request->promocion;
        $desde = $request->desde;
        $tasa_insta = $request->tasa_insta;
        $check = $request ->input("check");
        $date = date("Y-m-d H:i:s"); 

        
        //SQLs para la crear una nueva instalacion tanto en la tabla soporte como en la table tipos_soportes//
        if ($check == 0) {
        $result = DB::select("SELECT id_equipo FROM equipos2 WHERE nombre_equipo = ? ",[$modelo]);
        $modeloEquipo = $result[0]->id_equipo;
        }
        $result2 = DB::insert("INSERT INTO instalaciones(cliente_insta,status_insta,tipo_insta,tasa_insta,user_insta,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$id_cliente,1,$instalacion,$tasa_insta,$id_usuario,$date,$date]);
        
        $result3 = DB::select('SELECT * FROM instalaciones ORDER BY id_insta DESC LIMIT 1');

        $idsoporte = $result3[0]->id_insta;

        if($instalacion == 1){
            if ($check == 0) {
                $result4 = DB::insert("INSERT INTO insta_detalles(id_insta,celda_det,ap_det,ipP_det,plan_det,tipo_det,serial_det,modelo_det) VALUES (?,?,?,?,?,?,?,?)",[$idsoporte,$celda,0,$ip,$plan,$tipoPlan,$serial,$modeloEquipo]);
            } else {
                $result4 = DB::insert("INSERT INTO insta_detalles(id_insta,celda_det,ap_det,ipP_det,plan_det,tipo_det,serial_det,modelo_det) VALUES (?,?,?,?,?,?,?,?)",[$idsoporte,$celda,0,$ip,$plan,$tipoPlan,0,1]);
            }
        }else{
            if ($check == 0) {
                $result4 = DB::insert("INSERT INTO insta_detalles(id_insta,celda_det,ap_det,ipP_det,plan_det,tipo_det,serial_det,modelo_det) VALUES (?,?,?,?,?,?,?,?)",[$idsoporte,$celda,0,0,$plan,$tipoPlan,$serial,$modeloEquipo]);
            }else{
                $result4 = DB::insert("INSERT INTO insta_detalles(id_insta,celda_det,ap_det,ipP_det,plan_det,tipo_det,serial_det,modelo_det) VALUES (?,?,?,?,?,?,?,?)",[$idsoporte,$celda,0,0,$plan,$tipoPlan,0,1]);
            }
        }    

        //proceso para obtener los datos y el ingreso de estos en el MikroTic//
        $res = DB::select("SELECT * FROM planes WHERE id_plan =:plan",["plan"=>$plan]);

        $dmb_plan = $res[0]->dmb_plan;
        $umb_plan = $res[0]->umb_plan;
        $perfil_plan = $res[0]->name_plan;

        if ($res[0]->carac_plan == 1 ) {
            $parent = "Asimetricos";
        } else if ($res[0]->carac_plan == 2 )  {

            $parent = "none";
        }

        $res2 = DB::select("SELECT * FROM clientes WHERE id = ? ",[$id_cliente])["0"];

        if( $res2->kind == 'V'|| $res2->kind =='E'){
            $nombre4 = explode(" ",$res2->nombre);
            $apellido4 = explode(" ",$res2->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($res2->nombre)." ".ucfirst($res2->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
        }else {
            $cliente1= ucwords(strtolower($res2->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
        }

        if($instalacion == 1){

        $res3 = DB::select("SELECT * FROM celdas INNER JOIN servidores ON celdas.servidor_celda = servidores.id_srvidor WHERE celdas.id_celda = ?",[$celda])["0"];

        $ip_mikrotic = $res3->ip_srvidor;
        $user_mikrotic = $res3->user_srvidor;
        $password_mikrotic = $res3->password_srvidor;
        }else{
        $res3 = DB::select("SELECT * FROM caja_distribucion AS c
                                 INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                 INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                 INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                     WHERE c.id_caja = ?",[$celda])["0"];

            $ip_mikrotic = $res3->ip_srvidor;
            $user_mikrotic = $res3->user_srvidor;
            $password_mikrotic = $res3->password_srvidor;
        }

        $res4 = DB::select("SELECT * FROM instalaciones ORDER BY id_insta DESC LIMIT 1")["0"];

        $id_soporte = $res4->id_insta;

        $estatus = $res4->id_insta;

          //SQL que cambia de estatus el equipo seleccionado para la instalacion//

          $chequeoIntalacionGrupal = DB::select("SELECT * FROM articulos WHERE serial_articulo = ? AND estatus = 7",[$serial]);

          if(count($chequeoIntalacionGrupal) > 0){

          }else{
            $result9 = DB::update('UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?',[$serial]);
          }


          if($serial2 == null || $serial2 == '0'){
              
          }else{
            $encontrarEquipo = DB::select("SELECT * FROM articulos WHERE serial_articulo = ?",[$serial2])["0"];
            $agregarVentaEuipo = DB::insert("INSERT INTO venta_equipo(cliente,monto,responsable,tipo,id_venta_articulo,fecha_venta) VALUES (?,?,?,?,?,?)",[$cliente,0,$id_usuario,"Promocion",$encontrarEquipo->id_articulo,$date]);
            $result9 = DB::update('UPDATE articulos SET estatus = 5 WHERE serial_articulo = ?',[$serial2]);
          }
  
          if($promocion != 0){
              $agregarPromocionEnEspera = DB::insert("INSERT INTO promociones_en_espera(promocion_espera,cliente_promocion,status_espera,created_at,updated_at) VALUES (?,?,?,?,?)",[$promocion,$id_cliente,1,$date,$date]);
          }

        //proceso para el ingreso de datos en las tablas historico_clientes, ticket_histories y historicos//
        $history = "Instalacion Nueva: ". $cliente; 

        $detalle = "Se creo el Ticket de Instalacion: ".$id_soporte." para el cliente: ". $cliente; 

        $res0 = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$history,"Instalaciones",$id_cliente,$id_usuario,null,$date,$date]);

        if($instalacion == 1){
            $res01 = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Ip activada para su instalacion","Soporte",$id_cliente,$id_usuario,null,$date,$date]);
       }

        $res02 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_soporte,$id_usuario,"Se Agenda Instalacion",$date,$date]);

        $res03 = DB::update("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_usuario,"Instalaciones",$detalle,$date,$date]);
        
        if($check == 0){
        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_usuario,'Inventario',"Se Agendo Instalacion para el Cliente " .$cliente." Equipo asignado: ".$modelo.", serial: ".$serial,$date,$date]);
        }

        //comando que ingresa los datos al MikroTic, este se encuentra en el Helper.php//
        if($instalacion == 1){
            activar_ip_pd($ip,$cliente,$ip_mikrotic,$user_mikrotic,$password_mikrotic,$dmb_plan,$umb_plan,$parent,$estatus,$id_soporte,$cliente2,$res2->dni,$perfil_plan);
        }else{
            
            activar_pppoe_pendiente($cliente2,$res2->dni,$ip_mikrotic,$user_mikrotic,$password_mikrotic,$perfil_plan,$estatus,$id_soporte);
            
        }

         //agregar a cupos de instalacion
         $fechaDisponible = DB::select("SELECT *,COUNT(*) as cantidad FROM instalaciones_cupos GROUP BY fecha_cupo HAVING cantidad < 7 ORDER BY fecha_cupo ASC LIMIT 1");

         $lugar_cupo = 0;
 
         if($res3->id_srvidor == 11 || $res3->id_srvidor == 29 || $res3->id_srvidor == 31){
             $lugar_cupo = 4;
         }
 
         if($res3->id_srvidor == 28){
             $lugar_cupo = 2;
         }
 
         if( $lugar_cupo == 2 || $lugar_cupo == 4){
             if ($fechaDisponible != [] ) {
                 $aggCupo = DB::insert("INSERT INTO instalaciones_cupos(id_insta,fecha_cupo,estado_cupo,lugar_cupo,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$idsoporte,$fechaDisponible[0]->fecha_cupo,1,$lugar_cupo,$date,$date]);
 
                 $diaDeLaSemana = Carbon::parse($fechaDisponible[0]->fecha_cupo) ->format('l');
 
             } else {
                 $fechaDisponible2 = DB::select("SELECT *,COUNT(*) as cantidad FROM instalaciones_cupos GROUP BY fecha_cupo HAVING cantidad = 7 ORDER BY fecha_cupo DESC LIMIT 1");
 
                 $fecha = Carbon::parse($fechaDisponible2[0]->fecha_cupo)->addDays(1)->format('Y-m-d');
 
                 $diaDeLaSemana = Carbon::parse($fechaDisponible2[0]->fecha_cupo)->addDays(1)->format('l');
 
                 if($diaDeLaSemana == "Saturday"){
 
                     $fecha2 = Carbon::parse($fechaDisponible2[0]->fecha_cupo)->addDays(1)->format('Y-m-d');
                     $fecha3 = Carbon::parse($fechaDisponible2[0]->fecha_cupo)->addDays(2)->format('Y-m-d');
                     $fecha4 = Carbon::parse($fechaDisponible2[0]->fecha_cupo)->addDays(3)->format('Y-m-d');
 
                     for ($i=1; $i <= 7 ; $i++) { 
                         $aggCupo3 = DB::insert("INSERT INTO instalaciones_cupos(id_insta,fecha_cupo,estado_cupo,lugar_cupo,created_at,updated_at) VALUES (?,?,?,?,?,?)",[0,$fecha2,1,0,$date,$date]);
                     }
 
                     for ($i=1; $i <= 7 ; $i++) { 
                         $aggCupo4 = DB::insert("INSERT INTO instalaciones_cupos(id_insta,fecha_cupo,estado_cupo,lugar_cupo,created_at,updated_at) VALUES (?,?,?,?,?,?)",[0,$fecha3,1,0,$date,$date]);
                     }
 
                     $aggCupo5 = DB::insert("INSERT INTO instalaciones_cupos(id_insta,fecha_cupo,estado_cupo,lugar_cupo,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$idsoporte,$fecha4,1,$lugar_cupo,$date,$date]);
 
                 }else{
                     $aggCupo2 = DB::insert("INSERT INTO instalaciones_cupos(id_insta,fecha_cupo,estado_cupo,lugar_cupo,created_at,updated_at) VALUES (?,?,?,?,?,?)",[$idsoporte,$fecha,1,$lugar_cupo,$date,$date]);
                 }
 
             
             }
         }
         
       
        historico_cliente::create(['history'=>'ip activada para su instalacion', 'modulo'=>'Soporte', 'cliente'=>$id_cliente, 'responsable'=>$id_usuario]);
        historico::create(['responsable'=>$id_usuario, 'modulo'=>'Soporte', 'detalle'=>'ip activa asignada para el cliente: '.$id_cliente]);
        
        //comando que ingresa los datos al MikroTic, este se encuentra en el Helper.php//
        if($serial2 == "0" || $serial2 == null || $serial2 == 0 || $serial2 == "null"){
            $serial2 = "instalacion sin promocion";
        }else{
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_usuario,'Inventario',"Venta de Equipo: ".$modelo2." por medio de Promocion, serial: ".$serial2,$date,$date]);
        }
        
        return response()->json($request);
    }


    public function editarInstalacion(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $datos = $request->datos;

        $instalacion= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.*,e.*,p.*,u.nombre_user,apellido_user  FROM instalaciones AS s
                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                            INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                            INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                            WHERE s.id_insta = ?',[$datos["id_insta"]])["0"];

        //cambio de equipo y/o serial
           
        if($datos["serial_det"] != $instalacion->serial_det){
                if($instalacion->serial_det != "0"){
                    $mensaje = "equipos de inventario";
                    $equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["nombre_equipo"]])["0"];
                    $id_equipo = $equipo->id_equipo;
                    $nuevoSerial = DB::update("UPDATE insta_detalles SET modelo_det = ? WHERE id_insta = ?",[$id_equipo,$instalacion->id_insta]);
                    $volverInventario = DB::update("UPDATE articulos SET estatus = 1 WHERE serial_articulo = ?",[$instalacion->serial_det]);
                    $agregarInstalacion = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_det"]]);
                    
                    $nuevoSerial = DB::update("UPDATE insta_detalles SET serial_det = ? WHERE id_insta = ?",[$datos["serial_det"],$instalacion->id_insta]);
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["usuario"],'Inventario',"Cambio de equipo en Instalacion, de Equipo ".$instalacion->nombre_equipo." Serial: ".$instalacion->serial_det." a Equipo ".$datos["nombre_equipo"].", Serial: ".$datos["serial_det"]." codigo de instalacion: ".$datos["id_insta"],$date,$date]);
                }else{
                   $nuevoSerial = DB::update("UPDATE insta_detalles SET serial_det = ? WHERE id_insta = ?",[$datos["serial_det"],$instalacion->id_insta]);
                   $actEquipo = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_det"]]);
                   $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["usuario"],'Inventario',"Cambio de equipo en Instalacion, de Equipo Usado a Equipo".$datos["nombre_equipo"]." Serial: ".$datos["serial_det"]." codigo de instalacion: ".$datos["id_insta"],$date,$date]);
                }
        }else{
            $mensaje = "seriales iguales";
        }
        
       
        //cambio de celda
        if($datos["nombre_celda"] != $instalacion->nombre_celda){
            $celda = DB::select("SELECT * FROM celdas WHERE nombre_celda = ?",[$datos["nombre_celda"]])["0"];
            $id_celda = $celda->id_celda;
            $nuevaCelda = DB::update("UPDATE insta_detalles SET celda_det = ? WHERE id_insta = ?",[$id_celda,$instalacion->id_insta]);
        }else{
            $mensaje = "misma celda";
        }
        
        //cambio de plan
        if($datos["plan_det"] != $instalacion->plan_det){
            $celda = DB::select("SELECT * FROM celdas AS c INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor  WHERE nombre_celda = ?",[$datos["nombre_celda"]])["0"];
            $MK = $celda->ip_srvidor;
            $user = $celda->user_srvidor;
            $clave = $celda->password_srvidor;

            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];
                if ($plan->carac_plan == 1) {
                    $parent = "Asimetricos";
                } else if ($plan->carac_plan == 2) {
                    $parent = "none";
                }
                $cliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$datos["cliente_insta"]])["0"];
    
                if($cliente->kind=='G'|| $cliente->kind=='J'  || $cliente->kind=='V-'  &&  $cliente->social!= 'null' &&$cliente->kind != null){
                    $cliente1 = $cliente->social;
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }else{
                    $nombre4 = explode(" ",$cliente->nombre);
                    $apellido4 = explode(" ",$cliente->apellido);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                    $cliente1 = ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }


            if($datos["tipo_insta"] == 1){
                $nuevaCelda = DB::update("UPDATE insta_detalles SET plan_det = ?, tipo_det = ? WHERE id_insta = ?",[$datos["plan_det"],$datos["tipo_det"],$instalacion->id_insta]);
                Cambiar_plan($instalacion->ipP_det,$cliente,$MK,$user,$clave,$plan->dmb_plan, $plan->umb_plan,$parent,$instalacion->id_insta,$plan->name_plan);
            
            }else{
                
                $nuevaCelda = DB::update("UPDATE insta_detalles SET plan_det = ? WHERE id_insta = ?",[$datos["plan_det"],$instalacion->id_insta]);
                
                Cambiar_plan_pppoe($cliente2,$MK, $user, $clave,$plan->name_plan,$instalacion->id_insta);
            }
            
        }else{
            $mensaje ="plan igual";
        }   

        //cambio Ip
        if($datos["ipP_det"] != $instalacion->ipP_det){
                $celda = DB::select("SELECT * FROM celdas AS c INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor  WHERE nombre_celda = ?",[$datos["nombre_celda"]])["0"];
                $MK = $celda->ip_srvidor;
                $user = $celda->user_srvidor;
                $clave = $celda->password_srvidor;

                $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];
                if ($plan->carac_plan == 1) {
                    $parent = "Asimetricos";
                } else if ($plan->carac_plan == 2) {
                    $parent = "none";
                }
                $cliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$datos["cliente_insta"]])["0"];
    
                if($cliente->kind=='G'|| $cliente->kind=='J' || $cliente->kind == "V-"  &&  $cliente->social!= 'null' &&$cliente->kind != null){
                    $cliente2 = $cliente->social;
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
                    $cliente3 = $cliente;
                }else{
                    $nombre4 = explode(" ",$cliente->nombre);
                    $apellido4 = explode(" ",$cliente->apellido);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
        
                    $cliente1= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }


                if($instalacion->tipo_insta == 1){
                    
                    retirar_ip_pd($instalacion->ipP_det,$cliente,$MK,$user,$clave,$instalacion->id_insta,0);
                    activar_ip_pd($datos["ipP_det"],$cliente,$MK,$user,$clave,$plan->dmb_plan,$plan->umb_plan,$parent,$instalacion->id_insta,0,$cliente3,$instalacion->dni,$plan->name_plan);

                    $actualizarIp =  DB::select("UPDATE insta_detalles SET ipP_det = ? WHERE id_insta = ?",[$datos["ipP_det"],$datos["id_insta"]]);

                    historico_cliente::create(['history'=>'Cambio de ip: '.$instalacion->ipP_det.' ahora tiene '.$datos["ipP_det"], 'modulo'=>'Servicios', 'cliente'=>$instalacion->cliente_insta, 'responsable'=>$datos["usuario"]]);
                    historico::create(['responsable'=>$datos["usuario"], 'modulo'=>'Servicios', 'detalle'=>'Cambio de ip: '.$instalacion->ipP_det.' ahora tiene '.$datos["ipP_det"].'. Cliente '.$cliente]);

                    lista_ip::where('ip_servicio',$instalacion->ipP_det)->update(['ip' => $datos["ipP_det"]]);
                    
                }else{

                }
        }else{

        }

        
       return response()->json($datos); 
    }

    public function anularInstalacion(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $id = $request->id;
        $id_user = $request->id_user;
        
        $instalacion= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.*,e.*,p.*,u.nombre_user,apellido_user  FROM instalaciones AS s
                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                            INNER JOIN planes as p ON i.plan_det = p.id_plan
                                            INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                            INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                            INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                            INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                            INNER JOIN users AS u ON s.user_insta = u.id_user
                                            WHERE s.id_insta = ?',[$id])["0"];
        
        //devolucion de equipo a inventario
        if($instalacion->serial_det != "0"){
            $datosEquipo = DB::select("SELECT * FROM articulos WHERE serial_articulo = ?",[$instalacion->serial_det])["0"];
            $equipo = DB::update("UPDATE articulos SET estatus = 1 WHERE serial_articulo = ?",[$instalacion->serial_det]);
            $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_user,'Inventario',"Devolucion de Equipo al Inventario por Anulacion de Instalacion, Equipo ".$datosEquipo->modelo_articulo." Serial: ".$instalacion->serial_det.", codigo de instalacion: ".$instalacion->id_insta,$date,$date]);

        }else{
            $mensaje = "equipos usado";
        }


        //eliminar de lista activo y queue en MK
        $celda = DB::select("SELECT * FROM celdas AS c INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor  WHERE nombre_celda = ?",[$instalacion->nombre_celda])["0"];
        $MK = $celda->ip_srvidor;
        $user = $celda->user_srvidor;
        $clave = $celda->password_srvidor;

        $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$instalacion->plan_det])["0"];
            if ($plan->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($plan->carac_plan == 2) {
                $parent = "none";
            }
            $cliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$instalacion->cliente_insta])["0"];

            if($cliente->kind=='G'|| $cliente->kind=='J' || $cliente->kind=='V-' &&  $cliente->social!= 'null' &&$cliente->kind != null){
                $cliente1 = $cliente->social;
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else{
                $nombre4 = explode(" ",$cliente->nombre);
                $apellido4 = explode(" ",$cliente->apellido);
                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                $cliente1 = ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            }
          
        if($instalacion->tipo_insta == 1){
            retirar_ip_pd($instalacion->ipP_det,$cliente,$MK,$user,$clave,"P_I",0);
        }else{
            retirar_pppoe_pendiente($cliente2,$MK, $user, $clave,"P_I");
        }

        
        $anulacion = DB::update("UPDATE instalaciones SET status_insta =  3 WHERE id_insta = ?",[$instalacion->id_insta]); 
            
        historico::create(['responsable'=>$id_user, 'modulo'=>'Soporte', 'detalle'=>'Anulacion de insalacion del cliente: '.$cliente]);
       
        $res02 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$instalacion->id_insta,$id_user,"Instalacion anulada",$date,$date]);
        
         //posible devolucion de equipo por promocion
         $equipoPromocion = DB::select("SELECT * FROM venta_equipo as v
                                        INNER JOIN articulos as a on v.id_venta_articulo = a.id_articulo
                                            WHERE v.cliente = ? ORDER BY id_venta DESC",[$cliente]);
        
        if(count($equipoPromocion) > 0){
            $devolucionEquipoPromocion = DB::update("UPDATE articulos SET estatus = 1 WHERE id_articulo = ?",[$equipoPromocion["0"]->id_articulo]);
            $historialInventario2 = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_user,'Inventario',"Devolucion de Equipo de Promocion al Inventario por Anulacion de Instalacion, Equipo ".$equipoPromocion["0"]->modelo_articulo." Serial: ".$equipoPromocion["0"]->serial_articulo.", codigo de instalacion: ".$instalacion->id_insta,$date,$date]);

        }
       

        return response()->json($anulacion);
    }

    public function datosInstalacion($id){

        $instalacion= DB::select('SELECT s.*,i.*  FROM instalaciones AS s
                                            INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                            WHERE s.id_insta = ?',[$id])["0"];

        if ($instalacion->tipo_insta == 2) {
            $instalacion= DB::select('SELECT s.*,i.*,m.nombre_manga,caj.nombre_caja,caj.id_caja,caj.zona_caja,ser.nombre_srvidor,c.*,e.*,p.*,u.nombre_user,apellido_user  FROM instalaciones AS s
                                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                                        INNER JOIN caja_distribucion as caj ON i.celda_det = caj.id_caja
                                                        INNER JOIN manga_empalme as m ON caj.manga_caja = m.id_manga
                                                        INNER JOIN olts as o ON m.olt_manga = o.id_olt
                                                        INNER JOIN servidores as ser ON o.servidor_olt = ser.id_srvidor
                                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                                            WHERE s.id_insta = ?',[$id])["0"];
        }else{
            $instalacion= DB::select('SELECT s.*,i.*,cel.nombre_celda,ser.nombre_srvidor,c.*,e.*,p.*,u.nombre_user,apellido_user  FROM instalaciones AS s
                                        INNER JOIN insta_detalles as i ON s.id_insta = i.id_insta
                                        INNER JOIN planes as p ON i.plan_det = p.id_plan
                                        INNER JOIN equipos2 as e ON i.modelo_det = e.id_equipo
                                        INNER JOIN celdas as cel ON i.celda_det = cel.id_celda
                                        INNER JOIN servidores as ser ON cel.servidor_celda = ser.id_srvidor
                                        INNER JOIN clientes AS c ON s.cliente_insta = c.id
                                        INNER JOIN users AS u ON s.user_insta = u.id_user
                                            WHERE s.id_insta = ?',[$id])["0"];
        }       

        if($instalacion->kind=='G'|| $instalacion->kind=='J' || $instalacion->kind == "V-"  &&  $instalacion->social!= 'null' &&$instalacion->kind != null){
            $cliente2 = $instalacion->social;
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
            $instalacion->clientePPPOE = $cliente; 
        }else{
            $nombre4 = explode(" ",$instalacion->nombre);
            $apellido4 = explode(" ",$instalacion->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($instalacion->nombre)." ".ucfirst($instalacion->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','����','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            $instalacion->clientePPPOE = $cliente2;
        }

        
        
        $historial = DB::select("SELECT t.*,u.nombre_user,u.apellido_user FROM  instalaciones_histories AS t
                                    INNER JOIN users AS u ON t.user_ih = u.id_user WHERE instalacion_ih = ?",[$id]);

        $instalacion->historial = $historial;


        return response()->json($instalacion);
    }

    public function traerHistories($id){
        $historial = DB::select("SELECT t.*,u.nombre_user,u.apellido_user FROM  instalaciones_histories AS t
                                    INNER JOIN users AS u ON t.user_ih = u.id_user WHERE instalacion_ih = ?",[$id]);

        return response()->json($historial);
    }

    public function traerClientes($id)
    {   
        //$nombre = $request ->input('data');
      
        $result = DB::select("SELECT clientes.*, concat_ws(' ', nombre, apellido) as persona FROM clientes WHERE concat_ws(' ', nombre, apellido) LIKE ? OR dni LIKE ?  OR social LIKE ? ", ["%".$id."%","%".$id."%","%".$id."%"]);





        return response()->json($result); 
    }

    public function listaIp($id)
    {   
        $result = DB::select("SELECT ip_srv as ip FROM servicios WHERE ip_srv LIKE ?",["%".$id."%"]);

        $result2 = DB::select("SELECT value as ip FROM tipos_soportes WHERE nombre = 'ipP' AND value LIKE ?",["%".$id."%"]);

        $result3 = DB::select("SELECT ipP_det as ip FROM insta_detalles AS i
                                                        INNER JOIN instalaciones AS s ON s.id_insta = i.id_insta
                                                             WHERE ipP_det LIKE ? AND s.status_insta != 3 ",["%".$id."%"]);

         $ips2 = array_merge($result,$result2);

         $ips = array_merge($ips2,$result3);

        return response()->json($ips);
    }

    public function listaIp2(Request $request)
    {   
        $result = DB::select("SELECT c.id,c.kind,c.nombre,c.apellido,c.social,s.ip_srv as ip FROM servicios as s
                                inner join clientes as c on s.cliente_srv = c.id
                                inner join aps as a on s.ap_srv = a.id
                                inner join celdas as ce on a.celda_ap = ce.id_celda
                                inner join servidores as se on ce.servidor_celda = se.id_srvidor 
                                    WHERE se.id_srvidor = ?",[$request->mk]);
                                    

        //$result2 = DB::select("SELECT value as ip FROM tipos_soportes WHERE nombre = 'ipP' AND value LIKE ?",["%".$id."%"]);

        $result3 = DB::select("SELECT c.id,c.kind,c.nombre,c.apellido,c.social,d.ipP_det as ip FROM insta_detalles as d
                                    inner join instalaciones as i on d.id_insta = i.id_insta
                                    inner join clientes as c on i.cliente_insta = c.id
                                    inner join celdas as ce on d.celda_det = ce.id_celda
                                    inner join servidores as se on ce.servidor_celda = se.id_srvidor 
                                            WHERE se.id_srvidor = ?",[$request->mk]);

         //$ips2 = array_merge($result,$result2);

         //$ips = array_merge($ips2,$result3);

         $ips = collect(["servicios" => $result, "instalaciones" => $result3]);

        return response()->json($ips);
    }

    public function cerrarInstalacion(Request $request, $id){
        
        $date = date("Y-m-d H:i:s");
        
        $ip = DB::select("SELECT * FROM insta_detalles WHERE id_insta = ?",[$id])[0]->ipP_det;
        $instalacion = DB::select("SELECT * FROM instalaciones WHERE id_insta = ?",[$id])[0];
        $cliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$instalacion->cliente_insta])[0];
        $date = date("Y-m-d H:i:s");
        $insta = new instinst;
        if ($cliente->kind == 'V' || $cliente->kind == 'E') {
            $insta->ncliente = $cliente->nombre . " " . $cliente->apellido;
        } else {
            $insta->ncliente = ucwords($cliente->social);
        }

        foreach ($request->instaladores as $i) {

            $insta = new instinst;
            if ($cliente->kind == 'V' || $cliente->kind == 'E') {
                $insta->ncliente = $cliente->nombre . " " . $cliente->apellido;
            } else {
                $insta->ncliente = ucwords($cliente->social);
            }
            $insta->tipo = 1;
            $insta->ticket = $instalacion->id_insta;
            $insta->installer = $i["id_user"];
            $insta->stat = '1';
            $insta->save();
        }

       
        
        $actInstalacion = DB::update("UPDATE instalaciones SET status_insta = 2 WHERE id_insta = ?",[$id]);

        $venta = DB::select("SELECT * FROM ventas WHERE cliente_venta = ? AND status_venta = 2",[$instalacion->cliente_insta]);

        if (count($venta) > 0) {
            $actVenta = DB::update("UPDATE ventas SET status_venta = 3 WHERE id_venta = ?",[$venta[0]->id_venta]);
        }

        if($request->tipo_insta == 1){

            $ap = DB::update("UPDATE insta_detalles SET ap_det = ? WHERE id_insta = ?",[$request->ap,$id]);

                $instal_pend = new pendiente_servi;
                $instal_pend->soporte_pd = $id;
                $instal_pend->cliente_pd = $cliente->id;
                $instal_pend->celda_pd = $request->celda;
                $instal_pend->plan_pd = $request->plan;
                $instal_pend->ip_pd = $ip;
                $instal_pend->status_pd = 2;
                $instal_pend->save();

                $exterior = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo  WHERE consumible = ? AND id_zona = ?",[$request->id_exterior,$request->id_zona])[0];
                $cantidadExterior = $exterior->cantidad;
                $cantidadEFinal = $cantidadExterior - $request->cexterior;
                $exteriorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadEFinal,$exterior->id_consumible]);
        
                $interior = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo WHERE consumible = ? AND id_zona = ?",[$request->id_interior,$request->id_zona])[0];
                $cantidadInterior = $interior->cantidad;
                $cantidadIFinal = $cantidadInterior - $request->cinterior;
                $interiorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadIFinal,$interior->id_consumible]);
        
                $conectores = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo WHERE consumible = ? AND id_zona = ?",[$request->id_conector,$request->id_zona])[0];
                $cantidadConector = $conectores->cantidad;
                $cantidadCFinal = $cantidadConector - $request->cconector;
                $conectorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadCFinal,$conectores->id_consumible]);
        
                if($request->base > 0){
                    $base = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%base%' AND id_zona = ?",[$request->id_zona])[0];
                    $baseFinal = $base->cantidad - $request->base;
                    $baseF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$baseFinal,$base->id_consumible]);
                }
        
                if($request->grapa > 0){
                    $grapa = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%grapa%' AND id_zona = ?",[$request->id_zona])[0];
                    $grapaFinal = $grapa->cantidad - $request->grapa;
                    $grapaF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$grapaFinal,$grapa->id_consumible]);
                }
        
                if($request->alambre > 0){
                    $alambre = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%alambre%' AND id_zona = ?",[$request->id_zona])[0];
                    $alambreFinal = $alambre->cantidad - $request->alambre;
                    $alambreF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$alambreFinal,$alambre->id_consumible]);
                }
        
                
                if($request->base == 0 && $request->grapa == 0){
                    

                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Instalacion",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->alambre." Kg de alambre y".$request->cinterior." metros de cable ".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Instalacion: Se usaron '.$request->cconector.' '.$conectores->nombre_equipo.', '.$request->cexterior.' metros de cable '.$exterior->nombre_equipo.", ".$request->alambre." Kg de alambre y".$request->cinterior.' metros de cable '.$interior->nombre_equipo,$date,$date]);
        
                }
        
                if($request->base > 0 && $request->grapa > 0){
                   
                   
                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Instalacion",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, ".$request->base." Base de Antena y ".$request->grapa." Grapas Plasticas".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Instalacion: Se usaron '. $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, ".$request->base." Base de Antena y ".$request->grapa." Grapas Plasticas",$date,$date]);
        
                }
        
        
                if($request->base > 0 && $request->grapa == 0){
                    

                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Instalacion",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre y ".$request->base." Base de Antena".$interior->nombre_equipo,$date,$date]);


        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Instalacion: Se usaron ' . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, y ".$request->base." Base de Antena".$interior->nombre_equipo,$date,$date]);
                }
        
                if($request->base == 0 && $request->grapa > 0){
                  
                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Instalacion",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre y ".$request->grapa." Grapas Plasticas".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Instalacion: Se usaron '. $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, y ".$request->grapa." Grapas Plasticas",$date,$date]);
                }

        }else{
                
                $instal_pend = new pendiente_servi;
                $instal_pend->soporte_pd = $id;
                $instal_pend->cliente_pd = $cliente->id;
                $instal_pend->celda_pd = $request->celda;
                $instal_pend->plan_pd = $request->plan;
                $instal_pend->ip_pd = 0;
                $instal_pend->status_pd = 2;
                $instal_pend->save();
                

                $fibraoptica = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 as e ON c.consumible = e.id_equipo WHERE e.nombre_equipo = 'CABLE FIBRA' AND c.id_zona = ? ",[$request->id_zona])[0];
                $cantidadF = $fibraoptica->cantidad - $request->cableFibra;
                $updateFibra = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible =?",[$cantidadF,$fibraoptica->id_consumible]);

                $connector = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 as e ON c.consumible = e.id_equipo WHERE e.nombre_equipo = 'FAST CONNECTOR' AND c.id_zona = ? ",[$request->id_zona])[0];
                $cantidadC = $connector->cantidad - $request->fconnector;
                $updateConnector = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible =?",[$cantidadC,$connector->id_consumible]);
               
               
               $agregarHistorialInstalaciones = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Instalacion",$date,$date]);

               $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  instalaciones_histories(instalacion_ih,user_ih,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cableFibra ." Mts de Cable Fibra Optica,".$request->fconnector." Fast Connector",$date,$date]);
   
               $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Instalacion Fibra Optica: Se usaron '.$request->cableFibra ." Mts de Cable Fibra Optica y ".$request->fconnector." Fast Connector",$date,$date]);
   
        }

        $chequearCupo = DB::select("SELECT * FROM instalaciones_cupos WHERE id_insta = ?",[$id]);

        if(count($chequearCupo) > 0){
            $actCupo = DB::update("UPDATE instalaciones_cupos SET estado_cupo = 2 WHERE id_insta = ?",[$id]);
        }
        
        return response()->json($request);
    }

    public function guardarMigracion(Request $request){

        $date = date("Y-m-d H:i:s");

        $id_equipo = DB::select("SELECT * FROM articulos WHERE serial_articulo = ?",[$request->serial])["0"]->id_articulo;

        $actualizarStatusArticulo = DB::update('UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?',[$request->serial]);

        $guardarMigracion = DB::insert("INSERT INTO migraciones(servicio_migra,lugar_migra,equipo_migra,plan_migra,tasa_migra,status_migra,user_migra,created_at,updated_at)
                                            VALUES (?,?,?,?,?,?,?,?,?)",[$request->id,$request->lugar,$id_equipo,$request->plan,$request->tasa,1,$request->id_user,$date,$date]);

        $id_migracion = DB::select('SELECT * FROM migraciones ORDER BY id_migracion DESC LIMIT 1')["0"]->id_migracion;
        
        if($request->promo != 0){
            $agregarPromocionEnEspera = DB::insert("INSERT INTO promociones_en_espera(promocion_espera,cliente_promocion,status_espera,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->promo,$request->cliente,1,$date,$date]);
        }

        $BuscarCliente = DB::select("SELECT * FROM clientes WHERE id = ? ",[$request->cliente])["0"];

        if( $BuscarCliente->kind == 'V'|| $BuscarCliente->kind =='E'){
            $nombre4 = explode(" ",$BuscarCliente->nombre);
            $apellido4 = explode(" ",$BuscarCliente->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($BuscarCliente->nombre)." ".ucfirst($BuscarCliente->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
        }else {
            $cliente1= ucwords(strtolower($BuscarCliente->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
        }


        $history = "Migracion Nueva: ". $cliente; 

        $detalle = "Se creo el Ticket de Migracion: ".$id_migracion." para el cliente: ". $cliente; 

        $res0 = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$history,"Instalaciones",$request->cliente,$request->id_user,null,$date,$date]);

        $res02 = DB::update("INSERT INTO  migraciones_histories(migraciones_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_migracion,$request->id_user,"Se Agenda Migracion",$date,$date]);

        $res03 = DB::update("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,"Instalaciones",$detalle,$date,$date]);
          
        $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario',"Se Agendo migracion para el Cliente " .$cliente." Serial de equipo Asignado: ".$request->serial,$date,$date]);
    
        return response()->json($guardarMigracion);
    }

    public function datosMigracion($id){

        $migracion= DB::select('SELECT * FROM migraciones AS m
                                            INNER JOIN servicios AS s ON m.servicio_migra = s.id_srv
                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            INNER JOIN caja_distribucion AS caj ON m.lugar_migra = caj.id_caja
                                            INNER JOIN manga_empalme AS ma ON caj.manga_caja = ma.id_manga
                                            INNER JOIN articulos AS a ON m.equipo_migra = a.id_articulo
                                            INNER JOIN planes AS p ON m.plan_migra = p.id_plan
                                            INNER JOIN users AS u ON m.user_migra = u.id_user 
                                             WHERE m.id_migracion = ?',[$id])["0"];
     

        if($migracion->kind=='G'|| $migracion->kind=='J' || $migracion->kind == "V-"  &&  $migracion->social!= 'null' &&$migracion->kind != null){
            $cliente2 = $migracion->social;
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
            $migracion->clientePPPOE = $cliente; 
        }else{
            $nombre4 = explode(" ",$migracion->nombre);
            $apellido4 = explode(" ",$migracion->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($migracion->nombre)." ".ucfirst($migracion->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            $migracion->clientePPPOE = $cliente2;
        }

        
        
        $historial = DB::select("SELECT t.*,u.nombre_user,u.apellido_user FROM  migraciones_histories AS t
                                    INNER JOIN users AS u ON t.user_mh = u.id_user WHERE migraciones_mh = ?",[$id]);

        $migracion->historial = $historial;


        return response()->json($migracion);
    }

    public function cerrarMigracion(Request $request, $id){
        
        $date = date("Y-m-d H:i:s");
    
        $migracion = DB::select("SELECT * FROM migraciones WHERE id_migracion = ?",[$id])[0];
        $datosCliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$request->cliente])[0];
        $caja = DB::select("SELECT * FROM caja_distribucion AS c
                                    INNER JOIN manga_empalme AS m ON c.manga_caja = m.id_manga
                                    INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                    INNER JOIN servidores AS s ON o.servidor_olt = s.id_srvidor
                                        WHERE c.id_caja = ?",[$request->id_zona])[0];
        $servicio = DB::select("SELECT * FROM servicios WHERE id_srv = ?",[$migracion->servicio_migra])[0];
        $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$migracion->plan_migra])["0"];
        $equipo = DB::select("SELECT * FROM articulos AS a
                                INNER JOIN equipos2 AS e ON a.modelo_articulo = e.nombre_equipo
                                    WHERE a.id_articulo = ?",[$migracion->equipo_migra])[0];
       
        $migra = new instinst;
        if ($datosCliente->kind == 'V' || $datosCliente->kind == 'E') {
            $migra->ncliente = $datosCliente->nombre . " " . $datosCliente->apellido;
        } else {
            $migra->ncliente = ucwords($datosCliente->social);
        }

        foreach ($request->instaladores as $i) {

            $migra = new instinst;
            if ($datosCliente->kind == 'V' || $datosCliente->kind == 'E') {
                $migra->ncliente = $datosCliente->nombre . " " . $datosCliente->apellido;
            } else {
                $migra->ncliente = ucwords($datosCliente->social);
            }
            $migra->tipo = 2;
            $migra->ticket = $migracion->id_migracion;
            $migra->installer = $i["id_user"];
            $migra->stat = '1';
            $migra->save();
        }

       
        
        $actMigracion = DB::update("UPDATE migraciones SET status_migra = 2 WHERE id_migracion = ?",[$id]);

        
                
                $instal_pend = new pendiente_servi;
                $instal_pend->soporte_pd = $id;
                $instal_pend->cliente_pd = $datosCliente->id;
                $instal_pend->celda_pd = $request->id_zona;
                $instal_pend->plan_pd = $migracion->plan_migra;
                $instal_pend->ip_pd = 0;
                $instal_pend->status_pd = 2;
                $instal_pend->save();
                

                $fibraoptica = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 as e ON c.consumible = e.id_equipo WHERE e.nombre_equipo = 'CABLE FIBRA' AND c.id_zona = ? ",[$caja->zona_caja])[0];
                $cantidadF = $fibraoptica->cantidad - $request->cableFibra;
                $updateFibra = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible =?",[$cantidadF,$fibraoptica->id_consumible]);

                $connector = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 as e ON c.consumible = e.id_equipo WHERE e.nombre_equipo = 'FAST CONNECTOR' AND c.id_zona = ? ",[$caja->zona_caja])[0];
                $cantidadC = $connector->cantidad - $request->fconnector;
                $updateConnector = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible =?",[$cantidadC,$connector->id_consumible]);
               
               
               $agregarHistorialInstalaciones = DB::update("INSERT INTO  migraciones_histories(migraciones_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Migracion",$date,$date]);

               $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  migraciones_histories(migraciones_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cableFibra ." Mts de Cable Fibra Optica,".$request->fconnector." Fast Connector",$date,$date]);
   
               $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Migracion Fibra Optica: Se usaron '.$request->cableFibra ." Mts de Cable Fibra Optica y ".$request->fconnector." Fast Connector",$date,$date]);


            if($datosCliente->kind == 'V'|| $datosCliente->kind =='E'){
                $nombre4 = explode(" ",$datosCliente->nombre);
                $apellido4 = explode(" ",$datosCliente->apellido);
                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                $cliente1= ucfirst($datosCliente->nombre)." ".ucfirst($datosCliente->apellido);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            }else {
                $cliente1= ucwords(strtolower($datosCliente->social));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }


            suspender($servicio->ip_srv, $cliente,$caja->ip_srvidor, $caja->user_srvidor, $caja->password_srvidor,$servicio->id_srv);

            activar_pppoe($cliente2,$caja->ip_srvidor, $caja->user_srvidor, $caja->password_srvidor,$servicio->id_srv,$datosCliente->dni,$plan->name_plan);

            $actualizarServicio = DB::update("UPDATE servicios
                                                 SET tipo_srv = ?,
                                                     equipo_srv = ?,
                                                     ip_srv = ?,
                                                     serial_srv = ?,
                                                     ap_srv = ?,
                                                     plan_srv = ?,
                                                     tipo_plan_srv = ?
                                                        WHERE id_srv = ?",[2,$equipo->id_equipo,"0",$equipo->serial_articulo,$caja->id_caja,$plan->id_plan,$plan->tipo_plan,$servicio->id_srv]);


            $agregarHistorialCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Migracion Exitosa! ,cambios en los datos del servicio ". $servicio->id_srv,"Migraciones",$datosCliente->id,$request->id_user,null,$date,$date]);
            
        return response()->json($request);
    }



    public function guardarMudanza(Request $request){
        
        $date = date("Y-m-d H:i:s");

        $guardarMudanza = DB::insert("INSERT INTO mudanzas(servicio_muda,lugar_muda,ip_muda,tasa_muda,status_muda,user_muda,created_at,updated_at)
                                            VALUES (?,?,?,?,?,?,?,?)",[$request->id,$request->lugar,$request->ip,$request->tasa,1,$request->id_user,$date,$date]);

        $id_mudanza = DB::select('SELECT * FROM mudanzas ORDER BY id_mudanza DESC LIMIT 1')["0"]->id_mudanza;
        
        $BuscarCliente = DB::select("SELECT * FROM clientes WHERE id = ? ",[$request->cliente])["0"];

        if( $BuscarCliente->kind == 'V'|| $BuscarCliente->kind =='E'){
            $nombre4 = explode(" ",$BuscarCliente->nombre);
            $apellido4 = explode(" ",$BuscarCliente->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($BuscarCliente->nombre)." ".ucfirst($BuscarCliente->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
        }else {
            $cliente1= ucwords(strtolower($BuscarCliente->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
        }


        $history = "Mudanza Nueva: ". $cliente; 

        $detalle = "Se creo el Ticket de Migracion: ".$id_mudanza." para el cliente: ". $cliente; 

        $res0 = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$history,"Instalaciones",$request->cliente,$request->id_user,null,$date,$date]);

        $res02 = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id_mudanza,$request->id_user,"Se Agenda Mudanza",$date,$date]);

        $res03 = DB::update("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,"Instalaciones",$detalle,$date,$date]);
        
        
        return response()->json($request);
    }


    public function datosMudanza($id){

        $mudanza= DB::select('SELECT * FROM mudanzas AS m
                                            INNER JOIN servicios AS s ON m.servicio_muda = s.id_srv
                                            INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            INNER JOIN aps AS a ON m.lugar_muda = a.id
                                            INNER JOIN celdas AS ce ON a.celda_ap = ce.id_celda
                                            INNER JOIN servidores AS se ON ce.servidor_celda = se.id_srvidor
                                            INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo
                                            INNER JOIN users AS u ON m.user_muda = u.id_user 
                                            INNER JOIN planes AS p ON s.plan_srv = id_plan
                                                        WHERE m.id_mudanza = ?',[$id])["0"];
     

        if($mudanza->kind=='G'|| $mudanza->kind=='J' || $mudanza->kind == "V-"  &&  $mudanza->social!= 'null' &&$mudanza->kind != null){
            $cliente2 = $mudanza->social;
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente2);
            $mudanza->clientePPPOE = $cliente; 
        }else{
            $nombre4 = explode(" ",$mudanza->nombre);
            $apellido4 = explode(" ",$mudanza->apellido);
            $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

            $cliente1= ucfirst($mudanza->nombre)." ".ucfirst($mudanza->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            $mudanza->clientePPPOE = $cliente1;
        }

        
        
        $historial = DB::select("SELECT t.*,u.nombre_user,u.apellido_user FROM  mudanzas_histories AS t
                                    INNER JOIN users AS u ON t.user_mh = u.id_user WHERE mudanza_mh = ?",[$id]);

        $mudanza->historial = $historial;


        return response()->json($mudanza);
    }


    public function cerrarMudanza(Request $request, $id){
        
        $date = date("Y-m-d H:i:s");
        
        $mudanza = DB::select("SELECT * FROM mudanzas AS m
                                INNER JOIN servicios AS s ON m.servicio_muda = s.id_srv
                                INNER JOIN aps AS a ON s.ap_srv = a.id
                                INNER JOIN celdas AS ce ON a.celda_ap = ce.id_celda
                                INNER JOIN servidores AS se ON ce.servidor_celda = se.id_srvidor
                                INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                     WHERE id_mudanza = ?",[$id])[0];

        $nuevaMudanza  = DB::select("SELECT * FROM mudanzas AS m
                                        INNER JOIN servicios AS s ON m.servicio_muda = s.id_srv
                                        INNER JOIN aps AS a ON m.lugar_muda = a.id
                                        INNER JOIN celdas AS ce ON a.celda_ap = ce.id_celda
                                        INNER JOIN servidores AS se ON ce.servidor_celda = se.id_srvidor
                                        INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                            WHERE id_mudanza = ?",[$id])[0];                           
     
        $muda = new instinst;
        if ($mudanza->kind == 'V' || $mudanza->kind == 'E') {
            $muda->ncliente = $mudanza->nombre . " " . $mudanza->apellido;
        } else {
            $muda->ncliente = ucwords($mudanza->social);
        }
    
        foreach ($request->instaladores as $i) {

            $muda = new instinst;
            if ($mudanza->kind == 'V' || $mudanza->kind == 'E') {
                $muda->ncliente = $mudanza->nombre . " " . $mudanza->apellido;
            } else {
                $muda->ncliente = ucwords($mudanza->social);
            }
            $muda->tipo = 3;
            $muda->ticket = $mudanza->id_mudanza;
            $muda->installer = $i["id_user"];
            $muda->stat = '1';
            $muda->save();
        }

        
        $actMudanza = DB::update("UPDATE mudanzas SET status_muda = 2 WHERE id_mudanza = ?",[$id]);
        

                $instal_pend = new pendiente_servi;
                $instal_pend->soporte_pd = $id;
                $instal_pend->cliente_pd = $mudanza->cliente_srv;
                $instal_pend->celda_pd = $request->celda;
                $instal_pend->plan_pd = 1;
                $instal_pend->ip_pd = $mudanza->ip_muda;
                $instal_pend->status_pd = 2;
                $instal_pend->save();

                $exterior = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo  WHERE consumible = ? AND id_zona = ?",[$request->id_exterior,$request->id_zona])[0];
                $cantidadExterior = $exterior->cantidad;
                $cantidadEFinal = $cantidadExterior - $request->cexterior;
                $exteriorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadEFinal,$exterior->id_consumible]);
        
                $interior = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo WHERE consumible = ? AND id_zona = ?",[$request->id_interior,$request->id_zona])[0];
                $cantidadInterior = $interior->cantidad;
                $cantidadIFinal = $cantidadInterior - $request->cinterior;
                $interiorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadIFinal,$interior->id_consumible]);
        
                $conectores = DB::select("SELECT * FROM consumibles INNER JOIN equipos2 ON consumibles.consumible = equipos2.id_equipo WHERE consumible = ? AND id_zona = ?",[$request->id_conector,$request->id_zona])[0];
                $cantidadConector = $conectores->cantidad;
                $cantidadCFinal = $cantidadConector - $request->cconector;
                $conectorFinal = DB::update("UPDATE consumibles SET cantidad = ? WHERE id_consumible = ?",[$cantidadCFinal,$conectores->id_consumible]);
        
                if($request->base > 0){
                    $base = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%base%' AND id_zona = ?",[$request->id_zona])[0];
                    $baseFinal = $base->cantidad - $request->base;
                    $baseF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$baseFinal,$base->id_consumible]);
                }
        
                if($request->grapa > 0){
                    $grapa = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%grapa%' AND id_zona = ?",[$request->id_zona])[0];
                    $grapaFinal = $grapa->cantidad - $request->grapa;
                    $grapaF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$grapaFinal,$grapa->id_consumible]);
                }
        
                if($request->alambre > 0){
                    $alambre = DB::select("SELECT * FROM consumibles AS c INNER JOIN equipos2 AS e ON c.consumible = e.id_equipo WHERE e.nombre_equipo LIKE '%alambre%' AND id_zona = ?",[$request->id_zona])[0];
                    $alambreFinal = $alambre->cantidad - $request->alambre;
                    $alambreF = DB::update("UPDATE consumibles SET cantidad = ?  WHERE id_consumible = ?",[$alambreFinal,$alambre->id_consumible]);
                }
        
                
                if($request->base == 0 && $request->grapa == 0){
                    

                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Mudanza",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->alambre." Kg de alambre y".$request->cinterior." metros de cable ".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Mudanza: Se usaron '.$request->cconector.' '.$conectores->nombre_equipo.', '.$request->cexterior.' metros de cable '.$exterior->nombre_equipo.", ".$request->alambre." Kg de alambre y".$request->cinterior.' metros de cable '.$interior->nombre_equipo,$date,$date]);
        
                }
        
                if($request->base > 0 && $request->grapa > 0){
                   
                   
                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Mudanza",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, ".$request->base." Base de Antena y ".$request->grapa." Grapas Plasticas".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Mudanza: Se usaron '. $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, ".$request->base." Base de Antena y ".$request->grapa." Grapas Plasticas",$date,$date]);
        
                }
        
        
                if($request->base > 0 && $request->grapa == 0){
                    

                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Mudanza",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre y ".$request->base." Base de Antena".$interior->nombre_equipo,$date,$date]);


        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Mudanza: Se usaron ' . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, y ".$request->base." Base de Antena".$interior->nombre_equipo,$date,$date]);
                }
        
                if($request->base == 0 && $request->grapa > 0){
                  
                    $agregarHistorialInstalaciones = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se Cierra las Mudanza",$date,$date]);

                    $agregarHistorialInstalaciones2 = DB::update("INSERT INTO  mudanzas_histories(mudanza_mh,user_mh,comment,created_at,updated_at) VALUES (?,?,?,?,?)",[$id,$request->id_user,"Se usaron " . $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre y ".$request->grapa." Grapas Plasticas".$interior->nombre_equipo,$date,$date]);
        
                    $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$request->id_user,'Inventario','Cierre de Mudanza: Se usaron '. $request->cconector ." ".$conectores->nombre_equipo.", " .$request->cexterior." metros de cable ".$exterior->nombre_equipo.", ".$request->cinterior." metros de cable ".$interior->nombre_equipo.", ".$request->alambre." Kg de alambre, y ".$request->grapa." Grapas Plasticas",$date,$date]);
                }



                if($mudanza->kind == 'V'|| $mudanza->kind =='E'){
                    $nombre4 = explode(" ",$mudanza->nombre);
                    $apellido4 = explode(" ",$mudanza->apellido);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
    
                    $cliente1= ucfirst($mudanza->nombre)." ".ucfirst($mudanza->apellido);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }else {
                    $cliente1= ucwords(strtolower($mudanza->social));
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }
    
    
                suspender($mudanza->ip_srv, $cliente,$mudanza->ip_srvidor, $mudanza->user_srvidor, $mudanza->password_srvidor,$mudanza->id_srv);

                if ($mudanza->carac_plan == 1) {
                    $parent = "Asimetricos";
                }else{
                    $parent = "none";
                }

                activar($mudanza->ip_muda, $cliente, $nuevaMudanza->ip_srvidor, $nuevaMudanza->user_srvidor, $nuevaMudanza->password_srvidor, $nuevaMudanza->dmb_plan, $nuevaMudanza->umb_plan, $parent, $nuevaMudanza->id_srv,$cliente2,$nuevaMudanza->kind,$nuevaMudanza->name_plan);
                

                $actualizarServicio = DB::update("UPDATE servicios SET ip_srv = ?,ap_srv = ?,WHERE id_srv = ?",[$mudanza->ip_muda,$mudanza->lugar_muda,$mudanza->id_srv]);


            $agregarHistorialCliente = DB::update("INSERT INTO historico_clientes (history,modulo,cliente,responsable,mensaje,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",["Mudanza Exitosa! ,cambios en los datos del servicio ". $mudanza->id_srv,"Mudanzas",$mudanza->clinete_srv,$request->id_user,null,$date,$date]);
        
        return response()->json($request);
    }

   public function cambiarFechaCupo(Request $request){

    $actualizarFecha = DB::update("UPDATE instalaciones_cupos SET fecha_cupo = ? WHERE id_insta = ?",[$request->fecha, $request->id]);

    return response()->json($actualizarFecha);

   }


   
}
