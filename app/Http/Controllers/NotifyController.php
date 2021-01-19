<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\historico;
use App\notify;
use App\User;
use App\servidores;
use App\celdas;
use App\ap;
use App\planes;
use App\cola_email_notify;
use Mail;
use App\historico_mensaje;
class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *modulo para enviar mensajes masivos segun router (detail 1)
     *segun celda (datail 2)
     *segun ap (detail 3)
     *y segun plan (detail 4)
     * @return \Illuminate\Http\Response
     */
    public function snotify(Request $request)
    {
        $listaDatos=[];
        $detail1 = [];
        
        if ($request->tipo == 1) {

            foreach ($request->datos as $cliente) {
        
                if($cliente["kind"]=='G'|| $cliente["kind"]=='J' || $cliente["kind"]=='V-'){
                    $cli= ucwords(strtolower($cliente["social"]));
                }else {
                    $cli= ucfirst($cliente["nombre"])." ".ucfirst($cliente["apellido"]);
                }
                
                $servicios= DB::select("SELECT * FROM servicios WHERE cliente_srv = ? AND (stat_srv = 1 OR stat_srv = 5)",[$cliente["id"]]);

                if(count($servicios) > 0){
                    array_push($listaDatos, $cliente);
                }

                
            }

          foreach ($listaDatos as $dato) {
                
                $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                    
                if (!$fp) {
                    //echo "ERROR: $errno - $errstr<br />\n";
                }
                else {
                    fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                    fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($dato["phone1"])." \"".urlencode($request->mensaje)."\" ".rand()." \r\n\r\n");
                    
                    fclose($fp);
                }

                historico_mensaje::create(['responsable'=>$request->responsable, 'modulo'=>'mensaje a deudores', 'detalle'=>$request->mensaje]);

          }  




        }
        
        



        /*
        $cli=[];
        $detail1=[];
        $tipo='';
        if ($request->tipo == 1){
            $tipo="Routers: ";
            foreach ($request->detail as $detail) {
                array_push($detail1, servidores::where('id_srvidor', $detail)->first()->nombre_srvidor);
                $result=DB::table('servicios')
                    ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
                    ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('celdas', 'aps.celda_ap','=','celdas.id_celda')
                    ->join('servidores', 'celdas.servidor_celda','=','servidores.id_srvidor')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->where('servidores.id_srvidor', '=', $detail)
                    ->get();
                foreach ($result as $cliente) {
                    $servicios=DB::table('servicios')
                        ->where('servicios.cliente_srv','=',$cliente->id)
                        ->where('servicios.stat_srv','!=','4')
                        ->where('servicios.stat_srv','!=','3')
                        ->get();
                    $cliente->servicios = $servicios->count();

                    if ($cliente->servicios > 0){
                        array_push($cli, $cliente);
                    }
                }

            }
            /*
        }elseif ($request->tipo == 2) {
            $tipo="Celdas: ";
            foreach ($request->detail as $detail) {
                array_push($detail1, celdas::where('id_celda', $detail)->first()->nombre_celda);
                $result=DB::table('servicios')
                    ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
                    ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('celdas', 'aps.celda_ap','=','celdas.id_celda')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->where('celdas.id_celda', '=', $detail)
                    ->get();
                foreach ($result as $cliente) {
                    $servicios=DB::table('servicios')
                        ->where('servicios.cliente_srv','=',$cliente->id)
                        ->where('servicios.stat_srv','!=','4')
                        ->where('servicios.stat_srv','!=','3')

                        ->get();
                    $cliente->servicios = $servicios->count();
                    if ($cliente->servicios > 0){
                        array_push($cli, $cliente);
                    }
                }
            }

        }elseif ($request->tipo == 3) {
            $tipo="APs: ";
            foreach ($request->detail as $detail) {
                array_push($detail1, ap::where('id', $detail)->first()->nombre_ap);
                $result=DB::table('servicios')
                    ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
                    ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->where('servicios.ap_srv', '=', $detail)
                    ->get();
                foreach ($result as $cliente) {
                    $servicios=DB::table('servicios')
                        ->where('servicios.cliente_srv','=',$cliente->id)
                        ->where('servicios.stat_srv','!=','4')
                        ->where('servicios.stat_srv','!=','3')
                        ->get();
                    $cliente->servicios = $servicios->count();
                    if ($cliente->servicios > 0){
                        array_push($cli, $cliente);
                    }
                }

            }
        }elseif ($request->tipo == 4) {
            $tipo="Planes: ";
            foreach ($request->detail as $detail) {
                array_push($detail1, planes::where('id_plan', $detail)->first()->name_plan);
                $result=DB::table('servicios')
                    ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.email', 'clientes.kind', 'clientes.social', 'planes.name_plan', 'planes.cost_plan', 'planes.taza')
                    ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                    ->join('clientes','clientes.id','=','servicios.cliente_srv')
                    ->join('planes','planes.id_plan','=','servicios.plan_srv')
                    ->join('aps','aps.id','=','servicios.ap_srv')
                    ->join('celdas', 'aps.celda_ap','=','celdas.id_celda')
                    ->join('servidores', 'celdas.servidor_celda','=','servidores.id_srvidor')
                    ->where('servidores.id_srvidor', '!=', '11')
                    ->where('servicios.plan_srv', '=', $detail)
                    ->get();
                foreach ($result as $cliente) {
                    $servicios=DB::table('servicios')
                        ->where('servicios.cliente_srv','=',$cliente->id)
                        ->where('servicios.stat_srv','!=','4')
                        ->where('servicios.stat_srv','!=','3')
                        ->get();
                    $cliente->servicios = $servicios->count();
                    if ($cliente->servicios > 0){
                        array_push($cli, $cliente);
                    }
                }

            }
        }elseif ($request->tipo == 5) {
            $tipo="Todos";
            $result=DB::table('servicios')
                ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
                ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                ->join('clientes','clientes.id','=','servicios.cliente_srv')
                ->get();
            foreach ($result as $cliente) {
                $servicios=DB::table('servicios')
                    ->where('servicios.cliente_srv','=',$cliente->id)
                   ->where('servicios.stat_srv','!=','4')
                     ->where('servicios.stat_srv','!=','3')
                    ->get();
                $cliente->servicios = $servicios->count();
                if ($cliente->servicios > 0){
                    array_push($cli, $cliente);
                }
            }

        }elseif ($request->tipo == 6) {
            $tipo="Cliente: ";
            foreach ($request->detail as $detail) {
                $result=DB::table('clientes')
                    ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
                    ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
                    ->where('clientes.id', '=', $detail)
                    ->get()->first();
                if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j' || strtolower($result->kind)=='v-')  &&(strtolower($result->social)!= 'null' && $result->kind != null)){
                    $cliente= ucwords(strtolower($result->social));
                }else {
                    $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                }
                array_push($detail1, $cliente);
                $servicios=DB::table('servicios')
                    ->where('servicios.cliente_srv','=',$result->id)
                    ->where('servicios.stat_srv','!=','4')
                    ->where('servicios.stat_srv','!=','3')

                    ->get();
                $result->servicios = $servicios->count();
                array_push($cli, $result);
                if ($result->servicios > 0){
                }
            }

        }
        $comment="mensaje a ".$tipo.implode(', ', $detail1)." con contenido ".$request->message." enviandose en total ".count($cli)." mensajes";
        $responsable=User::where('id_user', '=', $request->responsable)->first();
        $responsable=$responsable->id_user;
        if(count($cli)>0){
            historico::create(['responsable'=>$responsable, 'modulo'=>'Notificaciones', 'detalle'=>$comment]);
        }
        //return $cli;
        foreach ($cli as $user) {
            if($request->tipo==4){
                $hashes=array("#nombre_plan", "#valor_plan", "#valor_d_plan");
                $valores=array($user->name_plan, number_format($user->cost_plan, 2), number_format($user->taza, 2));
                $mensaje=str_replace($hashes, $valores, $request->message);
            }else{
                $mensaje=$request->message;
            }
            $cliente="";
            if((strtolower($user->kind)=='g'||strtolower($user->kind)=='j')&&(strtolower($user->social)!= 'null' && $user->kind != null)){
                $cliente=ucwords(strtolower($user->social));
                $message= "MARAVECA: Srs. ".$cliente.", ".$mensaje;
            }else {
                $cliente=ucfirst($user->nombre)." ".ucfirst($user->apellido);
                $message= "MARAVECA: Sr(a) ".$cliente.", ".$mensaje;
            }
            if($request->via == 0){
                // //Asignamos variables
                // $PhoneNumber = ($user->phone1);
                // $text = $message;
                // $user1 = "maraveca";
                // $password = "PL5OJK";
                //
                //
                // //Se crea un manejador CURL para realizar la petición
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, "http://www.interconectados.net/api2/?PhoneNumber=".$PhoneNumber."&text=&user=".$user1."&password=".$password." ");
                // curl_exec($ch);
                // curl_close($ch);
                //
                // return $ch;
                //
                // //Se crea un manejador CURL para realizar la petición
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, "mensajesms.com.ve/sms2/API/api.php?cel=04122398291&men=Saludos_prueba_de_salida_de_SMS_de MensajeSMS.com.ve&u=demo&t=D3M04P1");
                // curl_exec($ch);
                // curl_close($ch);

                //return $ch;
                // return response()->json($request);

                //echo $message;
                sendsms($user->phone1, $message);
                //sendsms('04122398291', $message);




            }elseif ($request->via == 1){
                cola_email_notify::create(['id_cli'=>$user->id, 'mensaje'=>$mensaje, 'tipo'=>$request->tipo, 'via'=>$request->via , 'contador'=>0]);
                Mail::send('emails.Notify', [
                    'cliente' => $cliente,
                    'fecha'=>date('d-m-Y'),
                    'mensaje'=>$mensaje,
                    'moroso'=>$user],function ($message) use ($cliente, $user)
                {
                  //  $message->subject('Notificacion-'.$cliente.'('.date('d-m-Y').')');
                    $message->subject('INFORMACION IMPORTANTE');
                    $message->from('no-responder@maraveca.com', 'Maraveca Telecomunicaciones(Mail Automatico)');
                    $message->to($user->email);
                    $message->bcc('hector.diaz@maraveca.com');
                    // $message->to("ana.reyes@maraveca.com");
                    //  $message->to('jesus.orono@maraveca.com');

                });
            }elseif ($request->via == 2){
                cola_email_notify::create(['id_cli'=>$user->id, 'mensaje'=>$mensaje, 'tipo'=>$request->tipo, 'via'=>$request->via,'contador'=>0]);
                Mail::send('emails.Navidad', [
                    'cliente' => $cliente,
                    'fecha'=>date('d-m-Y'),
                    'mensaje'=>$mensaje,
                    'moroso'=>$user],function ($message) use ($cliente, $user)
                {
                  //  $message->subject('Notificacion-'.$cliente.'('.date('d-m-Y').')');
                    $message->subject('¡Felices Fiestas!');
                    $message->from('no-responder@maraveca.com', 'Maraveca Telecomunicaciones(Mail Automatico)');
                    $message->to($user->email);
                    $message->bcc('no-responder@maraveca.com');
                    //$message->bcc('hector.diaz@maraveca.com');
                    // $message->to("ana.reyes@maraveca.com");
                    //  $message->to('jesus.orono@maraveca.com');


                });
            }
        }
        //return ['number'=>count($cli)];
        */
        return response()->json($listaDatos);
    }

   
    public function mensajes_morosos()
    {
        $msj_moroso = notify::where('tipo_sms','=', 3)->get();

        return $msj_moroso;
    }

    public function env_msj_morosos(Request $request)
    {
        
        historico::create(['responsable'=>$request->responsable, 'modulo'=>'Notificacion', 'detalle'=>'Envio de mensajes masivos a clientes que presentan deuda:'.$request->mensaje]);
        //notify::where('tipo_sms','=', $request->tipo_sms)->update(['mensaje' => $request->mensaje]);

        \Artisan::call('MensajesAMorososFront', [
            'mensaje' => $request->mensaje, 'Responsable'=>$request->responsable
        ]);
        
        return response()->json($request);
    }


    public function traerHistoricoMensaje(){

        $historicos = DB::select("SELECT h.*,COUNT(id) as cantidad FROM historico_mensajes AS h
                                        GROUP BY DAY(created_at), MONTH(created_at), YEAR(created_at)  
                                            ORDER BY id  DESC");

        return response()->json($historicos);
    }

}
