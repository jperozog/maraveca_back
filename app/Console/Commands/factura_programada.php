<?php

namespace App\Console\Commands;
use App\fac_product;
use App\fac_prog;
use App\historico_cliente;
use Illuminate\Console\Command;
use DB;
use App\configuracion;
use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_pago;

use App\fac_adic;
use \Carbon\Carbon;
use function App\Http\Controllers\revisar_Balances;
use function App\Http\Controllers\revisar_pagado;

class factura_programada extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura_programadas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'corre solo una facturacion a un cliente determinado de forma programada';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fac_progs=DB::table('fac_progs')
            ->where('status','=', '1')
            ->get();

        foreach ($fac_progs as $fac_prog){

            $fecha_prog = $fac_prog->fecha;
            $today = \Carbon\Carbon::now();
            $fecha = $today->toDateString();


            if($fecha_prog == $fecha )
            {
                fac_prog::where('id', $fac_prog->id)->update(['contador'=>($fac_prog->contador+1)]);

                //$fecha = date( "Y-m-d", strtotime( $this->argument('fecha') ) );;
                $pro = 1;
                $start = new Carbon('first day of this month');
                $end = new Carbon('first day of next month');
                $lastDayOfThisMonth = new Carbon('last day of this month');
                $totalofdays=$end->diff($start)->format('%a');
                $nbOfDaysRemainingThisMonth =  $lastDayOfThisMonth->diff($today)->format('%a');
                $pastdays = $totalofdays - $nbOfDaysRemainingThisMonth;

                echo $fecha. "\n";
                echo $fecha_prog. "\n";
                echo $nbOfDaysRemainingThisMonth. "\n";
                echo $totalofdays. "\n";
                $array = $fac_prog->id_servicio;
                $servi_cli = json_decode($array);
                /*// $json_array = base64_decode($servi_cli);
                 $servi_cli2 = json_encode($servi_cli, true);
                 echo  $servi_cli2. "\n";*/
                //echo  $servi_cli. "\n";
                $clientes=DB::table('clientes')
                    ->where('id', '=', $fac_prog->id_cliente)
                    ->get();
                foreach ($clientes as $cliente) { //para cada cliente
                    if(is_array($servi_cli)) {
                        $servicios = DB::table('servicios')//buscamos los servicios
                        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                            ->where('servicios.cliente_srv', '=', $cliente->id)
                            ->where('servicios.stat_srv', '!=', '4')
                            ->whereIn('servicios.id_srv', $servi_cli)
                            ->get();
                    }else{
                        $servicios = DB::table('servicios')//buscamos los servicios
                        ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                            ->where('servicios.cliente_srv', '=', $cliente->id)
                            ->where('servicios.stat_srv', '!=', '4')
                            ->where('servicios.id_srv', $servi_cli)
                            ->get();

                    }
                    $tasa=configuracion::where('nombre','=','taza')->first()->valor;
                    $adicionales=fac_adic::where('id_cli', $cliente->id)->where('id_fac', null)->get();
                    if ($servicios->count()  >= 1 || $adicionales->count()>=1) { //en caso de que exista al menos un servicio
                        $iva=configuracion::where('nombre','=','iva')->first();
                        $iva1=($iva->valor+100)/100;
                        foreach ($servicios as $servicio) {
                            if ($servicio->tipo_plan_srv !=3 && $servicio->modo_pago_srv !==2) {
                                $denominacion = configuracion::where('nombre', '=', 'denominacion')->first();
                            } else {
                                $denominacion = configuracion::where('nombre', '=', 'denominacion_in')->first();
                            }
                        }
                        if($servicio->serie_srv == 1){
                            $tmp=configuracion::where('nombre','=','facturacion');
                            $numero=$tmp->first();
                            $numero=$numero->valor+1;
                            $tmp->update(["valor"=>$numero]);
                            $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                                [
                                    'id_cliente'=>$cliente->id,
                                    'fac_num'=>"SerieB-".str_pad($numero, 4, '0', STR_PAD_LEFT),
                                    'fac_status'=>'1',
                                    'denominacion'=>$denominacion->valor,
                                    'serie_fac' =>'1' ,
                                    'fac_serv' =>$servicio->id_srv,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ]
                            );
                        }else{
                            $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                                [
                                    'id_cliente'=>$cliente->id,
                                    'fac_status'=>'1',
                                    'denominacion'=> '$',  /*$denominacion->valor,*/ //se agrega como denominacion general $ a todos los que no sean facturable 27/01/2020
                                    'serie_fac' =>'0' ,
                                    'fac_serv' =>$servicio->id_srv,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ]
                            );
                        }
                        if ($cliente->kind == 'V' || $cliente->kind == 'E' || $cliente->kind == 'v' || $cliente->kind == 'e') {
                            DB::table('fac_dets')->insert([
                                [
                                    'fac_id'=>$id,
                                    'detail'=>'cliente',
                                    'value'=>$cliente->nombre.' '.$cliente->apellido,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],
                                [
                                    'fac_id'=>$id,
                                    'detail'=>'dni',
                                    'value'=>$cliente->kind.$cliente->dni,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'email',
                                    'value'=>$cliente->email,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'phone',
                                    'value'=>$cliente->phone1,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'address',
                                    'value'=>$cliente->direccion,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ]
                            ]);
                        } elseif ($cliente->kind == 'j' || $cliente->kind == 'J' || $cliente->kind == 'g' || $cliente->kind == 'G'){
                            DB::table('fac_dets')->insert([
                                [
                                    'fac_id'=>$id,
                                    'detail'=>'cliente',
                                    'value'=>$cliente->social,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],
                                [
                                    'fac_id'=>$id,
                                    'detail'=>'dni',
                                    'value'=>$cliente->kind.$cliente->dni,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'email',
                                    'value'=>$cliente->email,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'phone',
                                    'value'=>$cliente->phone1,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ],[
                                    'fac_id'=>$id,
                                    'detail'=>'address',
                                    'value'=>$cliente->direccion,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                ]
                            ]);
                        }

                        if($servicios->count()  >= 1 ){
                            foreach ($servicios as $key) {
                                if($key->serie_srv == 1 ){
                                    if($pro == 1){

                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1, 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                "precio_dl" =>round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1)/$tasa, 2),
                                                "precio_bs" => round(((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),
                                            ]
                                        );
                                    }else{

                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round($key->cost_plan*$iva1, 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                "precio_dl" =>round(($key->cost_plan*$iva1)/$tasa, 2),
                                                'precio_bs' =>round((($key->cost_plan )/$tasa),2),
                                            ]
                                        );
                                    }
                                } elseif ($key->serie_srv  == 0 && $key->tipo_plan_srv != 4 && $key->tipo_plan_srv != 5 && $key->tipo_plan_srv != 3) {
                                    if($pro==1){
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_dl'=>round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)/$tasa), 2),
                                                "precio_bs" => round(((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),
                                            ]
                                        );
                                    }else{
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>$key->taza,
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_dl'=>round((($key->cost_plan)/$tasa), 2),
                                                'precio_bs' =>round((($key->cost_plan )/$tasa),2),
                                            ]
                                        );
                                    }
                                }
                                elseif ($key->serie_srv  == 0 && ($key->tipo_plan_srv == 4 || $key->tipo_plan_srv == 5) && $key->modo_pago_srv== 1) {
                                    if($pro==1){
                                 /*       if($key->carac_plan== 2){
                                            $precio=  round((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth), 2);
                                        } else {

                                            $precio= round(((($key->cost_plan+ (2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth), 2);
                                        }*/
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_dl'=>round((((($key->cost_plan)/$totalofdays)*$nbOfDaysRemainingThisMonth)/$tasa), 2),
                                                "precio_bs" => round(((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),
                                            ]
                                        );
                                    }else{
                                 /*       if($key->carac_plan== 2){
                                            $precio=  ($key->cost_plan);
                                        } else {

                                            $precio= round(($key->cost_plan + (2*$tasa)),2);
                                        }*/
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>$key->taza,
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_dl'=>round((($key->cost_plan )/$tasa),2),
                                                'precio_bs' =>round((($key->cost_plan )/$tasa),2),

                                            ]
                                        );
                                    }
                                }elseif ($key->serie_srv  == 0 && $key->tipo_plan_srv == 3) {
                                    if($pro==1){
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                "precio_bs" => round(((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),
                                                "precio_dl" =>round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1)/$tasa, 2),

                                            ]
                                        );
                                    }else{
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>$key->taza,
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_bs' =>round((($key->cost_plan )/$tasa),2),
                                                'precio_dl'=>round((($key->cost_plan )/$tasa),2),
                                            ]
                                        );
                                    }
                                }
                                elseif ($key->serie_srv  == 0 && ($key->tipo_plan_srv == 4 || $key->tipo_plan_srv == 5) && $key->modo_pago_srv== 2) {
                                    if($pro==1){
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>round((($key->taza/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                "precio_bs" => round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$tasa), 2),
                                                "precio_dl" =>round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1)/$tasa, 2),

                                            ]
                                        );
                                    }else{
                                        DB::table('fac_products')->insert(
                                            [
                                                'codigo_factura'=>$id,
                                                'codigo_articulo'=>$key->id_plan,
                                                'nombre_articulo'=>$key->name_plan,
                                                'precio_articulo'=>$key->taza ,
                                                'IVA'=>$iva->valor,
                                                'comment_articulo'=>$key->comment_srv,
                                                "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                                "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                                'precio_bs' =>round(($key->cost_plan*$tasa),2),
                                                'precio_dl'=>round((($key->cost_plan )/$tasa),2),
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                        if($adicionales->count()  >= 1 ){
                            foreach ($adicionales as $key) {
                                if($cliente->serie == 1){
                                    DB::table('fac_products')->insert(
                                        [
                                            'codigo_factura'=>$id,
                                            'codigo_articulo'=>$key->codigo_articulo,
                                            'nombre_articulo'=>$key->nombre_articulo,
                                            'precio_unitario'=>$key->precio_unitario,
                                            'IVA'=>$key->IVA,
                                            'cantidad'=>$key->cantidad,
                                            'precio_articulo'=>round($key->precio_articulo*$iva1, 2),
                                            'comment_articulo'=>$key->comment_articulo,
                                            "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                                        ]
                                    );
                                }elseif ($cliente->serie == 0) {
                                    DB::table('fac_products')->insert(
                                        [
                                            'codigo_factura'=>$id,
                                            'codigo_articulo'=>$key->codigo_articulo,
                                            'nombre_articulo'=>$key->nombre_articulo,
                                            'precio_unitario'=>$key->precio_unitario,
                                            'IVA'=>$key->IVA,
                                            'cantidad'=>$key->cantidad,
                                            'precio_articulo'=>$key->precio_articulo,
                                            'comment_articulo'=>$key->comment_articulo,
                                            "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                                        ]
                                    );

                                }
                                fac_adic::where('id', $key->id)->update(['id_fac'=>$id]);
                            }
                        }

                        historico_cliente::create(['history'=>'Generacion de factura programada Nº '.$id, 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>'0']);
                        fac_prog::where('id', $fac_prog->id)->update(['status'=>2]);
                        /*===================================Mensajes de facturacion al clientes========================================================*/
                       $fac= fac_product::where('codigo_factura', '=', $id)->get();

                           if($fac->count()>1){
                               $monto_fac=0;
                               foreach ($fac as $fac_prod ){

                               $monto_fac+= $fac_prod->precio_articulo;
                               }
                           echo $monto_fac."\n\n\n";
                           } elseif ($fac->count()<=1){
                               foreach ($fac as $fac_prod ){
                          $monto_fac= $fac_prod->precio_articulo;

                           }
                           }
                        echo $monto_fac."\n\n\n";
                           $input  = $fac_prod->created_at;
                           $hoy= date_format($input, 'd/m/y');
                        if ($cliente->tipo_planes == 2 ||$cliente->tipo_planes == 5 ) {
                            $txt = 'se ha generado una nueva factura correspondiente a la fecha '.$hoy.', por un monto de: ' . number_format($monto_fac, 2) .' US$, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana';//cambio del formato del link de mi pago a mi ventana 07/06/2019,

                        } else{
                            $txt = $txt = 'se ha generado una nueva factura correspondiente a la fecha '.$hoy.', por un monto de: ' . number_format($monto_fac, 2) .' Bs.S, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana';//cambio del formato del link de mi pago a mi ventana 07/06/2019,

                        }
                        //$txt="se ha generado una nueva factura correspondiente al mes en curso, su saldo actual es de: ".number_format($cliente->deuda, 2)." Bs.S, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana";//cambio del formato del link de mi pago a mi ventana 07/06/2019,
                        //$txt="le informamos que su servicio se encuentra suspendido. Actualmente presenta un saldo vencido en su facturación DE ".number_format($cliente->deuda, 2)." Bs.S";
                        //$txt="recuerde que este es un sistema de mensajeria masiva no monitoreada. para cualquier informacion comuniquese a traves de: maraveca.com info@maraveca.com o al master 02617725180 ó 02687755100";
                        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
                            $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$txt;
                        }else {
                            $message= "MARAVECA: Sr.(a) ".ucwords(strtolower($cliente->apellido)).", ".$txt;
                        }
                        //echo $message."\n";
                        //sendsms($cliente->phone1, $message);
                        revisar_Balances($cliente->id);

                           $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);

                                if (!$fp) {
                                    echo "ERROR: $errno - $errstr<br />\n";
                                }
                                else {
                                    fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
                                    fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($cliente->phone1)." \"".urlencode($message)."\" ".rand()." \r\n\r\n");
                                    //while (!feof($fp)) 	echo fgets($fp, 1024);
                                    //echo fread($fp, 4096);
                                    fclose($fp);
                                    echo $cliente->phone1." \ ".$message."\n";
                                }
                           echo $cliente->phone1." \ ".$message."\n\n\n";




                        /*===============================================================================================================================*/









                }
            }
        }
    }
}
}
