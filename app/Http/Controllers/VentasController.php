<?php

namespace App\Http\Controllers;

use App\ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\historico_cliente;
use App\historico;

class VentasController extends Controller
{
  
    public function index(Request $request)
    {

        if($request->user == 1){
            $ventas = DB::select("SELECT * FROM ventas AS v 
                                    INNER JOIN clientes AS c ON v.cliente_venta = c.id");

            foreach ($ventas as $v) {
                if ($v->promo_venta != 0 ) {
                    $promocion = DB::select("SELECT * FROM promociones WHERE id_promocion = ? ",[$v->promo_venta])[0];
                    $v->nombre_promocion  = $promocion->nombre_promocion;
                }
            }                            

        }else{
            $ventas = DB::select("SELECT * FROM ventas AS v 
            INNER JOIN clientes AS c ON v.cliente_venta = c.id WHERE responsable_venta = ?",[$request->user]);

                foreach ($ventas as $v) {
                    if ($v->promo_venta != 0 ) {
                        $promocion = DB::select("SELECT * FROM promociones WHERE id_promocion = ? ",[$v->promo_venta])[0];
                        $v->nombre_promocion  = $promocion->nombre_promocion;
                    }
                }                            

        }

       
       return response()->json($ventas);
    }

    public function store(Request $request)
    {
        $date = date("Y-m-d H:i:s");
        $clientePotencial = DB::select("SELECT * FROM pclientes WHERE id = ?",[$request->cliente])["0"];

        $agregarCliente = DB::insert("INSERT INTO clientes (kind,dni,email,nombre,apellido,direccion,estado,municipio,parroquia,day_of_birth,serie,tipo_planes,phone1,phone2,social,comment,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                                         [
                                            $clientePotencial->kind,
                                            $clientePotencial->dni,
                                            $clientePotencial->email,
                                            $clientePotencial->nombre,
                                            $clientePotencial->apellido,
                                            $clientePotencial->direccion,
                                            $clientePotencial->estado,
                                            $clientePotencial->municipio,
                                            $clientePotencial->parroquia,
                                            $clientePotencial->day_of_birth,
                                            $clientePotencial->serie,
                                            1,
                                            $clientePotencial->phone1,
                                            $clientePotencial->phone2,
                                            $clientePotencial->social,
                                            $clientePotencial->comment,
                                            $date,
                                            $date
                                         ]);

        $id_cliente = DB::select("SELECT * FROM clientes ORDER BY id DESC LIMIT 1")["0"]->id; 

        $actualizarPotencial = DB::update("UPDATE pclientes SET id_cli = ? WHERE id = ?",[$id_cliente,$request->cliente]);


        $agregarVenta = DB::insert("INSERT INTO ventas(cliente_venta,promo_venta,status_venta,tasa_venta,responsable_venta,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$id_cliente,0,1,1,$request->user,$date,$date]);

        return response()->json($request);
    }

    public function guardarPromoVenta(Request $request)
    {
        $actulizarVenta = DB::update("UPDATE ventas SET promo_venta = ? WHERE id_venta = ?",[$request->promo,$request->venta]);

        return response()->json($request);
    }

    public function guardarTipoVenta(Request $request)
    {

        if ($request->tipo == 1) {
            $actulizarVenta = DB::update("UPDATE ventas SET tasa_venta = 0 WHERE id_venta = ?",[$request->venta]);
        } else {
            $actulizarVenta = DB::update("UPDATE ventas SET tasa_venta = ? WHERE id_venta = ?",[$request->tasa,$request->venta]);
        }
        

        return response()->json($request);
    }

    public function guardaVentaInstalacion(Request $request)
    {
        
        
        //variables para la creacion de una nueva instalacion//
        $id_usuario = $request ->id_user;
        $id_cliente = $request ->id_cliente;
        $modelo = $request ->modeloEquipo; 
        $celda = $request ->celda;  
        $ip = $request ->ip;
        $plan = $request ->plan;  
        $tipoPlan = $request ->tipoPlan;  
        $serial = $request ->serial; 
        $instalacion = $request->instalacion;
        $check = $request ->check;
        $date = date("Y-m-d H:i:s"); 


        $venta = DB::select("SELECT * FROM ventas WHERE cliente_venta = ? AND status_venta = 1",[$id_cliente])[0];

        
        //SQLs para la crear una nueva instalacion tanto en la tabla soporte como en la table tipos_soportes//
        if ($check == 0) {
        $result = DB::select("SELECT id_equipo FROM equipos2 WHERE nombre_equipo = ? ",[$modelo]);
        $modeloEquipo = $result[0]->id_equipo;
        }
        $result2 = DB::insert("INSERT INTO instalaciones(cliente_insta,status_insta,tipo_insta,tasa_insta,user_insta,created_at,updated_at) VALUES (?,?,?,?,?,?,?)",[$id_cliente,1,$instalacion,$venta->tasa_venta,$id_usuario,$date,$date]);
        
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


  
          if($venta->promo_venta != 0){
              $agregarPromocionEnEspera = DB::insert("INSERT INTO promociones_en_espera(promocion_espera,cliente_promocion,status_espera,created_at,updated_at) VALUES (?,?,?,?,?)",[$venta->promo_venta,$id_cliente,1,$date,$date]);
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

        $actulizarStatusVenta = DB::update("UPDATE ventas SET status_venta = 2 WHERE id_venta = ? ",[$venta->id_venta]);
       
        historico_cliente::create(['history'=>'ip activada para su instalacion', 'modulo'=>'Soporte', 'cliente'=>$id_cliente, 'responsable'=>$id_usuario]);
        historico::create(['responsable'=>$id_usuario, 'modulo'=>'Soporte', 'detalle'=>'ip activa asignada para el cliente: '.$id_cliente]);
        
            
        return response()->json($request);

    }

    
}
