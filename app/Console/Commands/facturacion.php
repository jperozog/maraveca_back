<?php

namespace App\Console\Commands;

use App\balance_clientes_in;
use DB;
use App\historico;
use App\historico_cliente;
use App\servicios;
use Illuminate\Console\Command;
use App\balance_cliente;
use App\fac_adic;
use App\fac_pago;
use App\configuracion;
use function App\Http\Controllers\revisar_pagado;

class facturacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturacion:correr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corre La Facturacion Cada Mes';

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

        $iva=configuracion::where('nombre','=','iva')->first();
        $iva1=(+$iva->valor+100)/100;
        $denominacion=configuracion::where('nombre','=','denominacion')->first();
        $clientes=DB::table('clientes')
        ->orderBy('id', 'ASC')->get();
        $i=0;
        $tmp=configuracion::where('nombre','=','facturacion');
        $numero=$tmp->first()->valor; //2906
        foreach ($clientes as $cliente) {
            
            $promo =DB::table ('fac_promo')
            ->where ('fac_promo.id_cliente_p', '=',$cliente->id)
            ->where(	'fac_promo.status', '=','1')
            ->get();

            if($promo->count()<1){



                 //para cada cliente
            $prog=DB::table ('fac_progs')
            ->where ('fac_progs.id_cliente', '=',$cliente->id)
            ->where(	'fac_progs.status', '=','1')
            ->get();
        if ($prog->count()<1){// si existe algun cliente con factura programa no se le generara factura a traves de este proceso

            $servicios=DB::table('servicios')  //buscamos los servicios
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
                ->where('servicios.cliente_srv','=',$cliente->id)
                ->where('servicios.stat_srv','!=','4')
                ->where('servicios.stat_srv','!=','5')
                ->where('servicios.stat_srv','!=','3')
                ->where('servicios.serie_srv','!=','1')//para no generar facturas fiscales por ordenes de la gerencia
                ->get();
            $tasa=configuracion::where('nombre','=','taza')->first()->valor;
            $adicionales=fac_adic::where('id_cli', $cliente->id)->where('id_fac', null)->get();


            if ($servicios->count()  >= 1 || $adicionales->count()>=1) { //en caso de que exista al menos un servicio
                $debe=0; //variable para contador
                $facturas=DB::select(
                    "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
      from fac_controls where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1");//selecciono todas las facturas
                foreach ($facturas as $factura) {
                    if($factura->monto > $factura->pagado){ //reviso cuales estan pagadas
                        $debe+=1;
                    }
                }
                //3260 365
                if ($debe <= 1) { //si debe 1 o menos, correra la facturacion

                    foreach($servicios as $servicio) {
                        $i++;
                        if ($servicio->tipo_plan_srv != 3 && $servicio->modo_pago_srv !==2) {
                            $denominacion = configuracion::where('nombre', '=', 'denominacion')->first();
                        } else {
                            $denominacion = configuracion::where('nombre', '=', 'denominacion_in')->first();
                        }

                        //if (true) { //siempre
                        /*if($servicio->serie_srv == 1){ //si es facturable
                            $numero=$numero+1;
                            $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                                [
                                    'id_cliente'=>$cliente->id,
                                    'fac_num'=>"SerieB-".str_pad($numero, 4, '0', STR_PAD_LEFT),
                                    'fac_status'=>'1',
                                    'denominacion'=>$denominacion->valor,
                                    'serie_fac' =>'1',
                                    'fac_serv' =>$servicio->id_srv,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                            );
                        }
                        else*/
                        if($servicio->serie_srv == 0){ //si no es facturable
                            $id=DB::table('fac_controls')->insertGetId( //generamos la factura
                                [
                                    'id_cliente'=>$cliente->id,
                                    'fac_status'=>'1',
                                    'denominacion'=>'$',  /*$denominacion->valor,*/ //se agrega como denominacion general $ a todos los que no sean facturable 27/01/2020
                                    'serie_fac' =>'0',
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
                        } elseif ($cliente->kind == 'j' || $cliente->kind == 'J' || $cliente->kind == 'g' || $cliente->kind == 'G' || $cliente->kind == 'v-' || $cliente->kind == 'V-'){
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
                        if($servicios->count()>=1  ){
                            // foreach ($servicios as $servicio) {
                            /*if($servicio->serie_srv == 1 && $servicio->tipo_plan_srv != 4){
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$servicio->id_plan,
                                        'nombre_articulo'=>$servicio->name_plan,
                                        'precio_articulo'=>round($servicio->cost_plan*$iva1, 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$servicio->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                             "precio_dl" =>round(($servicio->cost_plan*$iva1)/$tasa, 2),

                                    ]
                                );
                            }


                            elseif ($servicio->serie_srv == 1 && ($servicio->tipo_plan_srv == 4 ||$servicio->tipo_plan_srv == 5) && $servicio->modo_pago_srv== 1  ){

                              if($servicio->carac_plan== 2){
                       $precio=  round($servicio->cost_plan*$iva1, 2);
                    } else {

                        $precio= round(($servicio->cost_plan+($tasa*2))*$iva1, 2);
                    }
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$servicio->id_plan,
                                        'nombre_articulo'=>$servicio->name_plan,
                                        'precio_articulo'=> $precio,
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$servicio->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                            "precio_dl" =>round((($servicio->cost_plan+ (2*$tasa))*$iva1)/$tasa, 2),

                                    ]
                                );
                            }*/

                            //elseif
                            if($servicio->serie_srv == 0  && $servicio->tipo_plan_srv !== 4 && $servicio->tipo_plan_srv !== 5 && $servicio->tipo_plan_srv !== 3) {
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura' => $id,
                                        'codigo_articulo' => $servicio->id_plan,
                                        'nombre_articulo' => $servicio->name_plan,
                                        'precio_articulo' => round($servicio->taza, 2),
                                        'IVA' => $iva->valor,
                                        'comment_articulo' => $servicio->comment_srv,
                                        "created_at" => \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl' => round((($servicio->cost_plan) / $tasa), 2),
                                        'precio_bs' =>round(($servicio->taza *$tasa),2),

                                    ]
                                );

                            }  elseif ($servicio->serie_srv == 0  && ($servicio->tipo_plan_srv == 4 ||$servicio->tipo_plan_srv == 5)&& $servicio->modo_pago_srv == 2 ) {
                                    DB::table('fac_products')->insert(
                                        [
                                            'codigo_factura'=>$id,
                                            'codigo_articulo'=>$servicio->id_plan,
                                            'nombre_articulo'=>$servicio->name_plan,
                                            'precio_articulo'=>round($servicio->taza, 2),
                                            'IVA'=>$iva->valor,
                                            'comment_articulo'=>$servicio->comment_srv,
                                            "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                            "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                            'precio_dl'=>round((($servicio->cost_plan)/$tasa), 2),
                                            'precio_bs' =>round(($servicio->taza *$tasa),2),
                                        ]
                                    );

                            }
                            elseif ($servicio->serie_srv  == 0 && ($servicio->tipo_plan_srv == 4 ||$servicio->tipo_plan_srv == 5) && $servicio->modo_pago_srv== 1) {
                                /*if($servicio->carac_plan== 2){
                                    $precio=  round($servicio->cost_plan, 2);
                                } else {

                                    $precio= round($servicio->cost_plan+($tasa*2));
                                }*/
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$servicio->id_plan,
                                        'nombre_articulo'=>$servicio->name_plan,
                                        'precio_articulo'=> $servicio->taza,
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$servicio->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_dl'=>round((($servicio->cost_plan)/$tasa),2),
                                        'precio_bs' =>round(($servicio->taza *$tasa),2),
                                    ]
                                );

                            }elseif ( $servicio->tipo_plan_srv == 3 || $servicio->modo_pago_srv == 2 ) {
                                DB::table('fac_products')->insert(
                                    [
                                        'codigo_factura'=>$id,
                                        'codigo_articulo'=>$servicio->id_plan,
                                        'nombre_articulo'=>$servicio->name_plan,
                                        'precio_articulo'=>round($servicio->taza, 2),
                                        'IVA'=>$iva->valor,
                                        'comment_articulo'=>$servicio->comment_srv,
                                        "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                        "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                                        'precio_bs' =>round(($servicio->taza *$tasa),2),
                                        'precio_dl'=>round((($servicio->cost_plan)/$tasa),2),
                                    ]
                                );

                            }
                        }
                        echo 'Generacion de factura Nº '.$id.' para el servicio Nº '.$servicio->id_srv.' del cliente '.$cliente->nombre.' '.$cliente->apellido. "\n\n";
                        historico::create(['responsable'=>'0', 'modulo'=>'Facturacion', 'detalle'=>'Generacion de nueva factura programada para el cliente '.$cliente->id]);

                        historico_cliente::create(['history'=>'Generacion de factura Nº '.$id, 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>'0']);
                    }
                }

                if($adicionales->count()  >= 1 ){
                    foreach ($adicionales as $servicio) {
                        if($cliente->serie == 1){
                            DB::table('fac_products')->insert(
                                [
                                    'codigo_factura'=>$id,
                                    'codigo_articulo'=>$servicio->codigo_articulo,
                                    'nombre_articulo'=>$servicio->nombre_articulo,
                                    'precio_unitario'=>$servicio->precio_unitario,
                                    'IVA'=>$servicio->IVA,
                                    'cantidad'=>$servicio->cantidad,
                                    'precio_articulo'=>round($servicio->precio_articulo*$iva1, 2),
                                    'comment_articulo'=>$servicio->comment_articulo,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                                ]
                            );
                        }elseif ($cliente->serie == 0) {
                            DB::table('fac_products')->insert(
                                [
                                    'codigo_factura'=>$id,
                                    'codigo_articulo'=>$servicio->codigo_articulo,
                                    'nombre_articulo'=>$servicio->nombre_articulo,
                                    'precio_unitario'=>$servicio->precio_unitario,
                                    'IVA'=>$servicio->IVA,
                                    'cantidad'=>$servicio->cantidad,
                                    'precio_articulo'=>$servicio->precio_articulo,
                                    'comment_articulo'=>$servicio->comment_articulo,
                                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()

                                ]
                            );

                        }
                        fac_adic::where('id', $servicio->id)->update(['id_fac'=>$id]);
                    }
                }

                $facturacion=DB::select("SELECT fac_controls.*,
          (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
          (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
          where fac_controls.id_cliente = $cliente->id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas del cliente
                foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
                    if ($factura->denominacion == '$'){
                        $balance=balance_clientes_in::where('bal_cli_in', '=', $cliente->id)->where('bal_stat_in', 1)->where('bal_rest_in', '>', 0)->get();
                    }else{
                        $balance=balance_cliente::where('bal_cli', '=', $cliente->id)->where('bal_stat', 1)->where('bal_rest', '>', 0)->get();

                    }
                   // $tasa=configuracion::where('nombre','=','taza')->first()->valor;
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
                                        revisar_pagado($factura->id);
                                    }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                                       $restante=round(($restante-$deuda),2);//calculo lo que quedara
                                        fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                                        $factura->monto=0;
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
                                /*if ($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16 ){
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
                # code...*/
            }

        }















            }
            
           
        }
        $tmp->update(["valor"=>$numero]);
        historico::create(['responsable'=>'0', 'modulo'=>'Procesos', 'detalle'=>'Facturas Generadas '.$i]);
        echo 'Se generaron un total de '.$i.' facturas';
    }

}
