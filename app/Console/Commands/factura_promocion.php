<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\historico_cliente;
use App\historico;
use DB;
use App\configuracion;
use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_pago;
use App\fac_adic;
use \Carbon\Carbon;
use function App\Http\Controllers\revisar_pagado;

class factura_promocion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura_promocion
    {cliente : cliente para generar 1 factura}
    {fecha?}
    {fecha2?}
    {pro?}
    {responsable?}
    {nro_servicio?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        
        
        $date=$this->argument('fecha');
        $date2=$this->argument('fecha2');
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        $id_cliente = $this->argument('cliente');
        $pro = $this->argument('pro');
        $responsable= $this->argument('responsable');
        $fecha = date("Y-m-d H:i:s");
        $nro_servicio = $this->argument('nro_servicio');

        $cliente = DB::select("SELECT * FROM  clientes WHERE id = ?",[$id_cliente])[0];

        $promocion = DB::select("SELECT p.* FROM fac_promo AS f
                                     INNER JOIN planes AS p ON f.id_plan_p = p.id_plan
                                         WHERE id_cliente_p = ?",[$id_cliente])[0];


        $servicio = DB::select("SELECT s.id_srv,s.comment_srv,s.serie_srv,p.* FROM servicios AS s
                                    INNER JOIN planes AS p ON s.plan_srv = p.id_plan
                                            WHERE s.id_srv = ? ",[$nro_servicio])[0];

        $precio = $servicio->taza - $promocion->taza;
        $exo = $servicio->dmb_plan - $promocion->dmb_plan;   
            

        $today = Carbon::createFromFormat('d/m/Y',$date, 'America/Caracas');
        $today2 = Carbon::createFromFormat('d/m/Y',$date2, 'America/Caracas');
   
        $inicioMes = new Carbon("first day of this month");
        $inicioSiguienteMes = new Carbon('first day of next month');
        $finalMes = new Carbon('last day of this month');
        $diasRestantes=$today->diff($today2)->format('%a');
        $diasCompletos=$inicioSiguienteMes->diff($inicioMes)->format('%a');

       
        $calculo1 = $precio/ $diasCompletos;
        $precioFinal = $calculo1*$diasRestantes;

        

        $iva=configuracion::where('nombre','=','iva')->first();
        $iva1=($iva->valor+100)/100;
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
                    'denominacion'=>'Bs.S',
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
                'denominacion'=> '$', 
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
        } elseif ($cliente->kind == 'j' || $cliente->kind == 'J' || $cliente->kind == 'v-' || $cliente->kind == 'V-' || $cliente->kind == 'g' || $cliente->kind == 'G'){
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
        
        if($servicio->serie_srv == 1){ 
            DB::table('fac_products')->insert(
                [
                    'codigo_factura'=>$id,
                    'codigo_articulo'=>$servicio->id_plan,
                    'nombre_articulo'=>$servicio->name_plan,
                    'precio_articulo'=>round($precioFinal * $tasa, 2),
                    'IVA'=>"16",
                    'comment_articulo'=>"(Exoneracion Por Promocion,Cobro:".$exo."MB), Fac: Promocion (".$this->argument('fecha')."-".$this->argument('fecha2').")",
                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                    "precio_dl" =>round(($servicio->cost_plan*$iva1)/$tasa, 2),
                    'precio_bs' =>round(($servicio->taza *$tasa),2),

                ]
            );
        }else{

            DB::table('fac_products')->insert(
                [
                    'codigo_factura'=>$id,
                    'codigo_articulo'=>$servicio->id_plan,
                    'nombre_articulo'=>$servicio->name_plan,
                    'precio_articulo'=>$precioFinal,
                    'IVA'=>"16",
                    'comment_articulo'=>"(Exoneracion Por Promocion,Cobro:".$exo."MB), Fac: Promocion (".$this->argument('fecha')."-".$this->argument('fecha2').")",
                    "created_at" =>  \Carbon\Carbon::now(), # \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # \Datetime()
                    'precio_dl'=>round($precio, 2),
                    'precio_bs' =>round($precio-$tasa,2),

                ]
            );
        }
        
        historico_cliente::create(['history'=>'Generacion de factura Promocion Nº '.$id." Fecha:  Fac: Promocion (".$this->argument('fecha')."-".$this->argument('fecha2').")", 'modulo'=>'Facturacion', 'cliente'=>$cliente->id, 'responsable'=>$responsable]);
        
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

                    if(count($balance) < 1){
                        $restante = "no hay balance";
                    }else{
                        foreach ($balance as $restante1) {

                            if($servicio->serie_srv == 1){
                                $restante = $restante1->bal_rest;
                                if($restante>0){
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
                                            revisar_pagado($factura->id);
                                        }
                                        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                                            //echo $restante;
                                            $restante = round((+$restante / $tasa), 2);
                                            echo $restante;
                                        }

                                        $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                                        $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                                    }
                                }

                            }else{

                            $restante = $restante1->bal_rest_in;
        
                            if($restante>0){
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
                                    $up = balance_clientes_in::where('id_bal_in','=', $restante1->id_bal_in);
                                    $up->update(['bal_rest_in'=>$restante, 'uso_bal_in' => 1]);//acualizo lo que quedo
                                }
                            }
                         } 


                        }
                        
                    }
            }
               
    }
}
