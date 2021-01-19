<?php

namespace App\Http\Controllers;

use App\balance_cliente;
use App\balance_clientes_in;
use App\clientes;
use App\configuracion;
use App\fac_pago;
use App\fac_product;
use App\historico;
use App\historico_cliente;
use App\historico_config_admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\fac_control;
use Illuminate\Http\Response;

class FacProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return DB::table('fac_products')
            ->select('*', 'fac_controls.denominacion', 'fac_products.id as id_facProd')
            ->join('fac_controls', 'fac_controls.id', '=', 'fac_products.codigo_factura')
            ->orderBy('precio_articulo', 'DSC')
            ->where('codigo_factura', '=', $id)
            ->get();
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //return $request;

        $tasa = configuracion::where('nombre', '=', 'taza')->first()->valor;

        $fac = fac_control::where('id', '=', $request->codigo_factura)->first();

        if ($fac->fac_status == 3) {

            $fac->update(['fac_status' => 1]);

        }

       if ($fac->denominacion == '$') {                                       //si la factura es en blanco en dolares pero cambiada a bs



            $precio_bs = round(($request->precio_articulo * $tasa), 2); // cmabio el precio del servicio o producto cuando la tasa cambie



           $myNewData = $request->request->add([
               'precio_bs' => $precio_bs,
		'precio_dl' => $request->precio_articulo
	]);
 }
        fac_product::create($request->all());

        $clientes = DB::table('clientes')
            ->where('clientes.id', $fac->id_cliente)
            ->get();

        foreach ($clientes as $cliente) {


            $tasa = configuracion::where('nombre', '=', 'taza')->first()->valor;
            $facturacion = DB::select("SELECT fac_controls.*,
          (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
          (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
          where fac_controls.id_cliente = $cliente->id and fac_controls.fac_status = 1 ORDER BY created_at ASC ;");//selecciono todas las facturas del cliente
            foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
                if ($factura->denominacion == '$') {
                    $balance = balance_clientes_in::where('bal_cli_in', '=', $cliente->id)->where('bal_stat_in', 1)->where('bal_rest_in', '>', 0)->get(); // para los balances en facturas en dolares
                } else {
                    $balance = balance_cliente::where('bal_cli', '=', $cliente->id)->where('bal_stat', 1)->where('bal_rest', '>', 0)->get();

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
        }


        return 200;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\fac_product $fac_product
     * @return \Illuminate\Http\Response
     */
    public function show(fac_product $fac_product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\fac_product $fac_product
     * @return \Illuminate\Http\Response
     */
    public function edit(fac_product $fac_product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\fac_product $fac_product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $precio_articulo = ($request->cantidad * $request->precio_articulo);
        $request->offsetSet('precio_articulo', $precio_articulo);
        $prod = fac_product::where('id', '=', $id);

        $producto = $prod->get()->first();
        $cliente = clientes::where('id', '=', $request->cliente)->first();
        if ((strtolower($cliente->kind) == 'g' || strtolower($cliente->kind) == 'j') && (strtolower($cliente->social) != 'null' && $cliente->kind != null)) {
            $cli = ucwords(strtolower($cliente->social));
        } else {
            $cli = ucfirst($cliente->nombre) . " " . ucfirst($cliente->apellido);
        }
        historico_config_admin::create(['history' => 'Edicion de servicio/producto:  "' . $producto->nombre_articulo . '" de la factura Nº ' . $producto->codigo_factura . ' perteneciente al cliente ' . $cli, 'modulo' => 'Facturacion', 'cliente' => $request->cliente, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de servicio/producto "' . $producto->nombre_articulo . '" de la factura Nº ' . $producto->codigo_factura . ' perteneciente al cliente: ' . $cli]);
        unset($request['responsable']);
        unset($request['edit']);
        unset($request['cliente']);
        $prod->update($request->all());
        revisar_Balances($request->cliente);
   return 200;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\fac_product $fac_product
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        //
        $producto = fac_product::where('id', $id);
        $producto = $producto->get()->first();

        $cliente = clientes::where('id', '=', $request->cliente)->first();
        if ((strtolower($cliente->kind) == 'g' || strtolower($cliente->kind) == 'j') && (strtolower($cliente->social) != 'null' && $cliente->kind != null)) {
            $cli = ucwords(strtolower($cliente->social));
        } else {
            $cli = ucfirst($cliente->nombre) . " " . ucfirst($cliente->apellido);
        }
        historico_config_admin::create(['history' => 'Eliminacion de servicio/producto:  "' . $producto->nombre_articulo . '" de la factura Nº ' . $producto->codigo_factura . ' perteneciente al cliente ' . $cli, 'modulo' => 'Facturacion', 'cliente' => $request->cliente, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Eliminacion de servicio/producto "' . $producto->nombre_articulo . '" de la factura Nº ' . $producto->codigo_factura . ' perteneciente al cliente: ' . $cli]);

        $x = $producto->delete();
        return 200;
    }


    public function actualizar_precio(Request $request, $id)
    {
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        $producto= fac_product::where('codigo_factura', '=', $id)->get();
        foreach($producto as $prod){

            $monto_nuevo= round( ((float)$prod->precio_dl* (float)$tasa),2);



            fac_product:: where('id', $prod->id)->update(['precio_articulo' => $monto_nuevo]);

        }
        $fac= fac_control::where('id', '=', $id)->first();
        $data =  $request->json()->all();
        $responsable= $data['params']['responsable'];
        revisarBalance($fac->id_cliente);
        $cliente=clientes::where('id','=', $fac->id_cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }


        historico::create(['responsable' =>$responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de monto de la factura Nº '.$id.' perteneciente al cliente: ' .$cli]);
        historico_cliente::create(['history'=>'Edicion de monto de la factura Nº '.$id, 'modulo'=>'Facturacion', 'cliente'=>$fac->id_cliente, 'responsable'=>$responsable]);




        return $monto_nuevo;

    }

}