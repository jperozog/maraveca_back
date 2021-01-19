<?php

namespace App\Http\Controllers;

use App\balance_clientes_in;
use App\fac_control;
use App\fac_pago;
use App\fac_products;
use App\balance_cliente;
use App\clientes;
use App\historico;
use App\historico_config_admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FacPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
      return DB::table('fac_pagos')
          ->select('*', 'fac_controls.denominacion', 'fac_pagos.id as id_facPag', 'fac_pagos.created_at', 'fac_pagos.updated_at')
          ->join('fac_controls','fac_controls.id','=','fac_pagos.fac_id')
      ->orderBy('fac_pagos.created_at','ASC')
      ->where('fac_id', '=', $id)

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
   $responsable = $request->responsable;
      unset($request['responsable']);
      $t1 = fac_pago::create($request->all());
      $cliente=DB::table('fac_pagos')->select('id_cliente')->join('fac_controls', 'fac_controls.id', '=', 'fac_pagos.fac_id')->where('fac_pagos.id', $t1->id)->get()->first();
      revisar($cliente->id_cliente);
      revisar_pagado($t1->fac_id);
      return $t1;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\fac_pago  $fac_pago
     * @return \Illuminate\Http\Response
     */
    public function show(fac_pago $fac_pago)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\fac_pago  $fac_pago
     * @return \Illuminate\Http\Response
     */
    public function edit(fac_pago $fac_pago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\fac_pago  $fac_pago
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $responsable = $request->responsable_edit;


        $pag = fac_pago::where('id', '=', $id);
       $pago = $pag->get()->first();
        $cliente=clientes::where('id','=',$request->cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        historico_config_admin::create(['history' => 'Edicion de pago de '.number_format($pago->pag_monto, 2, '.', ',').' '.$request->denominacion.' a '.number_format($request->pag_monto, 2, '.', ',').' '.$request->denominacion.' de la factura Nº '.$pago->fac_id.' perteneciente al cliente '.$cli, 'modulo' => 'Facturacion', 'cliente' => $request->cliente, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Edicion de pago  de '.number_format($pago->pag_monto, 2, '.', ',').' '.$request->denominacion.' a '.number_format($request->pag_monto, 2, '.', ',').' '.$request->denominacion.' de la factura Nº ' .$pago->fac_id.' perteneciente al cliente: ' .$cli]);
        unset($request['responsable_edit']);
        unset($request['responsable']);
        unset($request['edit']);
        unset($request['cliente']);
        unset($request['denominacion']);
        $pag->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\fac_pago  $fac_pago
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $fac_p= fac_pago::where('id', $id);
        $fac_pago = $fac_p->get();
        foreach($fac_pago as $pago ){
            if (isset($pago ->balance_pago) && $pago ->balance_pago != null){
                $balance=balance_cliente::where('id_bal', '=', $pago->balance_pago);
                $bal= $balance->first();
                if ( $bal-> bal_tip == 8 || $bal-> bal_tip == 9 || $bal-> bal_tip == 10 || $bal-> bal_tip == 11) {
                    $restodl= round(($pago->pag_monto / $bal->tasa),2);

                    $balance->update(['bal_rest' => DB::raw($restodl)]); //nuevo
                } else {
                    $balance->update(['bal_rest' => DB::raw('bal_rest+ ' .$pago->pag_monto)]); //nuevo

                }
            }
            elseif (isset($pago ->balance_pago_in) && $pago ->balance_pago_in != null) {
                $balance=balance_clientes_in::where('id_bal_in', '=', $pago->balance_pago_in);

                $balance->update(['bal_rest_in' => DB::raw('bal_rest_in+ ' .$pago->pag_monto)]);
            }

        }
        $cliente=clientes::where('id','=',$request->cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        historico_config_admin::create(['history' => 'Eliminacion de pago por un monto de '.number_format($pago->pag_monto, 2, '.', ',').' '.$request->denominacion.' de la factura Nº '.$pago->fac_id.' perteneciente al cliente '.$cli, 'modulo' => 'Facturacion', 'cliente' => $request->cliente, 'responsable' => $request->responsable]);

        historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' => 'Eliminacion de pago por un monto de '.number_format($pago->pag_monto, 2, '.', ',').' '.$request->denominacion.' de la factura Nº ' .$pago->fac_id.' perteneciente al cliente: ' .$cli]);
        $x= $fac_p->delete();
        return $x;
    }
}
