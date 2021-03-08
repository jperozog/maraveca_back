<?php

namespace App\Http\Controllers;

use App\servicio;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use App\historico_cliente;
use App\historico;
use App\instinst;
use App\lista_ip;
use App\servicios;
use App\cola_de_ejecucion;
use App\pendiente_servi;
use App\ticket_history;
use DateTime;

class ServicioController extends Controller
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
            $servicios =  DB::select("SELECT * FROM servicios AS s
                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                        INNER JOIN aps AS a ON s.ap_srv = a.id
                                        INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                        INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                        INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                        INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                            WHERE c.serie = 1 ORDER BY c.id DESC ");
             
             $instalacionesInlamabricas = DB::select("SELECT * FROM instalaciones AS i
                                                        INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                        INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                        INNER JOIN aps AS a ON d.ap_det = a.id
                                                        INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                                        INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                                        INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                        INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                            WHERE i.status_insta = 2 AND i.tipo_insta = 1 AND cl.serie = 1  ORDER BY i.id_insta DESC"); 
            
            $instalacionesFibra = DB::select("SELECT * FROM instalaciones AS i
                                                INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                INNER JOIN caja_distribucion AS caj ON d.celda_det = caj.id_caja
                                                INNER JOIN manga_empalme AS m ON caj.manga_caja = m.id_manga
                                                INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                INNER JOIN servidores AS ser ON o.servidor_olt = ser.id_srvidor
                                                INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                    WHERE i.status_insta = 2 AND i.tipo_insta = 2 and cl.serie = 1  ORDER BY i.id_insta DESC");  
               
               
        } else {
            $serviciosInalambricos =  DB::select("SELECT * FROM servicios AS s
                                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                    INNER JOIN aps AS a ON s.ap_srv = a.id
                                                    INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                                    INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                                    INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                                        WHERE s.tipo_srv = 1
                                                            ORDER BY c.id DESC ");

            $serviciosFibra =  DB::select("SELECT * FROM servicios AS s
                                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                                    INNER JOIN caja_distribucion AS ca ON s.ap_srv = ca.id_caja
                                                    INNER JOIN manga_empalme AS m ON ca.manga_caja = m.id_manga
                                                    INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                    INNER JOIN servidores AS ser ON o.servidor_olt = ser.id_srvidor
                                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                                    INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                                        WHERE s.tipo_srv = 2
                                                            ORDER BY c.id DESC ");                            

            $instalacionesInlamabricas = DB::select("SELECT * FROM instalaciones AS i
                                                     INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                     INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                     INNER JOIN aps AS a ON d.ap_det = a.id
                                                     INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                                     INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                                     INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                     INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                     WHERE i.status_insta = 2 AND i.tipo_insta = 1  ORDER BY i.id_insta DESC");

            $instalacionesFibra = DB::select("SELECT * FROM instalaciones AS i
                                                    INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                    INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                    INNER JOIN caja_distribucion AS caj ON d.celda_det = caj.id_caja
                                                    INNER JOIN manga_empalme AS m ON caj.manga_caja = m.id_manga
                                                    INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                                    INNER JOIN servidores AS ser ON o.servidor_olt = ser.id_srvidor
                                                    INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                    INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                    WHERE i.status_insta = 2 AND i.tipo_insta = 2  ORDER BY i.id_insta DESC");     
                                                    
        }

        $servicios = array_merge($serviciosInalambricos,$serviciosFibra); 

        $instalacionesCerradas = array_merge($instalacionesInlamabricas,$instalacionesFibra); 


        $todo=collect(['servicios'=>$servicios, 'instalaciones'=>$instalacionesCerradas]);


         return response()->json($todo);                               
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

    public function activarServicio(Request $request){
        
        $date = date("Y-m-d H:i:s"); 
        $datos = $request->datos;
        if($datos["userComision"] == 0){
            $generacomision = null;
            $datos["userComision"] = null;
            $datos["porcentajeComision"] = null;
        }
        else{
            $generacomision = 1;
        }

                                            

        
        

        if($datos["tipo_insta"] == 1){
        $instalacion = DB::select("SELECT * FROM instalaciones AS i
                                                INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                INNER JOIN aps AS a ON d.ap_det = a.id
                                                INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                                INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                                INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                  WHERE i.id_insta = ?",[$datos["id_insta"]])["0"];

        $nuevoServicio = DB::insert("INSERT INTO servicios (
                                        id_srv,
                                        cliente_srv,
                                        instalacion_srv,
                                        costo_instalacion_srv,  
                                        credito_srv,
                                        start_srv,
                                        tipo_srv,
                                        equipo_srv,
                                        ip_srv,
                                        mac_srv,
                                        serial_srv,
                                        ap_srv,
                                        plan_srv,
                                        tipo_plan_srv,
                                        modo_pago_srv,
                                        serie_srv,
                                        stat_srv,
                                        gen_comision_serv,
                                        user_comision_serv,
                                        porcentaje_comision_serv,
                                        comment_srv,
                                        created_at,
                                        updated_at)
                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ",
                                        [$datos["id_insta"],
                                        $datos["cliente_insta"],
                                        $datos["fechaInsta"],
                                        $datos["costo"],
                                        $datos["credito"],
                                        $datos["fechaIniSer"],
                                        $datos["tipo_insta"],
                                        $datos["modelo_det"],
                                        $datos["ipP_det"],
                                        $datos["serial_det"],
                                        $datos["serial_det"],
                                        $datos["ap_det"],
                                        $datos["plan_det"],
                                        $datos["tipo_det"],
                                        1,
                                        $datos["tipoCliente"],
                                        $datos["estadoCliente"],
                                        $generacomision,
                                        $datos["userComision"],
                                        $datos["procentajeComision"],
                                        $datos["comentario"],
                                        $date,
                                        $date
        ]);

        }else{
            $instalacion = DB::select("SELECT * FROM instalaciones AS i
                                                INNER JOIN clientes AS cl ON i.cliente_insta = cl.id
                                                INNER JOIN insta_detalles AS d ON i.id_insta = d.id_insta
                                                INNER JOIN caja_distribucion AS caj ON d.celda_det = caj.id_caja
                                                INNER JOIN manga_empalme AS m ON caj.manga_caja = m.id_manga
                                                INNER JOIN planes AS p ON d.plan_det = p.id_plan
                                                INNER JOIN equipos2 AS e ON d.modelo_det = e.id_equipo
                                                WHERE i.id_insta = ?",[$datos["id_insta"]])["0"];

            $nuevoServicio = DB::insert("INSERT INTO servicios (
                                                        id_srv,
                                                        cliente_srv,
                                                        instalacion_srv,
                                                        costo_instalacion_srv,  
                                                        credito_srv,
                                                        start_srv,
                                                        tipo_srv,
                                                        equipo_srv,
                                                        ip_srv,
                                                        mac_srv,
                                                        serial_srv,
                                                        ap_srv,
                                                        plan_srv,
                                                        tipo_plan_srv,
                                                        modo_pago_srv,
                                                        serie_srv,
                                                        stat_srv,
                                                        gen_comision_serv,
                                                        user_comision_serv,
                                                        porcentaje_comision_serv,
                                                        comment_srv,
                                                        created_at,
                                                        updated_at)
                                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ",
                                                        [$datos["id_insta"],
                                                        $datos["cliente_insta"],
                                                        $datos["fechaInsta"],
                                                        $datos["costo"],
                                                        $datos["credito"],
                                                        $datos["fechaIniSer"],
                                                        $datos["tipo_insta"],
                                                        $datos["modelo_det"],
                                                        $datos["ipP_det"],
                                                        $datos["serial_det"],
                                                        $datos["serial_det"],
                                                        $datos["celda_det"],
                                                        $datos["plan_det"],
                                                        $datos["tipo_det"],
                                                        1,
                                                        $datos["tipoCliente"],
                                                        $datos["estadoCliente"],
                                                        $generacomision,
                                                        $datos["userComision"],
                                                        $datos["procentajeComision"],
                                                        $datos["comentario"],
                                                        $date,
                                                        $date
            ]);                        
        }
        
      
        
        $id_srv = $datos["id_insta"];
        
        //cambio de equipo y/o serial
           
        if($datos["serial_det"] != $instalacion->serial_det){
            if($instalacion->serial_det != "0"){
                $mensaje = "equipos de inventario";
                $equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["nombre_equipo"]])["0"];
                $id_equipo = $equipo->id_equipo;
                $volverInventario = DB::update("UPDATE articulos SET estatus = 1 WHERE serial_articulo = ?",[$instalacion->serial_det]);
                $agregarInstalacion = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_det"]]);
                
                $nuevoSerial = DB::update("UPDATE servicios SET serial_srv = ?, equipo_srv = ? WHERE id_srv = ?",[$datos["serial_det"],$id_equipo,$id_srv]);
                $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["id_usuario"],'Inventario',"Cambio de equipo en Servicio, de Equipo ".$instalacion->nombre_equipo." Serial: ".$instalacion->serial_det." a Equipo ".$datos["nombre_equipo"].", Serial: ".$datos["serial_det"]." codigo de servicio: ".$id_srv,$date,$date]);
            }else{
                $equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["nombre_equipo"]])["0"];
                $id_equipo = $equipo->id_equipo;

               $nuevoSerial = DB::update("UPDATE servicios SET serial_srv = ?, equipo_srv = ? WHERE id_srv = ?",[$datos["serial_det"],$id_equipo,$id_srv]);
               $actEquipo = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_det"]]);
               $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["id_usuario"],'Inventario',"Cambio de equipo en Servicio, de Equipo Usado a Equipo".$datos["nombre_equipo"]." Serial: ".$datos["serial_det"]." codigo de servicio: ".$id_srv,$date,$date]);
            }
        }
        if($datos["tipo_insta"] == 1){

            if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                $nombre4 = explode(" ",$datos["nombre"]);
                $apellido4 = explode(" ",$datos["apellido"]);
                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            }else {
                $cliente1= ucwords(strtolower($datos["social"]));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                /*
                if(strlen($cliente) > 60){

                }
                */
            }
        
                retirar_ip_pd($instalacion->ipP_det,$cliente,$datos["ip_srvidor"],$datos["user_srvidor"],$datos["password_srvidor"],"P_I",0);   

                $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];
                if ($datos["carac_plan"] == 1) {
                    $parent = "Asimetricos";
                } else if ($datos["carac_plan"] == 2) {
                    $parent = "none";
                }

                activar($datos["ipP_det"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$plan->dmb_plan, $plan->umb_plan,$parent,$id_srv,$cliente2,$datos["dni"],$plan->name_plan);  
                
        }else{

            if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                $nombre4 = explode(" ",$datos["nombre"]);
                $apellido4 = explode(" ",$datos["apellido"]);
                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            }else {
                $cliente1= ucwords(strtolower($datos["social"]));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }

            retirar_pppoe_pendiente($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$id_srv);

            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];
            
            activar_pppoe($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$id_srv,$datos["dni"],$plan->name_plan);

        }



        
        $cambiarStatusInstalacion = DB::update("UPDATE instalaciones SET status_insta = 4 WHERE id_insta = ?",[$datos["id_insta"]]);

        historico_cliente::create(['history' => 'Activo por inicio de servicio', 'modulo' => 'Servicios', 'cliente' => $datos["cliente_insta"], 'responsable' => $datos["id_usuario"]]);
        
        if($datos["tipoCliente"] == 1){

        }else{
            /*Creacion de Promocion si Es necesario*/
        $promocionEspera = DB::select("SELECT * FROM promociones_en_espera AS e 
        INNER JOIN promociones AS p ON e.promocion_espera = p.id_promocion
              WHERE e.cliente_promocion = ? AND e.status_espera = 1 ORDER BY e.id_promo_espera DESC",[$datos["cliente_insta"]]);

            if(count($promocionEspera) > 0){

            if($datos["tipo_insta"] == 1){
            $fechafinal = Carbon::now()->addMonths($promocionEspera["0"]->meses);

            $planPromocion = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];

            $planServicio = $planPromocion->dmb_plan;

            if($promocionEspera["0"]->mbGratis > 0){
            if($planServicio <= $promocionEspera["0"]->mbGratis){
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio;

            if($palabra[1] != "Dedicado"){
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ?",["%".$nombrePlan." ".$nuevaVelocidad."%",$planPromocion->tipo_plan])["0"];
            }else{
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ? AND dmb_plan = ?",["%".$nombrePlan."%",$planPromocion->tipo_plan,$planServicio])["0"];
            }
            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }else{
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio - $promocionEspera["0"]->mbGratis;

            if($palabra[1] != "Dedicado"){
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ?",["%".$nombrePlan." ".$nuevaVelocidad."%",$planPromocion->tipo_plan])["0"];
            }else{
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ? AND dmb_plan = ?",["%".$nombrePlan."%",$planPromocion->tipo_plan,$planServicio])["0"];
            }
            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }          
            }else{
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio;

            if($palabra[1] != "Dedicado"){
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ?",["%".$nombrePlan." ".$nuevaVelocidad."%",$planPromocion->tipo_plan])["0"];
            }else{
            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ? AND dmb_plan = ?",["%".$nombrePlan."%",$planPromocion->tipo_plan,$planServicio])["0"];
            }
            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }

            $agregarPromocion = DB::insert('INSERT INTO fac_promo(id_cliente_p,promocion,id_servicio_p,id_plan_p,fecha,comentario,responsable,status,created_at,updated_at)
            VALUES (?,?,?,?,?,?,?,?,?,?)',
            [$datos["cliente_insta"],$promocionEspera["0"]->id_promocion,$id_srv,$nuevoPLan->id_plan,$fechafinal,"promocion ".$promocionEspera["0"]->nombre_promocion,$datos["id_usuario"],1,$date,$date]);

            $actualizarPromocionEspera= DB::update("UPDATE promociones_en_espera SET status_espera = 2 WHERE id_promo_espera = ?",[$promocionEspera["0"]->id_promo_espera]);
            if($promocionEspera["0"]->mbGratis > 0){
            if($planServicio > $promocionEspera["0"]->mbGratis){
            /*Creacion de factura al inicio del servicio*/ 
            $fechaFacturaInicio = date("d/m/Y");
            $fechaFacturaFinal = new Carbon('last day of this month');
            $date = date("d/m/Y", strtotime($datos["fechaInsta"]));

            \Artisan::call('factura_promocion', [
            'cliente' =>$datos["cliente_insta"], 'fecha'=>$date,'fecha2'=>$fechaFacturaFinal->format('d/m/Y'), 'pro'=>4, 'nro_servicio'=>$id_srv, 'responsable'=>0
            ]);
            }
            }
            historico_cliente::create(['history'=>'Creacion de Promocion para Cliente '.$promocionEspera["0"]->nombre_promocion.' Para Culminacion: '.$fechafinal, 'modulo'=>'Facturacion','cliente' => $datos["cliente_insta"], 'responsable'=>$datos["id_usuario"]]);
            }else{

            $fechafinal = Carbon::now()->addMonths($promocionEspera["0"]->meses);

            $planPromocion = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_det"]])["0"];

            $planServicio = $planPromocion->dmb_plan;

            if($promocionEspera["0"]->mbGratis > 0){
            if($planServicio <= $promocionEspera["0"]->mbGratis){
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio;

            $nuevoPLan = DB::select("SELECT * FROM planes WHERE dmb_plan = ? AND tipo_plan = ?",[$nuevaVelocidad,$planPromocion->tipo_plan])["0"];

            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }else{
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio - $promocionEspera["0"]->mbGratis;

            $nuevoPLan = DB::select("SELECT * FROM planes WHERE dmb_plan = ? AND tipo_plan = ?",[$nuevaVelocidad,$planPromocion->tipo_plan])["0"];

            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }          
            }else{
            $palabra = explode(" ",$planPromocion->name_plan);

            $nombrePlan = $palabra[0]." ".$palabra[1]; 

            $nuevaVelocidad = $planServicio;

            $nuevoPLan = DB::select("SELECT * FROM planes WHERE name_plan LIKE ? AND tipo_plan = ?",[$nuevaVelocidad,$planPromocion->tipo_plan])["0"];

            if ($nuevoPLan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($nuevoPLan->carac_plan == 2) {
            $parent = "none";
            }
            }

            $agregarPromocion = DB::insert('INSERT INTO fac_promo(id_cliente_p,promocion,id_servicio_p,id_plan_p,fecha,comentario,responsable,status,created_at,updated_at)
            VALUES (?,?,?,?,?,?,?,?,?,?)',
            [$datos["cliente_insta"],$promocionEspera["0"]->id_promocion,$id_srv,$nuevoPLan->id_plan,$fechafinal,"promocion ".$promocionEspera["0"]->nombre_promocion,$datos["id_usuario"],1,$date,$date]);

            $actualizarPromocionEspera= DB::update("UPDATE promociones_en_espera SET status_espera = 2 WHERE id_promo_espera = ?",[$promocionEspera["0"]->id_promo_espera]);
            if($promocionEspera["0"]->mbGratis > 0){
            if($planServicio > $promocionEspera["0"]->mbGratis){
            /*Creacion de factura al inicio del servicio*/ 
            $fechaFacturaInicio = date("d/m/Y");
            $fechaFacturaFinal = new Carbon('last day of this month');
            $date = date("d/m/Y", strtotime($datos["fechaInsta"]));

            \Artisan::call('factura_promocion', [
            'cliente' =>$datos["cliente_insta"], 'fecha'=>$date,'fecha2'=>$fechaFacturaFinal->format('d/m/Y'), 'pro'=>4, 'nro_servicio'=>$id_srv, 'responsable'=>0
            ]);
            }
            }
            historico_cliente::create(['history'=>'Creacion de Promocion para Cliente '.$promocionEspera["0"]->nombre_promocion.' Para Culminacion: '.$fechafinal, 'modulo'=>'Facturacion','cliente' => $datos["cliente_insta"], 'responsable'=>$datos["id_usuario"]]);

            }



            }else{

            /*Creacion de factura al inicio del servicio*/ 
            $fechaFacturaInicio = date("d/m/Y");
            $fechaFacturaFinal = new Carbon('last day of this month');
            $date = date("d/m/Y", strtotime($datos["fechaInsta"]));

            \Artisan::call('factura:generar', [
            'cliente' => $datos["cliente_insta"], 'fecha'=>$date,'fecha2'=>$fechaFacturaFinal->format('d/m/Y') , 'pro'=>1, 'nro_servicio'=>$id_srv, 'responsable'=>0
            ]);
            }  
        }

        

        return response()->json($request);
    }

    public function guardarServicio(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $datos = $request->datos;
        if($datos["usuarioComision"] == 0){
            $generacomision = null;
            $datos["usuarioComision"] = null;
            $datos["porcentaje"] = null;
        }else{
            $generacomision = 1;
        }
        
        $nuevoServicio = DB::insert("INSERT INTO servicios (cliente_srv,
                                                                instalacion_srv,
                                                                costo_instalacion_srv,  
                                                                credito_srv,
                                                                start_srv,
                                                                tipo_srv,
                                                                equipo_srv,
                                                                ip_srv,
                                                                mac_srv,
                                                                serial_srv,
                                                                ap_srv,
                                                                plan_srv,
                                                                tipo_plan_srv,
                                                                modo_pago_srv,
                                                                serie_srv,
                                                                stat_srv,
                                                                gen_comision_serv,
                                                                user_comision_serv,
                                                                porcentaje_comision_serv,
                                                                comment_srv,
                                                                created_at,
                                                                updated_at)
                                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ",
                                                                [$datos["idCliente"],
                                                                $datos["fechaInstalacion"],
                                                                $datos["costo"],
                                                                $datos["credito"],
                                                                $datos["fechaInicioServicio"],
                                                                $datos["tipoServicio"],
                                                                $datos["equipo"],
                                                                $datos["ip"],
                                                                $datos["serial"],
                                                                $datos["serial"],
                                                                $datos["ap"],
                                                                $datos["plan"],
                                                                $datos["tipoPlan"],
                                                                1,
                                                                $datos["tipoCliente"],
                                                                1,
                                                                $generacomision,
                                                                $datos["usuarioComision"],
                                                                $datos["porcentaje"],
                                                                $datos["comentario"],
                                                                $date,
                                                                $date
                                                                ]);
        $id_srv = DB::select("SELECT * FROM servicios ORDER BY id_srv DESC LIMIT 1")["0"]->id_srv;                                                        

        
        //activar Cliente
        $datosCliente = DB::select("SELECT * FROM clientes WHERE id = ?",[$datos["idCliente"]])["0"];
        if( $datosCliente->kind == 'V'|| $datosCliente->kind =='E'){
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

        $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan"]])["0"];
        if ($plan->carac_plan == 1) {
            $parent = "Asimetricos";
        } else if ($plan->carac_plan == 2) {
            $parent = "none";
        }

        $datosMK = DB::select("SELECT * FROM aps AS a
                                     INNER JOIN celdas AS c ON a.celda_ap = c.id_celda
                                     INNER JOIN servidores AS s ON c.servidor_celda = s.id_srvidor
                                        WHERE a.id = ?",[$datos["ap"]])["0"];

        activar($datos["ip"], $cliente,$datosMK->ip_srvidor, $datosMK->user_srvidor, $datosMK->password_srvidor,$plan->dmb_plan, $plan->umb_plan,$parent,$id_srv,$cliente2,$datosCliente->dni,$plan->name_plan);  

         //asignacion equipo
         $actEquipo = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial"]]);
         $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["id_usuario"],'Inventario',"Se Agendo Servicio, Equipo: ".$datos["modeloEquipo"]." Serial: ".$datos["serial"].", al Cliente".$cliente,$date,$date]);

        //agendar IP
        $IP = DB::insert("INSERT INTO lista_ips(ip,cliente_ip,status_ip,ip_servicio,created_at,updated_at) VALUES(?,?,?,?,?,?)",[$datos["ip"],$datos["idCliente"],1,0,$date,$date]);

        return response()->json($request);
    }

    public function editarServicio(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $datos = $request->datos;

        $servicio =  DB::select("SELECT * FROM servicios AS s
                                     INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                     INNER JOIN aps AS a ON s.ap_srv = a.id
                                     INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                     INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                     INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                     INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                        WHERE s.id_srv = ?",[$datos["id_srv"]])["0"];

        //cambio de estado del servicio
        
        if ($datos["stat_srv"] != $servicio->stat_srv) {
            //activar servicio cliente
            if($datos["stat_srv"] == 1){
                if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                    $nombre4 = explode(" ",$datos["nombre"]);
                    $apellido4 = explode(" ",$datos["apellido"]);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);
    
                    $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }else {
                    $cliente1= ucwords(strtolower($datos["social"]));
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }
                
                cola_de_ejecucion::create(['id_srv'=>$datos["id_srv"], 'accion'=>'s', 'contador'=>'1']);

                $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["id_plan"]])["0"];
                if ($plan->carac_plan == 1) {
                    $parent = "Asimetricos";
                } else if ($plan->carac_plan == 2) {
                    $parent = "none";
                }
                if($servicio->tipo_srv == 1){
                    activar($datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$plan->dmb_plan, $plan->umb_plan,$parent,$datos["id_srv"],$cliente2,$datos["dni"],$plan->name_plan);
                }else{
                    activar_pppoe($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"],$datos["dni"],$plan->name_plan);
                }
                historico_cliente::create(['history'=>'Servicio Activado', 'modulo'=>'Servicios', 'cliente'=>$datos["cliente_srv"], 'responsable'=>$datos["id_usuario"]]);
                historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Activa al cliente: '.$cliente]);
                $adicionales=servicios::where('id_srv', $datos["id_srv"]);
                $adicionales->update(['stat_srv'=>1]);
            }
            //suspender servicio cliente
           if($datos["stat_srv"] == 3){
                if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                    $nombre4 = explode(" ",$datos["nombre"]);
                    $apellido4 = explode(" ",$datos["apellido"]);
                    $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                    $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                }else {
                    $cliente1= ucwords(strtolower($datos["social"]));
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }
                
                cola_de_ejecucion::create(['id_srv'=>$datos["id_srv"], 'accion'=>'s', 'contador'=>'1']);
                if($servicio->tipo_srv == 1){
                    suspender($datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"]);
                }else{
                    retirar_pppoe_pendiente($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"]);
                }
                historico_cliente::create(['history'=>'Servicio Suspendido', 'modulo'=>'Servicios', 'cliente'=>$datos["cliente_srv"], 'responsable'=>$datos["id_usuario"]]);
                historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
                
                $adicionales=servicios::where('id_srv', $datos["id_srv"]);
                $adicionales->update(['stat_srv'=>3]);
                
                
           }
           //retirar servicio cliente
           if($datos["stat_srv"] == 4){
            if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucwords(strtolower($datos["social"]));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            
            cola_de_ejecucion::create(['id_srv'=>$datos["id_srv"], 'accion'=>'s', 'contador'=>'1']);
            
            retirar($datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"]);

            $mesActual = Carbon::now()->month;
            $añoActual = Carbon::now()->year;

            $facturaRetiro = DB::select("SELECT * FROM fac_controls WHERE id_cliente = ? AND YEAR(created_at) <= ? ORDER BY id DESC LIMIT 1",[$datos['cliente_srv'],$añoActual]);
            
            $productosRetiro = DB::select("SELECT f.*, DAY(created_at) as dia FROM fac_products as f WHERE codigo_factura = ? ORDER BY id DESC LIMIT 1",[$facturaRetiro["0"]->id]);

            $inicio = $productosRetiro["0"]->dia;
            $final = new Carbon('last day of this month');

            $diasConServicioTotal = $final->day - $inicio; 

            $precioDiario = $productosRetiro["0"]->precio_articulo / $diasConServicioTotal;

            $finalDeServicio = Carbon::now()->day - $inicio;

            $precioFinal = $precioDiario * $finalDeServicio;

            $precioRestante = $productosRetiro["0"]->precio_articulo - $precioFinal;

            $actualizarProducto = DB::select("UPDATE fac_products SET precio_articulo = ?, comment_articulo = 'Monto modificado por retiro de servicio' WHERE id = ?",[$precioFinal,$productosRetiro["0"]->id]);

            $añadirCuentaIncobrable = DB::select("INSERT INTO cuentas_incobrables(factura,monto,denominacion,created_at,updated_at) VALUES (?,?,?,?,?)",[$productosRetiro["0"]->codigo_factura,$precioRestante,"$",$date,$date]);

            //$diasTotales=$final->diff($inicio)->format('%a');

            $montoFacturaProductos = DB::Select("SELECT round(SUM(precio_articulo), 2) AS monto from  fac_products where  codigo_factura = ?",[$facturaRetiro["0"]->id])["0"]->monto;
            $montoFacturaPagos = DB::Select("SELECT round(SUM(pag_monto), 2) AS pagado from  fac_pagos where  fac_id = ?",[$facturaRetiro["0"]->id])["0"]->pagado;


            if ($montoFacturaPagos > $montoFacturaProductos) {
                $restanteFacturaPagos = $montoFacturaPagos - $montoFacturaProductos;

                $pagoAnterior = DB::select("SELECT * from  fac_pagos where  fac_id = ? ORDER BY pag_monto DESC LIMIT 1",[$facturaRetiro["0"]->id])["0"];

                $nuevoMontoPago =  $pagoAnterior->pag_monto - $restanteFacturaPagos;

                $actualizarPagoAnterior = DB::update("UPDATE fac_pagos SET pag_monto = ? WHERE id = ?",[round($nuevoMontoPago,2),$pagoAnterior->id]);

                $balanceIn = DB::select("SELECT * FROM balance_clientes_ins WHERE id_bal_in = ?",[$pagoAnterior->balance_pago_in])["0"];

                $nuevoRestanteBalanceIn = $balanceIn->bal_rest_in + $restanteFacturaPagos;

                $actualizarBalanceIn = DB::update("UPDATE balance_clientes_ins  SET bal_rest_in = ? WHERE id_bal_in = ?",[$nuevoRestanteBalanceIn,$balanceIn->id_bal_in]);
                
            }


            
            historico_cliente::create(['history'=>'Servicio Retirado', 'modulo'=>'Servicios', 'cliente'=>$datos["cliente_srv"], 'responsable'=>$datos["id_usuario"]]);
            historico_cliente::create(['history'=>'Factura Modificada por Retiro de Servicio, excedente en Cuentas Incobrables', 'modulo'=>'Cuentas Incobrables', 'cliente'=>$datos["cliente_srv"], 'responsable'=>0]);
            historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Suspende al cliente: '.$cliente]);
            
            $adicionales=servicios::where('id_srv', $datos["id_srv"]);
            $adicionales->update(['stat_srv'=>4]);
            
            
            }
            

            //exonerar servicio cliente
            if($datos["stat_srv"] == 5){
                if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                    $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }else {
                    $cliente1= ucwords(strtolower($datos["social"]));
                    $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                    $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                    $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                }
                
                cola_de_ejecucion::create(['id_srv'=>$datos["id_srv"], 'accion'=>'s', 'contador'=>'1']);

                $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["id_plan"]])["0"];
                if ($plan->carac_plan == 1) {
                    $parent = "Asimetricos";
                } else if ($plan->carac_plan == 2) {
                    $parent = "none";
                }
                exonerar($datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$plan->dmb_plan, $plan->umb_plan,$parent,$datos["id_srv"]);
                historico_cliente::create(['history'=>'Servicio Exonerado', 'modulo'=>'Servicios', 'cliente'=>$datos["cliente_srv"], 'responsable'=>$datos["id_usuario"]]);
                historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Exonera al cliente: '.$cliente]);
                $adicionales=servicios::where('id_srv', $datos["id_srv"]);
                $adicionales->update(['id_srv'=>5]);
            }
        }

        //cambio de equipo y/o serial
           
        if($datos["serial_srv"] != $servicio->serial_srv){
            if($servicio->serial_srv != "0"){
                $mensaje = "equipos de inventario";
                $equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["nombre_equipo"]])["0"];
                $id_equipo = $equipo->id_equipo;
                $volverInventario = DB::update("UPDATE articulos SET estatus = 1 WHERE serial_articulo = ?",[$servicio->serial_srv]);
                $agregarInstalacion = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_srv"]]);
                
                $nuevoSerial = DB::update("UPDATE servicios SET serial_srv = ?, equipo_srv = ? WHERE id_srv = ?",[$datos["serial_srv"],$id_equipo,$servicio->id_srv]);
                $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["id_usuario"],'Inventario',"Cambio de equipo en Servicio, de Equipo ".$servicio->nombre_equipo." Serial: ".$servicio->serial_srv." a Equipo ".$datos["nombre_equipo"].", Serial: ".$datos["serial_srv"]." codigo de servicio: ".$datos["id_srv"],$date,$date]);
            }else{
                $equipo = DB::select("SELECT * FROM equipos2 WHERE nombre_equipo = ?",[$datos["nombre_equipo"]])["0"];
                $id_equipo = $equipo->id_equipo;

               $nuevoSerial = DB::update("UPDATE servicios SET serial_srv = ?, equipo_srv = ? WHERE id_srv = ?",[$datos["serial_srv"],$id_equipo,$servicio->id_srv]);
               $actEquipo = DB::update("UPDATE articulos SET estatus = 3 WHERE serial_articulo = ?",[$datos["serial_srv"]]);
               $historialInventario = DB::insert("INSERT INTO historicos(responsable,modulo,detalle,created_at,updated_at) VALUES (?,?,?,?,?)",[$datos["id_usuario"],'Inventario',"Cambio de equipo en Servicio, de Equipo Usado a Equipo".$datos["nombre_equipo"]." Serial: ".$datos["serial_srv"]." codigo de servicio: ".$datos["id_srv"],$date,$date]);
            }
        }

        //cambio AP
        if($datos["ap_srv"] != $servicio->ap_srv){
            $nuevoSerial = DB::update("UPDATE servicios SET ap_srv = ? WHERE id_srv = ?",[$datos["ap_srv"],$servicio->id_srv]);
        }

         //cambio de plan
        if($datos["plan_srv"] != $servicio->plan_srv){
            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_srv"]])["0"];
            if ($plan->carac_plan == 1) {
                $parent = "Asimetricos";
            } else if ($plan->carac_plan == 2) {
                $parent = "none";
            }
            if($datos["kind"] == 'V'|| $datos["kind"] =='E'){
                $nombre4 = explode(" ",$datos["nombre"]);
                $apellido4 = explode(" ",$datos["apellido"]);
                $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
            }else {
                $cliente1= ucwords(strtolower($datos["social"]));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }

            if($servicio->tipo_srv == 1){
                Cambiar_plan($datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$plan->dmb_plan, $plan->umb_plan,$parent,$datos["id_srv"],$plan->name_plan);
            }else{
                retirar_pppoe_pendiente($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"]);
                activar_pppoe($cliente2,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$datos["id_srv"],$datos["dni"],$plan->name_plan);
            }

            $cambioPlan = DB::update("UPDATE servicios SET plan_srv = ?,tipo_plan_srv = ? WHERE id_srv = ?",[$datos["plan_srv"],$datos["tipo_plan_srv"],$datos["id_srv"]]);
            historico_cliente::create(['history' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan, 'modulo' => 'Servicios', 'cliente' => $servicio->cliente_srv, 'responsable' => $datos["id_usuario"]]);
            historico::create(['responsable' => $datos["id_usuario"], 'modulo' => 'Servicios', 'detalle' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan . '. Cliente ' . $cliente]);
        }

        //cambip IP
        if($datos["ip_srv"] != $servicio->ip_srv){

            if($datos["stat_srv"] == 1 || $datos["stat_srv"] == 5){
                $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$datos["plan_srv"]])["0"];
                    if ($plan->carac_plan == 1) {
                        $parent = "Asimetricos";
                    } else if ($plan->carac_plan == 2) {
                        $parent = "none";
                    }
                    if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                        $nombre4 = explode(" ",$datos["nombre"]);
                        $apellido4 = explode(" ",$datos["apellido"]);
                        $cliente3= ucfirst($nombre4[0])." ".ucfirst($apellido4[0]);

                        $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                        $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                        $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                        $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                        $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente3);
                    }else {
                        $cliente1= ucwords(strtolower($datos["social"]));
                        $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                        $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                        $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
                        $cliente2 = str_replace($remp_cliente, $correct_cliente, $cliente1);
                    }

                    cambiar_Ip($servicio->ip_srv,$datos["ip_srv"], $cliente,$datos["ip_srvidor"], $datos["user_srvidor"], $datos["password_srvidor"],$plan->dmb_plan, $plan->umb_plan,$parent,$datos["id_srv"],$cliente2,$datos["dni"],$plan->name_plan);        
            }

            
            historico_cliente::create(['history'=>'Cambio de ip: '.$servicio->ip_srv.' ahora tiene '.$datos["ip_srv"], 'modulo'=>'Servicios', 'cliente'=>$servicio->cliente_srv, 'responsable'=>$datos["id_usuario"]]);
            historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Cambio de ip: '.$servicio->ip_srv.' ahora tiene '.$datos["ip_srv"].'. Cliente '.$servicio->cliente_srv]);

            lista_ip::where('ip_servicio', $datos["id_srv"])->update(['ip' => $datos["ip_srv"]]);

            $cambioIp = DB::update("UPDATE servicios SET ip_srv = ? WHERE id_srv = ?",[$datos["ip_srv"],$servicio->id_srv]);
            
        }

        //agregar Comision
        if($datos["user_comision_serv"] != $servicio->user_comision_serv){
            if( $datos["kind"] == 'V'|| $datos["kind"] =='E'){
                $cliente1= ucfirst($datos["nombre"])." ".ucfirst($datos["apellido"]);
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
                $cliente1= ucwords(strtolower($datos["social"]));
                $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
                $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
                $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            $agrgarUsuarioComision = DB::update("UPDATE servicios SET gen_comision_serv = ?, user_comision_serv = ?, porcentaje_comision_serv = ? WHERE id_srv = ?",[1,$datos["user_comision_serv"],$datos["porcentaje_comision_serv"],$servicio->id_srv]);

            historico_cliente::create(['history'=>'Servicio ahora genera comision', 'responsable'=>$datos["id_usuario"]]);
            historico::create(['responsable'=>$datos["id_usuario"], 'modulo'=>'Servicios', 'detalle'=>'Servicio ahora genera comision'.'. Cliente '.$cliente]);
        }

        //cambiar facturable/ no facturable
        if($datos["serie_srv"] != $servicio->serie_srv){
            $cambiar = DB::update("UPDATE servicios SET serie_srv = ? WHERE id_srv = ?",[$datos["serie_srv"],$servicio->id_srv]);
        }

        //cambiar fecha instalacion
        if($datos["instalacion_srv"] != $servicio->instalacion_srv){
            $cambiarFechaI = DB::update("UPDATE servicios SET instalacion_srv = ? WHERE id_srv = ?",[$datos["instalacion_srv"],$servicio->id_srv]);
        }

        //cambiar fecha incio de servicio
        if($datos["start_srv"] != $servicio->start_srv){
            $cambiarFechaI = DB::update("UPDATE servicios SET start_srv = ? WHERE id_srv = ?",[$datos["start_srv"],$servicio->id_srv]);
        }

        //cambiar comentario
        if ($datos["comment_srv"] != $servicio->comment_srv) {
            $cambiarComentario = DB::update("UPDATE servicios SET comment_srv = ? WHERE id_srv = ?",[$datos["comment_srv"],$servicio->id_srv]);
        }

        
        
        return response()->json($request);
    }

    public function servicioCliente($id){

        $result = DB::select("SELECT * FROM servicios AS s
                                 INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                 INNER JOIN aps AS a ON s.ap_srv = a.id
                                 INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                    WHERE s.cliente_srv = ? AND s.stat_srv = 1",[$id]);

        return response()->json($result);
    }

    public function servicioIndividual(Request $request){
        if($request["tipo"] == 1){
            $result = DB::select("SELECT * FROM servicios AS s
                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                    INNER JOIN aps AS a ON s.ap_srv = a.id
                                    INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                    INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                    INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                        WHERE s.id_srv = ?",[$request["id"]])["0"];
        }else{
            $result = DB::select("SELECT * FROM servicios AS s
                                        INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                        INNER JOIN caja_distribucion AS ca ON s.ap_srv = ca.id_caja
                                        INNER JOIN manga_empalme AS m ON ca.manga_caja = m.id_manga
                                        INNER JOIN olts AS o ON m.olt_manga = o.id_olt
                                        INNER JOIN servidores AS ser ON o.servidor_olt = ser.id_srvidor
                                        INNER JOIN planes AS p ON s.plan_srv = p.id_plan 
                                        INNER JOIN equipos2 AS e ON s.equipo_srv = e.id_equipo 
                                            WHERE s.id_srv = ?",[$request["id"]])["0"];
        }

        return response()->json($result);
    }

    
    public function usuariosComision(){

        $result = DB::select("SELECT * FROM users where comision = 1");

        return response()->json($result);
    }

    public function generarCompromisoServicio(Request $request){
        $date = date("Y-m-d H:i:s"); 
        $servicio =  DB::select("SELECT * FROM servicios AS s
                INNER JOIN clientes AS c ON s.cliente_srv = c.id
                INNER JOIN aps AS a ON s.ap_srv = a.id
                INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                    WHERE s.id_srv = ?",[$request["compromiso"]])["0"];
            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$request["plan"]])["0"];
            if ($plan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($plan->carac_plan == 2) {
            $parent = "none";
            }
            if( $servicio->kind == 'V'|| $servicio->kind =='E'){
            $cliente1= ucfirst($servicio->nombre)." ".ucfirst($servicio->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
            $cliente1= ucwords(strtolower($servicio->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            Cambiar_plan($servicio->ip_srv, $cliente,$servicio->ip_srvidor, $servicio->user_srvidor, $servicio->password_srvidor,$plan->dmb_plan, $plan->umb_plan,$parent,$request["compromiso"],$plan->name_plan);

        
        historico_cliente::create(['history' => 'Generacion de Compromiso de Servicio al Cliente', 'modulo' => 'Servicios', 'cliente' => $servicio->cliente_srv, 'responsable' => $request["id_user"]]);
        historico::create(['responsable' => $request["id_user"], 'modulo' => 'Servicios', 'detalle' => 'Generacion de Compromiso de Servicio al Cliente, Cliente ' . $cliente]);

        $generarCompromiso = DB::insert("INSERT INTO  compromisos_servicios(id_cliente_com,id_servicio_com,com_tipo_plan,com_plan,status,fecha_finalizacion,id_responsable,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)",[$request["cliente"],$request["compromiso"],$request["tipo_plan"],$request["plan"],1,$request["fecha"],$request["id_user"],$date,$date]);

        return response()->json($generarCompromiso);
    }

    public function verificarCompromisoServicio(Request $request){

        $verificarCompromisoServicio = DB::select("SELECT * FROM compromisos_servicios AS c INNER JOIN planes AS p ON c.com_plan = p.id_plan WHERE id_servicio_com = ? AND c.status = 1",[$request["servicio"]]);
        return response()->json($verificarCompromisoServicio);

    }

    public function EliminarCompromisoServicio(Request $request){
        $servicio =  DB::select("SELECT * FROM servicios AS s
                                    INNER JOIN clientes AS c ON s.cliente_srv = c.id
                                    INNER JOIN aps AS a ON s.ap_srv = a.id
                                    INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                                    INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan  
                                         WHERE s.id_srv = ?",[$request["servicio"]])["0"];
           $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$servicio->plan_srv])["0"];
           if ($plan->carac_plan == 1) {
           $parent = "Asimetricos";
           } else if ($plan->carac_plan == 2) {
           $parent = "none";
           }
           if( $servicio->kind == 'V'|| $servicio->kind =='E'){
           $cliente1= ucfirst($servicio->nombre)." ".ucfirst($servicio->apellido);
           $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
           $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
           $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
           }else {
           $cliente1= ucwords(strtolower($servicio->social));
           $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
           $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
           $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
           }
           Cambiar_plan($servicio->ip_srv, $cliente,$servicio->ip_srvidor, $servicio->user_srvidor, $servicio->password_srvidor,$plan->dmb_plan, $plan->umb_plan,$parent,$request["servicio"],$plan->name_plan);

           $EliminarCompromiso = DB::update("UPDATE compromisos_servicios SET status = 2 WHERE id_compromiso = ?",[$request["compromiso"]]);

           historico_cliente::create(['history' => 'Eliminacion de Compromiso de Servicio al Cliente', 'modulo' => 'Servicios', 'cliente' => $servicio->cliente_srv, 'responsable' => $request["id_user"]]);
           historico::create(['responsable' => $request["id_user"], 'modulo' => 'Servicios', 'detalle' => 'Eiminacion de Compromiso de Servicio al Cliente, Cliente ' . $cliente]);

        return response()->json($request);
    }

    public function EditarCompromisoServicio(Request $request){

        $servicio =  DB::select("SELECT * FROM servicios AS s
                INNER JOIN clientes AS c ON s.cliente_srv = c.id
                INNER JOIN aps AS a ON s.ap_srv = a.id
                INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                    WHERE s.id_srv = ?",[$request["servicio"]])["0"];

            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$request["plan"]])["0"];
            if ($plan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($plan->carac_plan == 2) {
            $parent = "none";
            }
            if( $servicio->kind == 'V'|| $servicio->kind =='E'){
            $cliente1= ucfirst($servicio->nombre)." ".ucfirst($servicio->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
            $cliente1= ucwords(strtolower($servicio->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            Cambiar_plan($servicio->ip_srv, $cliente,$servicio->ip_srvidor, $servicio->user_srvidor, $servicio->password_srvidor,$plan->dmb_plan, $plan->umb_plan,$parent,$request["compromiso"],$plan->name_plan);

        // $cambioPlan = DB::update("UPDATE servicios SET plan_srv = ?,tipo_plan_srv = ? WHERE id_srv = ?",[$datos["plan_srv"],$datos["tipo_plan_srv"],$datos["id_srv"]]);
        //historico_cliente::create(['history' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan, 'modulo' => 'Servicios', 'cliente' => $servicio->cliente_srv, 'responsable' => $datos["id_usuario"]]);
        //historico::create(['responsable' => $datos["id_usuario"], 'modulo' => 'Servicios', 'detalle' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan . '. Cliente ' . $cliente]);

        $EditarCompromiso = DB::insert("UPDATE compromisos_servicios SET com_tipo_plan = ?,com_plan = ?,fecha_finalizacion = ? WHERE id_compromiso = ?",[$request["tipo_plan"],$request["plan"],$request["tipo_plan"],$request["fecha"],$request["compromiso"]]);


        return response()->json($EditarCompromiso);
    }

    public function GenerarFacturaPro(Request $request){
      
         $servicio =  DB::select("SELECT * FROM servicios AS s
                INNER JOIN clientes AS c ON s.cliente_srv = c.id
                INNER JOIN planes AS p on s.plan_srv = p.id_plan
                INNER JOIN aps AS a ON s.ap_srv = a.id
                INNER JOIN celdas AS cel ON a.celda_ap = cel.id_celda
                INNER JOIN servidores AS ser ON cel.servidor_celda = ser.id_srvidor
                    WHERE s.id_srv = ?",[$request["servicio"]])["0"];

            $plan = DB::select("SELECT * FROM planes WHERE id_plan = ?",[$request["plan"]])["0"];
            if ($plan->carac_plan == 1) {
            $parent = "Asimetricos";
            } else if ($plan->carac_plan == 2) {
            $parent = "none";
            }
            
            if( $servicio->kind == 'V'|| $servicio->kind =='E'){
            $cliente1= ucfirst($servicio->nombre)." ".ucfirst($servicio->apellido);
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }else {
            $cliente1= ucwords(strtolower($servicio->social));
            $remp_cliente= array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ý','ý','þ','ÿ');
            $correct_cliente= array('a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','s','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','y','y','b','y');
            $cliente = str_replace($remp_cliente, $correct_cliente, $cliente1);
            }
            Cambiar_plan($servicio->ip_srv, $cliente,$servicio->ip_srvidor, $servicio->user_srvidor, $servicio->password_srvidor,$plan->dmb_plan, $plan->umb_plan,$parent,$request["compromiso"],$plan->name_plan);
            

            $hoy = Carbon::now();
            $mañana = $hoy->addDay();
            $inicioMes = new Carbon("first day of this month");
            $inicioSiguienteMes = new Carbon('first day of next month');
            $finalMes = new Carbon('last day of this month');
            $diasTranscuridos = $inicioMes->diff($hoy)->format('%a');
            $diasRestantes=$hoy->diff($inicioSiguienteMes)->format('%a');
            $diasCompletos=$finalMes->diff($inicioMes)->format('%a');


            $productoAnterior = DB::select("SELECT * from fac_products WHERE codigo_factura = ? ORDER BY id DESC LIMIT 1",[$request["id_fac"]])["0"]; 
            
            $precioAnterior = $productoAnterior->precio_articulo/$diasCompletos;
            $precioProductoAnterior = $precioAnterior* $diasTranscuridos; 

            $actualizarProductoAnterior = DB::update("UPDATE fac_products SET precio_unitario = ?, precio_articulo = ? WHERE id = ?",[round($precioProductoAnterior,2),round($precioProductoAnterior,2),$productoAnterior->id]);

            $precioNuevo = $plan->taza / $diasCompletos;
            $precioProductoNuevo = $precioNuevo * $diasRestantes;

            $montoFacturaProductos = DB::Select("SELECT round(SUM(precio_articulo), 2) AS monto from  fac_products where  codigo_factura = ?",[$request["id_fac"]])["0"]->monto;
            $montoFacturaPagos = DB::Select("SELECT round(SUM(pag_monto), 2) AS pagado from  fac_pagos where  fac_id = ?",[$request["id_fac"]])["0"]->pagado;


            if ($montoFacturaPagos > $montoFacturaProductos) {
                $restanteFacturaPagos = $montoFacturaPagos - $montoFacturaProductos;

                $pagoAnterior = DB::select("SELECT * from  fac_pagos where  fac_id = ? ORDER BY pag_monto DESC LIMIT 1",[$request["id_fac"]])["0"];

                $nuevoMontoPago =  $pagoAnterior->pag_monto - $restanteFacturaPagos;

                $actualizarPagoAnterior = DB::update("UPDATE fac_pagos SET pag_monto = ? WHERE id = ?",[round($nuevoMontoPago,2),$pagoAnterior->id]);

                $balanceIn = DB::select("SELECT * FROM balance_clientes_ins WHERE id_bal_in = ?",[$pagoAnterior->balance_pago_in])["0"];

                $nuevoRestanteBalanceIn = $balanceIn->bal_rest_in + $restanteFacturaPagos;

                $actualizarBalanceIn = DB::update("UPDATE balance_clientes_ins  SET bal_rest_in = ? WHERE id_bal_in = ?",[$nuevoRestanteBalanceIn,$balanceIn->id_bal_in]);
                
            }

            $agregarProductoNuevo = DB::insert("INSERT INTO fac_products(codigo_factura,codigo_articulo,nombre_articulo,precio_unitario,IVA,cantidad,precio_articulo,comment_articulo,created_at,updated_at)
                                                    VALUES (?,?,?,?,?,?,?,?,?,?)",
                                                    [$productoAnterior->codigo_factura,$plan->id_plan,$plan->name_plan,$precioProductoNuevo,16,1,$precioProductoNuevo,"Prorrateo de Servicio",$hoy,$hoy]);

            $actualizarServicio = DB::update("UPDATE servicios SET plan_srv = ?, tipo_plan_srv = ? WHERE id_srv = ?",[$request["plan"],$request["tipoPlan"],$request["servicio"]]);

            revisarBalance_in($servicio->cliente_srv);

            historico_cliente::create(['history' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan, 'modulo' => 'Servicios', 'cliente' => $servicio->cliente_srv, 'responsable' => $request["id_usuario"]]);
            historico::create(['responsable' => $request["id_usuario"], 'modulo' => 'Servicios', 'detalle' => 'Cambio de plan: ' . $servicio->name_plan . ' ahora tiene ' . $plan->name_plan . '. Cliente ' . $cliente]);
            
        return response()->json($agregarProductoNuevo);
    }
}