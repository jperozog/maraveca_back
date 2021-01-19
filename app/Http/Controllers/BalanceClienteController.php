<?php

namespace App\Http\Controllers;
use App\historico;
use App\historico_config_admin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\clientes;
use App\balance_cliente;
use App\fac_pago;
use App\configuracion;

class BalanceClienteController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return balance_cliente::
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
      'balance_clientes.id_bal',
      'balance_clientes.bal_cli',
      'balance_clientes.bal_tip',
      'balance_clientes.bal_stat',
      'balance_clientes.bal_from',
      'balance_clientes.bal_monto',
      'balance_clientes.bal_rest',
      'balance_clientes.bal_comment',
        'balance_clientes.user_bal_mod',
        'users.nombre_user',
      'balance_clientes.created_at',
        'bal_comment_mod',
      'user_bal_mod',
      'bal_fecha_mod'
      )
      ->join('clientes','balance_clientes.bal_cli','=','clientes.id')
      ->join('users','users.id_user','=','balance_clientes.user_bal_mod')
      ->orderBy('balance_clientes.bal_stat','DESC')
      ->orderBy('balance_clientes.updated_at','DESC')
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
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
      //funcion para crear el pago y verifico la facturacion del cliente para asumir deudas con el mismo
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
      $responsable=$request->responsable;//guardo el id del responsable
      unset($request['responsable']);//y lo borro despues
      //$request->bal_rest=$request->bal_monto;//configuro el restante igual al monto
      //return $request->bal_rest;
      $request->created_at=Carbon::createFromTimestamp($request->created_at)->toDateTimeString();
      $id=balance_cliente::create(
        [
          'bal_cli'=>$request->bal_cli,
          'bal_tip'=>$request->bal_tip,
          'bal_monto'=>$request->bal_monto,
          'bal_rest'=>$request->bal_monto,
            'tasa'=>$tasa,
          'bal_comment'=>$request->bal_comment,
          'created_at'=>$request->created_at
        ]
      );//guardo el pago

      revisarBalance($request->bal_cli);
      revisar($request->bal_cli);

      $result=clientes::where('id', $request->bal_cli)->get()->first();
      if ($request-> bal_tip == 12 ||$request-> bal_tip == 13 ||$request-> bal_tip == 14){
          $mensaje='le informamos que su pago por US$ '.$request->bal_monto.' ha sido aprobado satisfactoriamente';
      }else{
          $mensaje='le informamos que su pago por Bs.S '.$request->bal_monto.' ha sido aprobado satisfactoriamente';

      }

      $message="";
      if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
        $cliente= ucwords(strtolower($result->social));
        $message= "MARAVECA: Srs. ".$cliente.", ".$mensaje;
      }else {
        $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
        $message= "MARAVECA: Sr(a) ".$cliente.", ".$mensaje;
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
    * @param  \App\balance_cliente  $balance_cliente
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {

      return balance_cliente::where('bal_cli', '=', $id)->get();

    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\balance_cliente  $balance_cliente
    * @return \Illuminate\Http\Response
    */
    public function delete(Request $request)
    {
      //return $request;
      //return $motivo;

            $motivo='';
            if($request->option == 1){
              $motivo = $request->reason;
            }
            $request1=balance_cliente::where('id_bal', $request->id_bal);
            $request1->update(['bal_stat'=>0]);
            $request1=$request1->join('clientes','balance_clientes.bal_cli','=','clientes.id')->get()->first();
            //return $request1;
            $result=clientes::where('id', $request1['bal_cli'])->get()->first();
            if($request1->get()->first()->bal_tip == 8 || $request1->get()->first()->bal_tip == 9 || $request1->get()->first()->bal_tip == 10 || $request1->get()->first()->bal_tip == 11){
            $mensaje='le informamos que su pago por US$ '.$request1['bal_monto'].' ha sido reversado, para mas informacion comuniquese con el departamento de administracion';

          } else{
            $mensaje='le informamos que su pago por Bs.S '.$request1['bal_monto'].' ha sido reversado, para mas informacion comuniquese con el departamento de administracion';
          }
            //$message="";
            if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
              $cliente= ucwords(strtolower($result->social));
              $message= "MARAVECA: Srs. ".$cliente.", ".$mensaje;
            }else {
              $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
              $message= "MARAVECA: Sr(a) ".$cliente.", ".$mensaje;
            }
            //$numero = '04122398291';
            sendmailBalance($motivo, $cliente, $request1, $request);
            //$numero = '04127503582';
            //sendsms($numero, $message);
          //  sendsms($result->phone1, $message);
            return 200;
          }

          /**
          * Update the specified resource in storage.
          *
          * @param  \Illuminate\Http\Request  $request
          * @param  \App\balance_cliente  $balance_cliente
          * @return \Illuminate\Http\Response
          */
          public function update(Request $request)
          {
            //return $request;
            balance_cliente::where('id_bal', $request->id_bal)->update(['bal_stat'=>1]);
            $facturacion=DB::select("SELECT fac_controls.*,
              (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
              (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado from fac_controls
              where fac_controls.id_cliente = $request->bal_cli and fac_controls.fac_status = 1 ORDER BY created_at ASC;");//selecciono todas las facturas del cliente
              foreach ($facturacion as $factura) { //para cada factura reviso su deuda y asumo desde lo cargado
                $balance=balance_cliente::where('bal_cli', '=', $request->bal_cli)->where('bal_rest', '>', 0)->where('bal_stat', 1)->get();
                $taza=configuracion::where('nombre','=','taza')->first()->valor;
                //return $taza;
                foreach ($balance as $restante1) {
                  if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                    //return +$restante1->bal_rest * $taza;
                    $restante = (round(+$restante1->bal_rest * $taza, 2));
                    //echo $restante;
                  }else{

                    $restante=$restante1->bal_rest;
                  }
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
                        }elseif ($deuda<$restante) {//si la deuda es menor que el resto
                          echo 'pagable';
                          $restante=+$restante-$deuda;//calculo lo que quedara
                          fac_pago::create(['fac_id'=>$factura->id, 'pag_tip'=>$restante1->bal_tip, 'status'=>'1', 'pag_monto'=>$deuda, 'pag_comment'=>$restante1->bal_comment, 'balance_pago' => $restante1->id_bal]);//registro el pago con el monto de la deuda
                          $factura->monto=0;

                        }
                        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                          $restante == round(+$restante / $taza, 2);
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
                        }
                        if($restante1->bal_tip == 8 || $restante1->bal_tip == 9 || $restante1->bal_tip == 10 || $restante1->bal_tip == 11){
                          //echo $restante;
                          $restante = round((+$restante / $taza), 2);
                          echo $restante;
                        }

                        $up = balance_cliente::where('id_bal','=', $restante1->id_bal);
                        $up->update(['bal_rest'=>$restante]);//acualizo lo que quedo
                      }

                    }}
                  }
                }
                $result=clientes::where('id', $request->bal_cli)->get()->first();
                $balanceo=0;
                $afavor=0;
                $facturacion1 = DB::select(
                  "SELECT fac_controls.*,
                  (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
                  (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
                  (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
                  where fac_controls.id_cliente = ".$request->bal_cli." and fac_controls.fac_status = 1 ORDER BY created_at DESC;");

                  foreach ($facturacion1 as $fac) {
                    $bal=$fac->pagado-$fac->monto;
                    $fac->bal=$bal;
                    if($bal!=0){
                      $balanceo+=round($bal, 2);
                    }
                  }
                  $balance=balance_cliente::where('bal_cli', '=', $request->bal_cli)->get();

                  foreach ($balance as $bal) {
                    if($bal->bal_stat == '1'){
                      if($bal->bal_rest>0){
                        $afavor+=round($bal->bal_rest, 2);
                      }
                    }
                  }
                  if($request->bal_tip == 8 || $request->bal_tip == 9 || $request->bal_tip == 10 || $request->bal_tip == 11){

                  if($balanceo==0 && $afavor >0){
                    $mensaje='le informamos que su pago por US$ '.$request->bal_monto.' ha sido aprobado satisfactoriamente, usted posee un saldo a su favor de: '.$afavor." Bs.S.";
                  }elseif($balanceo==0 && $afavor ==0){
                    $mensaje='le informamos que su pago por US$ '.$request->bal_monto.' ha sido aprobado satisfactoriamente, usted se encuentra solvente';
                  }elseif ($balanceo<0) {
                    $mensaje='le informamos que su pago por US$ '.$request->bal_monto.' ha sido procesado satisfactoriamente, usted aun presenta un saldo pendiente de: '.$balanceo*-1 ." Bs.S.";
                  }
                }else{
                  if($balanceo==0 && $afavor >0){
                    $mensaje='le informamos que su pago por Bs.S '.$request->bal_monto.' ha sido aprobado satisfactoriamente, usted posee un saldo a su favor de: '.$afavor." Bs.S.";
                  }elseif($balanceo==0 && $afavor ==0){
                    $mensaje='le informamos que su pago por Bs.S '.$request->bal_monto.' ha sido aprobado satisfactoriamente, usted se encuentra solvente';
                  }elseif ($balanceo<0) {
                    $mensaje='le informamos que su pago por Bs.S '.$request->bal_monto.' ha sido procesado satisfactoriamente, usted aun presenta un saldo pendiente  de: '.$balanceo*-1 ." Bs.S.";
                  }
                }
                revisar($request->bal_cli);
                  $message="";
                  if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
                    $cliente= ucwords(strtolower($result->social));
                    $message= "MARAVECA: Srs. ".$cliente.", ".$mensaje;
                  }else {
                    $cliente= ucfirst($result->nombre)." ".ucfirst($result->apellido);
                    $message= "MARAVECA: Sr(a) ".$cliente.", ".$mensaje;
                  }

                  sendsms($result->phone1, $message); //cliente
                  //sendsms("04126681426", $message); //ramon
                  //sendsms("04122398291", $message); //henry
                  //sendsms("04127503582", $message); //oroxo

                  return 200;
                }

                /**
                * Remove the specified resource from storage.
                *
                * @param  \App\balance_cliente  $balance_cliente
                * @return \Illuminate\Http\Response
                */
                public function destroy(balance_cliente $balance_cliente)
                {
                  //
                }

    public function edit_balance(Request $request, $id)
    {
        $balance = balance_cliente::where('id_bal','=', $id)->update($request->all());

        return $balance;
    }

    public function edit_balance_config(Request $request, $id){
                    $result = balance_cliente::where('id_bal','=', $id)->first();

switch ($request->bal_from) {
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
case 12:
        $rebalf= "otros bancos (Venezuela)";
        break;
case 13:
        $rebalf= "otros bancos (Bicentenario)";
        break;
case 14:
        $rebalf= "otros bancos (BNC)";
        break;
case 15:
        $rebalf= "otros bancos (Provincial)";
        break;
case 16:
        $rebalf= "otros bancos (Mercantil)";
        break;
case 17:
        $rebalf= "otros bancos (Banco del tesoro)";
        break;
case 18:
        $rebalf= "otros bancos (Banco Venezolano de credito)";
        break;
case 19:
        $rebalf= "otros bancos (Banplus)";
        break;
case 20:
        $rebalf= "otros bancos (BFC)";
        break;

}

        switch ($result->bal_from) {
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
            case 12:
                $refban= "otros bancos (Venezuela)";
                break;
            case 13:
                $refban= "otros bancos (Bicentenario)";
                break;
            case 14:
                $refban= "otros bancos (BNC)";
                break;
            case 15:
                $refban= "otros bancos (Provincial)";
                break;
            case 16:
                $refban= "otros bancos (Mercantil)";
                break;
            case 17:
                $refban= "otros bancos (Banco del tesoro)";
                break;
            case 18:
                $refban= "otros bancos (Banco Venezolano de credito)";
                break;
            case 19:
                $refban= "otros bancos (Banplus)";
                break;
            case 20:
                $refban= "otros bancos (BFC)";
                break;

        }


        switch ($request->bal_tip) {
            case 1:
                $rbaltip= "Transferencia BOD";
            case 2:
                $rbaltip= "Transferencia Banesco";
                break;
            case 3:
                $rbaltip= "Transferencia Venezuela";
                break;
            case 4:
                $rbaltip= "Retencion ISLR";
                break;
            case 5:
                $rbaltip= "Retencion IVA";
                break;
            case 7:
                $rbaltip= "Exonerado";
                break;
            case 8:
                $rbaltip= "Moneda Extranjera";
                break;
            case 9:
                $rbaltip= "Zelle";
                break;
            case 10:
                $rbaltip= "Wire Transfer";
                break;
            case 11:
                $rbaltip= "Efectivo $";
                break;
            case 15:
                $rbaltip= "Efectivo en Bs.S";
                break;

        }

  switch ($result->bal_tip) {
            case 1:
                $baltip= "Transferencia BOD";
            case 2:
                $baltip= "Transferencia Banesco";
                break;
            case 3:
                $baltip= "Transferencia Venezuela";
                break;
            case 4:
                $baltip= "Retencion ISLR";
                break;
            case 5:
                $baltip= "Retencion IVA";
                break;
            case 7:
                $baltip= "Exonerado";
                break;
            case 8:
                $baltip= "Moneda Extranjera";
                break;
            case 9:
                $baltip= "Zelle";
                break;
            case 10:
                $baltip= "Wire Transfer";
                break;
            case 11:
                $baltip= "Efectivo $";
                break;
            case 15:
                $baltip= "Efectivo en Bs.S";
                break;

        }


        $cliente=clientes::where('id','=',$request->bal_cli)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        if($result->bal_monto != $request->bal_monto){
      if($request->bal_tip!=8 && $request->bal_tip!=9 && $request->bal_tip!=10 && $request->bal_tip!=11){
        historico_config_admin::create(['history' => 'Edicion de pago de cliente N°:  '.$id.' de '.$result->bal_monto.' Bs.S a ' .$request->bal_monto. ' Bs.S', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de pago de cliente N°:  '.$id.' de '.$result->bal_monto.' Bs.S a ' .$request->bal_monto.' Bs.S, perteneciente al cliente: ' .$cli]);

    }
            if($request->bal_tip==8 || $request->bal_tip==9 || $request->bal_tip==10 || $request->bal_tip==11){
                historico_config_admin::create(['history' => 'Edicion del monto de pago del cliente N°:  '.$id.' de '.$result->bal_monto.' $ a ' .$request->bal_monto. ' $', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli, 'responsable' => $request->responsable]);

                historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion del monto de pago del cliente N°:  '.$id.' de '.$result->bal_monto.' $ a ' .$request->bal_monto.' $, perteneciente al cliente: ' .$cli]);

            }
    }
        if($result->bal_from != $request->bal_from){

            historico_config_admin::create(['history' => 'Edicion de origen de la transferencia del  pago de cliente N°:  '.$id.' de "'.$refban.'" a "' .$rebalf.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de origen de la transferencia, del pago de cliente N°: ' .$id. '  de '.$refban. ' a '.$rebalf.', perteneciente al cliente: ' .$cli]);
        }
        if($result->bal_tip != $request->bal_tip){

            historico_config_admin::create(['history' => 'Edicion del Banco de la transferencia del pago del cliente N°:  '.$id.'  de "'.$baltip.'"" a "' .$rbaltip.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion del pago de cliente N°: ' .$id. ' de Banco de la transferencia de '.$baltip. ' a '.$rbaltip.', perteneciente al cliente: ' .$cli]);
        }
        if($result->bal_comment != $request->bal_comment){

            historico_config_admin::create(['history' => 'Edicion de la referencia de transferencia del pago de cliente N°:  '.$id.'  de "'.$result->bal_comment.'" a "' .$request->bal_comment.'"', 'modulo' => 'Facturacion', 'cliente' =>$request->bal_cli, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion del pago de cliente N°: ' .$id. ' de la referencia de la transferencia de "'.$result->bal_tip. '" a "'.$request->bal_tip.'", perteneciente al cliente: ' .$cli]);
        }
        balance_cliente::where('id_bal','=', $id) ->update([
            'bal_cli' => $request->bal_cli ,
            'bal_tip' => $request->bal_tip ,
            'bal_from' => $request->bal_from,
            'bal_comment' => $request->bal_comment,
            'bal_monto' => $request->bal_monto,
            'bal_rest' =>  $request->bal_monto,


        ]);
  return 200;
    }


public function anular_balance_config(Request $request, $id)
{
$data =  $request->json()->all();

$responsable= $data['params']['responsable'];
$datos = $data['params']['datos'];
$id_cliente= $datos['bal_cli'];

$cliente=clientes::where('id','=',$id_cliente)->first();
    if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
        $cli= ucwords(strtolower($cliente->social));
    }else {
        $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
    }

    $bal_bs=balance_cliente::where('id_bal', $id);
fac_pago::where('balance_pago',$id )->delete();
    historico_config_admin::create(['history' => 'Anulacion del  pago de cliente N°:  '.$id , 'modulo' => 'Facturacion', 'cliente' =>$id_cliente, 'responsable' => $responsable]);

    historico::create(['responsable' => $responsable, 'modulo' => 'Facturacion', 'detalle' => 'Anulacion del  pago de cliente N°: ' .$id.', perteneciente al cliente: ' .$cli]);

    $bal_bs->update(['bal_stat'=>0]);

    return 200;
}
}