<?php

namespace App\Console\Commands;

use App\historico_cliente;
use Illuminate\Console\Command;
use DB;
use App\configuracion;
use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_pago;
use App\fac_adic;
use \Carbon\Carbon;
use function App\Http\Controllers\revisar_pagado;

class factura_blanco extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura_blanco:generar
  {cliente : cliente para generar 1 factura}
  {fecha?}
  {pro?}
  {responsable?}
  {denominacion?}
  {serie?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'corre solo una facturacion a un cliente determinado';

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
        if($this->argument('fecha')){
            $today = Carbon::createFromFormat('d/m/Y', $this->argument('fecha') , 'America/Caracas');
            $fecha = $today;
        }else{
            $today = \Carbon\Carbon::now();
            $fecha = $today;
        }
        //$fecha = date( "Y-m-d", strtotime( $this->argument('fecha') ) );;
        $pro = $this->argument('pro');
        $start = new Carbon('first day of this month');
        $end = new Carbon('first day of next month');
        $lastDayOfThisMonth = new Carbon('last day of this month');
        $totalofdays=$end->diff($start)->format('%a');
        $nbOfDaysRemainingThisMonth =  $lastDayOfThisMonth->diff($today)->format('%a');
        $pastdays = $totalofdays - $nbOfDaysRemainingThisMonth;
        $responsable= $this->argument('responsable');
        echo $fecha;
      //  $servi_cli = $this->argument('nro_servicio');
        $clientes=DB::table('clientes')
            ->where('id', '=', $this->argument('cliente'))
            ->get();
        foreach ($clientes as $cliente) { //para cada cliente

                $servicios = DB::table('servicios')//buscamos los servicios
                ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
                    ->where('servicios.cliente_srv', '=', $cliente->id)
                    ->where('servicios.stat_srv', '!=', '4')
                   // ->whereIn('servicios.id_srv', $servi_cli)
                    ->get();

            $tasa=configuracion::where('nombre','=','taza')->first()->valor;
            $adicionales=fac_adic::where('id_cli', $cliente->id)->where('id_fac', null)->get();
            if ($servicios->count()  >= 1 || $servicios->count()  >= 0|| $adicionales->count()>=1) { //en caso de que exista al menos un servicio
                $iva=configuracion::where('nombre','=','iva')->first();
                $iva1=($iva->valor+100)/100;
               // foreach ($servicios as $servicio) {
                    if ($this->argument('denominacion')=="1") {
                        $denominacion = 'Bs.S';
                    } elseif ($this->argument('denominacion')=="2"){
                        $denominacion = '$';
                    }
               // }
                if($this->argument('serie') == 1){
                    $tmp=configuracion::where('nombre','=','facturacion');
                    $numero=$tmp->first();
                    $numero=$numero->valor+1;
                    $tmp->update(["valor"=>$numero]);
                    $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                        [
                            'id_cliente'=>$cliente->id,
                            'fac_num'=>"SerieB-".str_pad($numero, 4, '0', STR_PAD_LEFT),
                            'fac_status'=>'3',
                            'denominacion'=>$denominacion,
                            'serie_fac' =>'1' ,
                            "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                        ]
                    );
                }else{
                    $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                        [
                            'id_cliente'=>$cliente->id,
                            'fac_status'=>'3',
                            'denominacion'=>'$',  /*$denominacion->valor,*/ //se agrega como denominacion general $ a todos los que no sean facturable 27/01/2020
                            'serie_fac' =>'0' ,
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

               /* if($servicios->count()  >= 1 ){
                    foreach ($servicios as $key) {
                        if($key->serie_srv == 1 && $key->tipo_plan_srv != 4){
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

                                    ]
                                );
                            }
                        }elseif ($key->serie_srv == 1  && $key->tipo_plan_srv == 4 && $key->modo_pago_srv== 1){
                            if($pro == 1){

                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>round(((($key->cost_plan+ (2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth) *$iva1, 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        "precio_dl" =>round((((($key->cost_plan+(2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth)*$iva1)/$tasa, 2),

                                    ]
                                );
                            }else{

                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>round(($key->cost_plan+ (2*$tasa))*$iva1, 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        "precio_dl" =>round((($key->cost_plan+ (2*$tasa))*$iva1)/$tasa, 2),
                                    ]
                                );
                            }
                        } elseif ($key->serie_srv  == 0 && $key->tipo_plan_srv != 4 && $key->tipo_plan_srv != 3) {
                            if($pro==1){
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>round((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl'=>round(((($key->cost_plan/$totalofdays)*$nbOfDaysRemainingThisMonth)/$tasa), 2),

                                    ]
                                );
                            }else{
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>($key->cost_plan),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl'=>round((($key->cost_plan)/$tasa), 2),

                                    ]
                                );
                            }
                        }
                        elseif ($key->serie_srv  == 0 && $key->tipo_plan_srv == 4 && $key->modo_pago_srv== 1) {
                            if($pro==1){
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>round(((($key->cost_plan+ (2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth), 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl'=>round((((($key->cost_plan+ (2*$tasa))/$totalofdays)*$nbOfDaysRemainingThisMonth)/$tasa), 2),

                                    ]
                                );
                            }else{
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>round(($key->cost_plan + (2*$tasa)),2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl'=>round((($key->cost_plan + (2*$tasa))/$tasa),2),


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

                                    ]
                                );
                            }else{
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>($key->taza),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_bs' =>round(($key->taza *$tasa),2),

                                    ]
                                );
                            }
                        }
                        elseif ($key->serie_srv  == 0 && $key->tipo_plan_srv == 4 && $key->modo_pago_srv== 2) {
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

                                    ]
                                );
                            }else{
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$key->id_plan,
                                        'nombre_articulo'=>$key->name_plan,
                                        'precio_articulo'=>($key->taza ),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$key->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_bs' =>round(($key->taza *$tasa),2),

                                    ]
                                );
                            }
                        }
                    }
                }*/
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
                historico_cliente::create(['history'=>'Generacion de factura Nº '.$id, 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>$responsable]);

                $facturacion=DB::select("SELECT fac_controls.*,
          (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
          (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
          where fac_controls.id_cliente = $cliente->id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas del cliente
                foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
                    if ($factura->denominacion == '$'){
                        $balance=balance_clientes_in::where('bal_cli_in', '=', $cliente->id)->where('bal_stat_in', 1)->where('bal_rest_in', '>', 0)->get(); // para los balances en facturas en dolares
                    }else{
                        $balance=balance_cliente::where('bal_cli', '=', $cliente->id)->where('bal_stat', 1)->where('bal_rest', '>', 0)->get();

                    }
                    foreach ($balance as $restante1) {
                        /*=====================================================================En caso de los balances en moneda nacional==============================================================*/
                        if ($factura->denominacion != '$') { // para calcular el balance de facturas en bs
                            if ($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11) {
                                //return +$restante1->bal_rest * $tasa;
                                $restante = (+$restante1->bal_rest * $tasa);
                                //echo $restante;
                            } else {

                                $restante = $restante1->bal_rest;
                            }


                        }
                        /*===================================================================================================================================*/

                        /*=====================================================================En caso de los balances en moneda internacional==============================================================*/
                  if ($factura->denominacion == '$') { // para calcular el balance de facturas en dolares
                            if ($restante1->bal_tip_in == 12 || $restante1->bal_tip_in == 13 || $restante1->bal_tip_in == 14 || $restante1->bal_tip_in == 16) {

                                $restante = $restante1->bal_rest_in;

                            } elseif (($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16)&&$restante1->uso_bal_in ==1  ) {

                                $restante = $restante1->bal_rest_in;

                            }
                            else {
                                $restante = ((float)$restante1->bal_rest_in / (float)$restante1->tasa);

                            }
                        }
                        /*===================================================================================================================================*/


                        //echo $factura->denominacion;
                        if($restante>0){
                            if($factura->denominacion == 'BSF'){
                                $restante=round(+$restante*100000, 2);
                                $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                                if($factura->monto>$factura->pagado){//si no esta solvente
                                    if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                                        echo 'queda 0 \n';
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                                        $factura->pagado=+$factura->pagado+$restante;
                                        $restante=0;
                                        revisar_pagado($factura->id);
                                    }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                                        echo 'pagable';
                                        $restante=+$restante-$deuda;//calculo lo que quedara
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                                        $factura->monto=0;
                                        revisar_pagado($factura->id);

                                    }
                                    if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                                        $restante == round(+$restante / $tasa, 2);
                                    }
                                    $restante=round(+$restante/100000, 2);
                                    $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                                    $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                                }

                            }elseif($factura->denominacion == 'Bs.S'){
                                $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                                if($factura->monto>$factura->pagado){//si no esta solvente
                                    if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//coloco todo el monto en un pago
                                        $factura->pagado=+$factura->pagado+$restante;
                                        $restante=0;
                                    }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                                       $restante=round(($restante-$deuda),2);//calculo lo que quedara
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                                        $factura->monto=0;
                                        revisar_pagado($factura->id);
                                    }
                                    if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                                        //echo $restante;
                                        $restante = round((+$restante / $tasa), 2);
                                        echo $restante;
                                        revisar_pagado($factura->id);
                                    }

                                    $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                                    $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                                }

                            }elseif($factura->denominacion == '$'){
                                $deuda=round($factura->monto-$factura->pagado, 2);//calculo su deuda
                                if($factura->monto>$factura->pagado){//si no esta solvente
                                    if($deuda>=$restante){//si la deuda es mayor o igual que el resto
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip_in, 'status'=>'1', 'pag_monto'=>$restante, 'pag_comment'=>$restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//coloco todo el monto en un pago
                                        $factura->pagado=+$factura->pagado+$restante;
                                        $restante=0;
                                        revisar_pagado($factura->id);
                                    }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                                       $restante=round(($restante-$deuda),2);//calculo lo que quedara
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip_in, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//registro el pago con el monto de la deuda
                                        $factura->monto=0;
                                        revisar_pagado($factura->id);
                                    }
                          /*          if ($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16 ){
                                        $restante = (+$restante * (float)$restante1->tasa);
                                        echo $restante;

                                    }*/
                                    $up = balance_clientes_in::where('id_bal_in','=', $restante1->id_bal_in);
                                    $up->update(['bal_rest_in'=>$restante, 'uso_bal_in' => 1]);//acualizo lo que quedo
                                }
                            }



                        }
                    }
                }
                # code...

            }
        }
    }
}
