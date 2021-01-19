<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use Illuminate\Support\Facades\DB;

class PotencialesController extends Controller
{
    //
    public function index(){
      // return DB::table('servicios')  //buscamos los servicios
      // ->select('clientes.nombre', 'clientes.apellido', 'clientes.phone1', 'clientes.social', 'clientes.kind', 'servidores.*', 'servicios.ip_srv', 'servicios.id_srv', 'servicios.stat_srv', 'servicios.cliente_srv' )
      // ->join('clientes','clientes.id','=','servicios.cliente_srv')
      //     ->join('aps','aps.id','=','servicios.ap_srv')
      //     ->join('planes','planes.id_plan','=','servicios.plan_srv')
      //     ->join('celdas','aps.celda_ap','=','celdas.id_celda')
      //     ->join('servidores','celdas.servidor_celda','=','servidores.id_srvidor')
      //     ->where('servicios.cliente_srv','=','214')
      //     //->where('servicios.stat_srv','!=','3')
      //     ->where('servicios.stat_srv','!=','4')
      //     ->where('servicios.stat_srv','!=','5')
      //     //->where('servicios.ip_srv', '192.168.0.1')
      //     ->groupBy('servicios.ip_srv')
      //     ->get();
      $clientes = DB::table('clientes')
      ->get();

      foreach ($clientes as $cliente) {
        // busco las facturas
        $monto=0;
        $pagado=0;
        $cli=[];
        $facturas = DB::select(
        "SELECT fac_controls.*,
        (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
        ROUND((SELECT SUM(fac_products.precio_articulo) from  fac_products where fac_controls.id = fac_products.codigo_factura)) as monto,
        ROUND((SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id)) as pagado
        from fac_controls
        WHERE fac_controls.id_cliente = $cliente->id
        and fac_controls.fac_status = 1 ORDER BY created_at DESC;");
          foreach ($facturas as $factura) {
            if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
              if ($factura->denominacion == 'BSF'){
                if($factura->fac_status==1){
                $monto=$monto+($factura->monto/100000);
                $pagado=$pagado+($factura->pagado/100000);
              }
              }elseif ($factura->denominacion == 'Bs.S') {
                if($factura->fac_status==1){
                $monto=$monto+$factura->monto;
                $pagado=$pagado+$factura->pagado;
              }
              }
            }else{
              if($factura->denominacion == 'BSF'){
                if($factura->fac_status==1){
                $monto=$monto+($factura->monto/100000);
              }
              }elseif ($factura->denominacion == 'Bs.S') {
                if($factura->fac_status==1){
                $monto=$monto+$factura->monto;
              }
              }
            }
          }
          $servicios=DB::table('servicios')
          ->where('servicios.cliente_srv','=',$cliente->id)
          ->where('servicios.stat_srv','!=','4')
          ->where('servicios.stat_srv','!=','5')
          //->where('servicios.stat_srv','!=','3')
          ->get();
        $cliente->deuda = $monto-$pagado;
        $cliente->servicios = $servicios->count();
      }
      foreach ($clientes as $cliente) {
        if ($cliente->deuda>0 && $cliente->servicios > 0){
          array_push($cli, $cliente);
        }
      }
      foreach ($cli as $moroso) {
        $txt="actualmente presenta un saldo de: ".number_format($moroso->deuda, 2)." Bs.S";
        //$txt="le informamos que su servicio se encuentra suspendido. Actualmente presenta un saldo vencido en su facturación DE ".number_format($moroso->deuda, 2)." Bs.S";
        //$txt="recuerde que este es un sistema de mensajeria masiva no monitoreada. para cualquier informacion comuniquese a traves de: maraveca.com info@maraveca.com o al master 02617725180 ó 02687755100";
          if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
            $message= "Srs. ".ucwords(strtolower($moroso->social)).", ".$txt;
          }else {
            $message= "Sr.(a) ".ucwords(strtolower($moroso->nombre." ".$moroso->apellido)).", ".$txt;
          }
          echo $message."<br />";
        //sendsms($moroso->phone1, $message);

      }
    }
}
