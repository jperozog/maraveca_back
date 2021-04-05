<?php


namespace App\Http\Controllers;

use App\historico;
use App\historico_config_admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\clientes;
use App\balance_clientes_in;
use App\fac_pago;
use App\configuracion;

class BalanceClienteInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return balance_clientes_in ::
        select(
            'clientes.kind',
            'clientes.dni',
            'clientes.password',
            'clientes.email',
            'clientes.nombre',
            'clientes.apellido',
            'clientes.direccion',
            'clientes.day_of_birth',
            'clientes.serie',
            'clientes.phone1',
            'clientes.phone2',
            'clientes.comment',
            'clientes.social',
            'balance_clientes_ins.id_bal_in',
            'balance_clientes_ins.bal_cli_in',
            'balance_clientes_ins.bal_tip_in',
            'balance_clientes_ins.bal_stat_in',
            'balance_clientes_ins.bal_from_in',
            'balance_clientes_ins.bal_monto_in',
            'balance_clientes_ins.bal_rest_in',
            'balance_clientes_ins.bal_comment_in',
            'balance_clientes_ins.bal_comment_mod_in',
            'balance_clientes_ins.bal_fecha_mod_in',
            'balance_clientes_ins.user_bal_mod_in',
           'users.nombre_user',
            'balance_clientes_ins.created_at'
        )
            ->join('clientes', 'balance_clientes_ins.bal_cli_in', '=', 'clientes.id')
            ->join('users','users.id_user','=','balance_clientes_ins.user_bal_mod_in')
            ->orderBy('balance_clientes_ins.bal_stat_in', 'DESC')
            ->orderBy('balance_clientes_ins.updated_at', 'DESC')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        //funcion para crear el pago y verifico la facturacion del cliente para asumir deudas con el mismo

        $responsable = $request->responsable;//guardo el id del responsable
        unset($request['responsable']);//y lo borro despues
        //$request->bal_rest=$request->bal_monto;//configuro el restante igual al monto
        //return $request->bal_rest;
        $request->created_at = Carbon::createFromTimestamp($request->created_at)->toDateTimeString();
        $id = balance_clientes_in::create(
            [
                'bal_cli_in' => $request->bal_cli_in,
                'bal_tip_in' => $request->bal_tip_in,
                'bal_monto_in' => $request->bal_monto_in,
                'bal_rest_in' => $request->bal_monto_in,
                'bal_comment_in' => $request->bal_comment_in,
                'tasa' => $tasa,
                'uso_bal_in' => 1,
                'created_at' => $request->created_at
            ]
        );//guardo el pago

        revisarBalance_in($request->bal_cli_in);
        revisar_in($request->bal_cli_in);
        //revisar_pagado($factura->id);
        $result = clientes::where('id', $request->bal_cli_in)->get()->first();

            $mensaje = 'le informamos que su pago por US$ ' . $request->bal_monto_in . ' ha sido aprobado satisfactoriamente';


        $message = "";
        if ((strtolower($result->kind) == 'g' || strtolower($result->kind) == 'j') && (strtolower($result->social) != 'null' && $result->kind != null)) {
            $cliente = ucwords(strtolower($result->social));
            $message = "MARAVECA: Srs. " . $cliente . ", " . $mensaje;
        } else {
            $cliente = ucfirst($result->nombre) . " " . ucfirst($result->apellido);
            $message = "MARAVECA: Sr(a) " . $cliente . ", " . $mensaje;
        }

        //sendsms('04122398291', $message);
        // sendsms('412-6681426', $message);
       // sendsms($result->phone1, $message);
        return 200;
        //return $up->get();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\balance_clientes $balance_clientes_in
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return balance_clientes_in::where('bal_cli_in', '=', $id)->get();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\balance_clientes_in $balance_clientes_in
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //return $request;
        //return $motivo;

        $motivo = '';
        if ($request->option == 1) {
            $motivo = $request->reason;
        }
        $request1 = balance_clientes_in::where('id_bal_in', $request->id_bal_in);
        $request1->update(['bal_stat_in' => 0]);
        $request1 = $request1->join('clientes', 'balance_clientes_ins.bal_cli_in', '=', 'clientes.id')->get()->first();
        //return $request1;
        $result = clientes::where('id', $request1['bal_cli_in'])->get()->first();

            $mensaje = 'le informamos que su pago por US$ ' . $request1['bal_monto_in'] . ' ha sido reversado, para mas informacion comuniquese con el departamento de administracion';


        //$message="";
        if ((strtolower($result->kind) == 'g' || strtolower($result->kind) == 'j') && (strtolower($result->social) != 'null' && $result->kind != null)) {
            $cliente = ucwords(strtolower($result->social));
            $message = "MARAVECA: Srs. " . $cliente . ", " . $mensaje;
        } else {
            $cliente = ucfirst($result->nombre) . " " . ucfirst($result->apellido);
            $message = "MARAVECA: Sr(a) " . $cliente . ", " . $mensaje;
        }
        //$numero = '04122398291';
      sendmailBalance($motivo, $cliente, $request1, $request);
        //$numero = '04127503582';
        //sendsms($numero, $message);
     sendsms($result->phone1, $message);
        return 200;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\balance_clientes_in $balance_clientes_in
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //return $request;
        balance_clientes_in::where('id_bal_in', $request->id_bal_in)->update(['bal_stat_in' => 1]);
        $facturacion = DB::select("SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
              where fac_controls.id_cliente = $request->bal_cli_in and fac_controls.fac_status = 1  ORDER BY created_at ASC;");//selecciono todas las facturas del cliente
        foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
            $balance = balance_clientes_in::where('bal_cli_in', '=', $request->bal_cli_in)->where('bal_rest_in', '>', 0)->where('bal_stat_in', 1)->get();

            foreach ($balance as $restante1) {

                    $restante = $restante1->bal_rest_in;

               


                echo $factura->denominacion;
                if ($restante > 0) {

                    $deuda = round($factura->monto - $factura->pagado, 2);//calculo su deuda
                    if ($factura->monto > $factura->pagado) {//si no esta solvente
                        if ($deuda >= $restante) {//si la deuda es mayor o igual que el resto
                            fac_pago::create(['fac_id' => $factura->id, 'pag_tip' => $restante1->bal_tip_in, 'status' => '1', 'pag_monto' => $restante, 'pag_comment' => $restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//coloco todo el monto en un pago
                            $factura->pagado = +$factura->pagado + $restante;
                            $restante = 0;
                            revisar_pagado($factura->id);
                        } elseif ($deuda < $restante) {//si la deuda es menor que el resto
                            $restante=round(($restante-$deuda),2);//calculo lo que quedara
                            fac_pago::create(['fac_id' => $factura->id, 'pag_tip' => $restante1->bal_tip_in, 'status' => '1', 'pag_monto' => $deuda, 'pag_comment' => $restante1->bal_comment_in, 'balance_pago_in' => $restante1->id_bal_in  ]);//registro el pago con el monto de la deuda
                            $factura->monto = 0;
                            revisar_pagado($factura->id);
                        }
                        /*    if ($restante1->bal_tip_in != 12 || $restante1->bal_tip_in != 13 || $restante1->bal_tip_in != 14 || $restante1->bal_tip_in != 16){
                                $restante = (+$restante * (float)$restante1->tasa);
                                echo $restante;
                            }*/

                        $up = balance_clientes_in::where('id_bal_in', '=', $restante1->id_bal_in);
                        $up->update(['bal_rest_in'=>$restante, 'uso_bal_in' => 1]);//acualizo lo que quedo
                    }


                }
            }

        }
        /*
        $result = clientes::where('id', $request->bal_cli_in)->get()->first();
        $balanceo = 0;
        $afavor = 0;
        $facturacion1 = DB::select(
            "SELECT fac_controls.*,
                  (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
                  (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
                  where fac_controls.id_cliente = " . $request->bal_cli_in . " and fac_controls.fac_status = 1 ORDER BY created_at DESC;");

        foreach ($facturacion1 as $fac) {
            $bal = $fac->pagado - $fac->monto;
            $fac->bal = $bal;
            if ($bal != 0) {
                $balanceo += round($bal, 2);
            }
        }
        $balance = balance_clientes_in::where('bal_cli_in', '=', $request->bal_cli_in)->get();

        foreach ($balance as $bal) {
            if ($bal->bal_stat_in == '1') {
                if ($bal->bal_rest_in > 0) {
                    $afavor += round($bal->bal_rest_in, 2);
                }
            }
        }


            if ($balanceo == 0 && $afavor > 0) {
                $mensaje = 'le informamos que su pago por US$ ' . $request->bal_monto_in . ' ha sido aprobado satisfactoriamente, usted posee un saldo a su favor de: ' . $afavor . " US$. ";
            } elseif ($balanceo == 0 && $afavor == 0) {
                $mensaje = 'le informamos que su pago por US$ ' . $request->bal_monto_in . ' ha sido aprobado satisfactoriamente, usted se encuentra solvente';
            } elseif ($balanceo < 0) {
                $mensaje = 'le informamos que su pago por US$ ' . $request->bal_monto_in . ' ha sido procesado satisfactoriamente, usted aun presenta un saldo pendiente de: ' . $balanceo * -1 . " US$. ";
            }
        //revisar_pagado
        revisar_in($request->bal_cli_in);
        $message = "";
        if ((strtolower($result->kind) == 'g' || strtolower($result->kind) == 'j') && (strtolower($result->social) != 'null' && $result->kind != null)) {
            $cliente = ucwords(strtolower($result->social));
            $message = "MARAVECA: Srs. " . $cliente . ", " . $mensaje;
        } else {
            $cliente = ucfirst($result->nombre) . " " . ucfirst($result->apellido);
            $message = "MARAVECA: Sr(a) " . $cliente . ", " . $mensaje;
        }

       // sendsms($result->phone1, $message); //cliente
        //sendsms("04126681426", $message); //ramon
        //sendsms("04122398291", $message); //henry
        //sendsms("04127503582", $message); //oroxo
        */
        return $factura->id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\balance_clientes_in $balance_clientes_in
     * @return \Illuminate\Http\Response
     */
    public function destroy(balance_clientes_in $balance_cliente_in)
    {
        //
    }
    public function edit_balance_in(Request $request, $id)
    {
        $balance = balance_clientes_in::where('id_bal_in','=', $id)->update($request->all());

        return $balance;
    }


    public function edit_balance_in_config(Request $request, $id){
        $result = balance_clientes_in::where('id_bal_in','=', $id)->first();

        switch ($request->bal_from_in) {
            case 1:
                $rebalf= "Mismo Banco";
            case 2:
                $rebalf= "Otro Banco";
                break;
            case 4:
                $rebalf= "Coro";
                break;
            case 5:
                $rebalf= "Maracaibo";
                break;
            case 6:
                $rebalf= "Taquilla Dabajuro";
                break;


        }

        switch ($result->bal_from_in) {
            case 1:
                $refban= "Mismo Banco";
            case 2:
                $refban= "Otro Banco";
                break;
            case 4:
                $refban= "Coro";
                break;
            case 5:
                $refban= "Maracaibo";
                break;
            case 6:
                $refban= "Taquilla Dabajuro";
                break;


        }


        switch ($request->bal_tip_in) {
            case 12:
                $rbaltip= "Zelle";
                break;
            case 13:
                $rbaltip= "Wire Transfer";
                break;
            case 14:
                $rbaltip= "Efectivo $";
                break;case 16:
                $rbaltip= "Exonerado";
                break;


        }

        switch ($result->bal_tip_in) {
            case 12:
                $baltip= "Zelle";
                break;
            case 13:
                $baltip= "Wire Transfer";
                break;
            case 14:
                $baltip= "Efectivo $";
                break;case 16:
            $baltip= "Exonerado";
            break;
        }


        $cliente=clientes::where('id','=',$request->bal_cli_in)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        if($result->bal_monto != $request->bal_monto){

                historico_config_admin::create(['history' => 'Edicion de pago de cliente N°:  '.$id.' de '.$result->bal_monto_in.' $ a ' .$request->bal_monto_in. ' Bs.S', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli_in, 'responsable' => $request->responsable]);

                historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de pago de cliente N°:  '.$id.' de '.$result->bal_monto_in.' Bs.S a ' .$request->bal_monto_in.' Bs.S, perteneciente al cliente: ' .$cli]);



        }
        if($result->bal_from_in != $request->bal_from_in){

            historico_config_admin::create(['history' => 'Edicion de origen de la transferencia del  pago de cliente N°:  '.$id.' de "'.$refban.'" a "' .$rebalf.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli_in, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de origen de la transferencia, del pago de cliente N°: ' .$id. '  de '.$refban. ' a '.$rebalf.', perteneciente al cliente: ' .$cli]);
        }
        if($result->bal_tip_in != $request->bal_tip_in){

            historico_config_admin::create(['history' => 'Edicion del Banco de la transferencia del pago del cliente N°:  '.$id.'  de "'.$baltip.'"" a "' .$rbaltip.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli_in, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion del pago de cliente N°: ' .$id. ' de Banco de la transferencia de '.$baltip. ' a '.$rbaltip.', perteneciente al cliente: ' .$cli]);
        }
        if($result->bal_comment_in != $request->bal_comment_in){

            historico_config_admin::create(['history' => 'Edicion de la referencia de transferencia del pago de cliente N°:  '.$id.'  de "'.$result->bal_comment_in.'" a "' .$request->bal_comment_in.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli_in, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion del pago de cliente N°: ' .$id. ' de la referencia de la transferencia de "'.$result->bal_tip_in. '" a "'.$request->bal_tip_in.'", perteneciente al cliente: ' .$cli]);
        }
        balance_clientes_in::where('id_bal_in','=', $id) ->update([
            'bal_cli_in' => $request->bal_cli_in ,
            'bal_tip_in' => $request->bal_tip_in ,
            'bal_from_in' => $request->bal_from_in,
            'bal_comment_in' => $request->bal_comment_in,
            'bal_monto_in' => $request->bal_monto_in,
            'bal_rest_in' =>  $request->bal_monto_in,


        ]);
        return 200;
    }




    public function anular_balance_in_config(Request $request, $id)
    {
        $data =  $request->json()->all();

        $responsable= $data['params']['responsable'];
        $datos = $data['params']['datos'];
        $id_cliente= $datos['bal_cli_in'];

        $cliente=clientes::where('id','=',$id_cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }

        $bal_bs=balance_clientes_in::where('id_bal_in', $id);
        fac_pago::where('balance_pago_in',$id )->delete();
        historico_config_admin::create(['history' => 'Anulacion del  pago de cliente N°:  '.$id , 'modulo' => 'Facturacion', 'cliente' =>$id_cliente, 'responsable' => $responsable]);

        historico::create(['responsable' => $responsable, 'modulo' => 'Facturacion', 'detalle' => 'Anulacion del  pago de cliente N°: ' .$id.', perteneciente al cliente: ' .$cli]);

        $bal_bs->update(['bal_stat_in'=>0]);

        return 200;
    }


}


