<?php

namespace App\Http\Controllers;

use App\fac_control;
use Illuminate\Support\Facades\DB;
use App\stat;
use App\User;
use App\oinstall;
use App\oinstallpgo;
use App\instinst;
use App\user_comisiones;
use App\instpag;
use App\pagos_comisiones;
use App\historico_comision;
use Illuminate\Http\Request;

class StatController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    $month=[];
    $month[0] = date('d-m-Y', strtotime('0 month'));
    $month[1] = date('d-m-Y', strtotime('-1 month'));
    $month[2] = date('d-m-Y', strtotime('-2 month'));
    $month[3] = date('d-m-Y', strtotime('-3 month'));
    $month[4] = date('d-m-Y', strtotime('-4 month'));
    $month[5] = date('d-m-Y', strtotime('-5 month'));
    $month[6] = date('d-m-Y', strtotime('-6 month'));
    $status=[];
    $fechas=[];
    foreach ($month as $mes) {
      $m=date('n', strtotime($mes));
      $y=date('Y', strtotime($mes));
      $facturado=0;
      $pagado = 0;
      $facturas = DB::select(
        "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
        from fac_controls where fac_controls.fac_status = 1 and fac_controls.denominacion != '$'  and MONTH(fac_controls.created_at) = $m and YEAR(fac_controls.created_at) = $y ORDER BY created_at DESC ;");
        if(count($facturas)>0){
          $den=$facturas[0]->denominacion;
        }
        foreach ($facturas as $factura) {
        
            $facturado=$facturado+$factura->monto;
            $pagado=$pagado+$factura->pagado;
         
        }
        array_push($status, ['pagado'=>round($pagado, 2), 'facturado'=>round($facturado, 2), 'fecha'=>date('n-Y', strtotime($mes))]);
        array_push($fechas, ['fecha'=>date('n-Y', strtotime($mes))]);
      }
      return collect(['datos'=>$status, 'fechas'=>$fechas]);
    }

    public function indexdl()
    {
        $month=[];
        $month[0] = date('d-m-Y', strtotime('0 month'));
        $month[1] = date('d-m-Y', strtotime('-1 month'));
        $month[2] = date('d-m-Y', strtotime('-2 month'));
        $status=[];
        $fechas=[];
        foreach ($month as $mes) {
            $m=date('n', strtotime($mes));
            $y=date('Y', strtotime($mes));
            $facturado=0;
            $pagado = 0;
            $facturas = DB::select(
                "SELECT fac_controls.*,
        (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
        (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
        from fac_controls where fac_controls.fac_status = 1 and fac_controls.denominacion = '$' and MONTH(fac_controls.created_at) = $m and YEAR(fac_controls.created_at) = $y  ORDER BY created_at DESC ;");
        /*
            if(count($facturas)>0){
                $den=$facturas[0]->denominacion;
            }
            foreach ($facturas as $factura) {
                if($den=='$'){
                    $facturado=$facturado+$factura->monto;
                    $pagado=$pagado+$factura->pagado;
                }
            }


            array_push($status, ['pagado'=>round($pagado, 2), 'facturado'=>round($facturado, 2), 'fecha'=>date('n-Y', strtotime($mes))]);
            */
            array_push($status,$facturas);
            array_push($fechas, ['fecha'=>date('n-Y', strtotime($mes))]);
        }
        $datos = collect(['datos'=>$status, 'fechas'=>$fechas]);

        return response()->json($datos);
    }

    public function show(Request $request){
      $fecha=explode('-', $request->mes);
      $incidencia=DB::select("SELECT count(fac_pagos.id) as numero_pagos, DATE_FORMAT(fac_pagos.updated_at, '%d-%m-%Y') as fecha, round(SUM(fac_pagos.pag_monto), 2) as monto_pagos, fac_controls.denominacion
FROM fac_pagos
left join fac_controls on fac_controls.id = fac_pagos.fac_id
where month(fac_controls.created_at) = $fecha[0] and YEAR(fac_controls.created_at) = $fecha[1] and fac_controls.fac_status = 1
group by DATE_FORMAT(fac_pagos.updated_at, '%Y-%m-%d')");

        return $incidencia;

        }
        /**
        * Show the form for creating a new resource.
        *
        * @return \Illuminate\Http\Response
        */
        public function instaladores()
        {
          return user::select(DB::raw('*, (select count(*) from instinsts where instinsts.installer = users.id_user and stat = 1) as installs'))->where('installer', '=', '1')->get();
        }

        public function instalador($id)
        {
          $installs = instinst::where('installer', '=', $id)->get();
          $pagos = instpag::where('installer', '=', $id)->get();
          $index = collect(['instalaciones'=>$installs,'pagos'=>$pagos]);

          return response()->json($index); //lo devuelvo via rest los soportes de determinado usuario

        }

        /**
        * Store a newly created resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\Response
        */
        public function store(Request $request)
        {
          //
        }

        /**
        * Display the specified resource.
        *
        * @param  \App\stat  $stat
        * @return \Illuminate\Http\Response
        */
        /**
        * Show the form for editing the specified resource.
        *
        * @param  \App\stat  $stat
        * @return \Illuminate\Http\Response
        */
        public function edit(stat $stat)
        {
          //
        }

        /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  \App\stat  $stat
        * @return \Illuminate\Http\Response
        */
        public function update(Request $request, stat $stat)
        {
          //
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  \App\stat  $stat
        * @return \Illuminate\Http\Response
        */
        public function destroy(stat $stat)
        {
          //
        }

public function oinstaladores()
{
    {
        return user::select(DB::raw('*, (select count(*) from oinstalls where oinstalls.installer = users.id_user and status_pgo = 1) as installs'))->where('installer', '=', '1')->get();
    }
}

public function oinstalador($id)
{
    $installs = oinstall::where('installer', '=', $id)->get();
    $pagos = oinstallpgo::where('installer', '=', $id)->get();
    $index = collect(['instalaciones'=>$installs,'pagos'=>$pagos]);


    return response()->json($index); //lo devuelvo via rest los soportes de determinado usuario

}

    public function user_comision()
    {
        return user::select(DB::raw('*, (select count(*) from servicios where servicios.user_comision_serv = users.id_user ) as installs'))->where('comision', '=', '1')->get();
    }



    public function fac_comision(Request $request, $id)
    {
        /*return DB::table('fac_controls')
        ->select('fac_controls.*', 'clientes.nombre', 'clientes.apellido', 'clientes.social')
        ->join('clientes','clientes.id', '=', 'fac_controls.id_cliente')
        ->orderBy('fac_controls.created_at','DSC')
        ->get();*/

        /*(select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
        (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
        (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
        (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address
        */
        if(isset($request->year)&&$request->year!=''){
            $year=$request->year;
        }else{
            $year=date('Y');
        }
        if(isset($request->month)&&$request->month!=''){
            $month=$request->month;
        }else{
            $month=date('n');
        }
        $facturas=DB::table('fac_controls')
            ->select(\DB::raw('fac_controls.*, servicios.user_comision_serv, servicios.porcentaje_comision_serv, servicios.id_srv'))
            ->join('clientes', 'clientes.id', '=', 'fac_controls.id_cliente')
            ->join('servicios', 'servicios.id_srv', '=', 'fac_controls.fac_serv')
            ->where('servicios.user_comision_serv', $id)
            ->whereRaw("MONTH(fac_controls.created_at) = $month and YEAR(fac_controls.created_at) = $year")
            ->orderBy('created_at', 'DESC');
        // if(isset($request->status)&&$request->status=='false'){
        //   $facturas->where('fac_status', '1');
        // }
        if(isset($request->fac)&&$request->fac=='fac'){
            $facturas->whereRaw('fac_num IS NOT NULL')->where('fac_status', '1');
        }elseif (isset($request->fac)&&$request->fac=='nfac') {
            $facturas->whereRaw('fac_num IS NULL')->where('fac_status', '1');
        }elseif (isset($request->fac)&&$request->fac=='null') {
            $facturas->where('fac_status', '2');
        }
        $facturas=$facturas->get();
        foreach ($facturas as $fac) {
            $cliente=DB::select("SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
            $monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
            $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
            $deuda = $monto-$pagado;
            $fac->cliente=$cliente;
            $fac->monto=$monto;
            $fac->pagado=$pagado;
            $fac->deuda=$deuda;
            // $fac->cliente=DB::select("SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
            // $fac->monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
            // $fac->pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
            if($fac->monto>$fac->pagado){
                $fac->estado="pendiente";
            }else if ($fac->monto<=$fac->pagado){
                $fac->estado="pagado";
            }
        }
        return $facturas;
        /*return DB::select(
          "SELECT fac_controls.*,
          (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
          (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
          (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
          from fac_controls where MONTH(fac_controls.created_at) = ".$month." and YEAR(fac_controls.created_at) = ".$year." ORDER BY created_at DESC ;");*/
    }

    public function fac_comision_montos(Request $request, $id)

    {
   if(isset($request->year)&&$request->year!=''){
        $year=$request->year;
      }else{
    $year=date('Y');
}
if(isset($request->month)&&$request->month!=''){
    $month=$request->month;
}else{
    $month=date('n');
}
        $status=[];
        $cliente= DB::table('clientes')
            ->select(\DB::raw('clientes.id, CONCAT(clientes.nombre,clientes.apellido) AS nombre_completo, servicios.user_comision_serv , servicios.porcentaje_comision_serv, servicios.id_srv '))
            ->join('servicios', 'servicios.cliente_srv', '=', 'clientes.id')
            ->where('servicios.user_comision_serv', $id)
            ->get();


        foreach ($cliente as $client) {
            $usuario= $client->user_comision_serv;
            $comision= $client->porcentaje_comision_serv;
            $id_client= $client->id;
            $facturas = fac_control::whereRaw("MONTH(fac_controls.created_at) = $month and YEAR(fac_controls.created_at) = $year")->where('fac_controls.fac_serv', $client->id_srv)->where('fac_controls.fac_status', '=','1')->orderBy('created_at', 'DESC');
// if(isset($request->status)&&$request->status=='false'){
//   $facturas->where('fac_status', '1');
// }
           /* if (isset($request->fac) && $request->fac == 'fac') {
                $facturas->whereRaw('fac_num IS NOT NULL')->where('fac_status', '1');
            } elseif (isset($request->fac) && $request->fac == 'nfac') {
                $facturas->whereRaw('fac_num IS NULL')->where('fac_status', '1');
            } elseif (isset($request->fac) && $request->fac == 'null') {
                $facturas->where('fac_status', '2');
            }*/
            $facturas = $facturas->get();
            foreach ($facturas as $fac) {
                $cliente = DB::select("SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
                $monto = DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
                $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
                $deuda = $monto - $pagado;
                $fac->cliente = $cliente;
                $fac->monto = $monto;
                $fac->pagado = $pagado;
                $fac->deuda = $deuda;
                $factura_num = $fac->id;
                $denominacion = $fac->denominacion;
                $f_month = $month;
                $f_year = $year;
                // $fac->cliente=DB::select("SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
                // $fac->monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
                // $fac->pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
               /* if ($fac->monto > $fac->pagado) {
                    $fac->estado = "pendiente";
                } else if ($fac->monto <= $fac->pagado) {
                    $fac->estado = "pagado";
                }*/



        array_push($status, ['pagado'=>round($pagado, 2), 'facturado'=>round($monto, 2), 'id_cliente'=>$id_client,'nombre_completo'=>$cliente,'usuario'=>$usuario, 'comision'=>$comision,'denominacion'=>$denominacion,'numero_factura'=>  $factura_num, 'month' => $f_month, 'year' => $f_year ]);
            }

}


return collect(['datos'=>$status]);
}
    public function report_pago(Request $request){

        $usuarios=User::where('id_user', '=', $request->user_responsable)
            ->first();
if($request->tipo_pago_comision == 1){

    $denominacion='Bs.S';
} else if($request->tipo_pago_comision == 2){

    $denominacion='$';
}
$mes = $request->month;
        switch ($mes) {

            case '1':
                $mes = 'Enero';
                break;
            case '2':
                $mes = 'Febrero';
                break;
            case '3':
                $mes = 'Marzo';
                break;
            case '4':
                $mes = 'Abril';
                break;
            case '5':
                $mes = 'Mayo';
                break;
            case '6':
                $mes = 'Junio';
                break;
            case '7':
                $mes = 'Julio';
                break;
            case '8':
                $mes = 'Agosto';
                break;
            case '9':
                $mes = 'Septiembre';
                break;
            case '10':
                $mes = 'Octubre';
                break;
            case '11':
                $mes = 'Noviembre';
                break;
            case '12':
                $mes = 'Diciembre';
                break;


        }
        historico_comision::create(['comentario'=>'Reporte de pago por un monto de: '.$request->monto_comision.' '.$denominacion.' para el perido: '.$mes.'-'.$request->year. ' por '.$usuarios->nombre_user.' '.$usuarios->apellido_user. '', 'user_comision'=>$request->user_comision, 'user_responsable'=>$request->user_responsable]);
    return pagos_comisiones::create($request->all());
    }
    public function history_pago($id){

        $historico = DB::table('historico_comisions')
            ->select(\DB::raw('historico_comisions.*'))
            ->where('historico_comisions.user_comision', $id)
            ->get();

        return $historico;
    }
}