<?php

namespace App\Http\Controllers;

use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_prog;
use App\planes;
use DateTime;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\fac_control;
use App\fac_pago;
use App\fac_product;
use App\clientes;
use App\historico_cliente;
use App\historico;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Console\Commands;
use App\configuracion;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mail;
use PDF;
use Jenssegers\Date\Date;

class FacControlController extends Controller
{
    public function index(Request $request,$id)
    {
        
        $permisoSeniat = DB::select("SELECT * FROM permissions WHERE user = ? AND perm = 'seniat'",[$id]);

        if (count($permisoSeniat) > 0) {
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
            //$facturas=fac_control::whereRaw("MONTH(fac_controls.created_at) = $month and YEAR(fac_controls.created_at) = $year")->orderBy('created_at', 'DESC');
            

            $facturas = DB::select("SELECT f.* FROM fac_controls as f
                                        INNER JOIN clientes as c ON f.id_cliente = c.id
                                            WHERE MONTH(f.created_at) = $month AND YEAR(f.created_at) = $year AND c.serie = 1
                                                ORDER BY f.created_at DESC");

            if(isset($request->fac)&&$request->fac=='fac'){
                $facturas->whereRaw('fac_num IS NOT NULL')->where('fac_status', '1');
            }elseif (isset($request->fac)&&$request->fac=='nfac') {
                $facturas->whereRaw('fac_num IS NULL')->where('fac_status', '1');
            }elseif (isset($request->fac)&&$request->fac=='null') {
                $facturas->where('fac_status', '2');
    
            }elseif (isset($request->fac)&&$request->fac=='bs') {
                $facturas->where('denominacion', '!=','$');
                
    
            }elseif (isset($request->fac)&&$request->fac=='dol') {
                $facturas->where('denominacion', '$');
            }
            foreach ($facturas as $fac) {
    
                $cliente=DB::select( 'SELECT * FROM clientes WHERE id = ?',[$fac->id_cliente]); 
                foreach ($cliente as $cli) {
                  
                    if($cli->kind == "J" || $cli->kind == "V-"  || $cli->kind == "G" ){
                        $fac->cliente = $cli->social;
                    }else{
                        $fac->cliente=$cli->nombre." ".$cliente[0]->apellido;
                    }
                    $fac->dni=$cli->dni;
                }
                                    //"SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
                $dni=DB::select('SELECT * FROM clientes WHERE id = ?',[$fac->id_cliente]);
                                 //"SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'dni'")[0]->value;
                $monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
                $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
                $deuda = $monto-$pagado; 
                $fac->monto=$monto;
                $fac->pagado=$pagado;
                $fac->deuda=$deuda;
               
                if($fac->monto>$fac->pagado){
                    $fac->estado="pendiente";
                }else if ($fac->monto<=$fac->pagado){
                    $fac->estado="pagado";
                }
            }
            
        } else {

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
            $facturas=fac_control::whereRaw("MONTH(fac_controls.created_at) = $month and YEAR(fac_controls.created_at) = $year")->orderBy('created_at', 'DESC');
            
            if(isset($request->fac)&&$request->fac=='fac'){
                $facturas->whereRaw('fac_num IS NOT NULL')->where('fac_status', '1');
            }elseif (isset($request->fac)&&$request->fac=='nfac') {
                $facturas->whereRaw('fac_num IS NULL')->where('fac_status', '1');
            }elseif (isset($request->fac)&&$request->fac=='null') {
                $facturas->where('fac_status', '2');
    
            }elseif (isset($request->fac)&&$request->fac=='bs') {
                $facturas->where('denominacion', '!=','$');
                
    
            }elseif (isset($request->fac)&&$request->fac=='dol') {
                $facturas->where('denominacion', '$');
            }
            $facturas=$facturas->get();
            foreach ($facturas as $fac) {
    
                $cliente=DB::select( 'SELECT * FROM clientes WHERE id = ?',[$fac->id_cliente]); 
                foreach ($cliente as $cli) {
                  
                    if($cli->kind == "J" || $cli->kind == "V-"  || $cli->kind == "G" ){
                        $fac->cliente = $cli->social;
                    }else{
                        $fac->cliente=$cli->nombre." ".$cliente[0]->apellido;
                    }
                    $fac->dni=$cli->dni;
                }
                                    //"SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
                $dni=DB::select('SELECT * FROM clientes WHERE id = ?',[$fac->id_cliente]);
                                 //"SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'dni'")[0]->value;
                $monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
                $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
                $deuda = $monto-$pagado; 
                $fac->monto=$monto;
                $fac->pagado=$pagado;
                $fac->deuda=$deuda;
               
                if($fac->monto>$fac->pagado){
                    $fac->estado="pendiente";
                }else if ($fac->monto<=$fac->pagado){
                    $fac->estado="pagado";
                }
            }
            

        }

        return $facturas;

    }

    public function show($id)
    {
        //return fac_control::find($id);
        return DB::select(
            "SELECT fac_controls.*,
         (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
         (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
         where fac_controls.id_cliente = ".$id." ORDER BY created_at DESC LIMIT 3 ;");
    }
    public function fac_cliente($id)
    {
        //return fac_control::find($id);
        return DB::select(
            "SELECT fac_controls.*,
         (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
         (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
         where fac_controls.id = ".$id." ORDER BY created_at DESC LIMIT 3 ;");
    }
    public function detail($id)
    {
        //return fac_control::find($id);
        return DB::select(
            "SELECT fac_controls.*,
         (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
         (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
         (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
         where fac_controls.id = ".$id.";");
    }

    public function facmail(Request $request, $id){
        $responsable = $request->responsable;
        unset($request['responsable']);
        $factura = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id = ".$id.";");
        $factura = $factura[0];
        $productos = DB::table('fac_products')
            ->select('*')
            ->orderBy('precio_articulo','DSC')
            ->where('codigo_factura', '=', $id)
            ->get();

        $cliente=clientes::where('id','=',$factura->id_cliente)->first();

        $monto = $factura->monto;
        $iva = $productos[0]->IVA;
        $impuesto = 0;
        $montosi = 0;
        if($cliente->serie=='1'){
            $impuesto = (($monto/($iva+100))*$iva);
            $montosi = $monto-$impuesto;
        }elseif ($cliente->serie=='0') {
            $montosi = $monto;
        }
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }

        historico::create(['responsable'=>$responsable, 'modulo'=>'Facturacion', 'detalle'=>'Envio por correo la factura '.$id.' para el cliente '.$cli]);


        /* Mail::send('emails.factura', [
        'factura' => $factura,
        'productos' => $productos,
        'cliente'=>$cliente,
        'impuesto'=>$impuesto,
        'monto'=>$monto,
        'montosi'=>$montosi,
        'iva'=>$iva],function ($message) use ($id, $factura, $productos, $cliente, $impuesto, $monto, $montosi, $iva)*/
        Mail::send('emails.Cuerpo', [
            'cliente' => ucwords($factura->cliente),
            'fecha'=>date('d-m-Y', strtotime($factura->created_at)),
            'moroso'=>$cliente],function ($message) use ($id, $factura, $productos, $cliente, $impuesto, $monto, $montosi, $iva)
        {

            //$productos = $productos[0];


            $data = PDF::loadView('emails.factura', [
                'factura' => $factura,
                'productos' => $productos,
                'cliente'=>$cliente,
                'impuesto'=>$impuesto,
                'monto'=>$monto,
                'montosi'=>$montosi,
                'iva'=>$iva])
                ->setPaper([0, 0, 595.276, 447.874])->setWarnings(false)->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->stream();
            $message->attachData($data, 'Recibo Maraveca-'.ucwords($factura->cliente).'('.date('d-m-Y', strtotime($factura->created_at)).').pdf');
            $message->subject('Recibo Maraveca-'.ucwords($factura->cliente).'('.date('d-m-Y', strtotime($factura->created_at)).')');
            $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Mail Automatico)');
            $message->to($cliente->email);

        });


        return response()->json(['message' => 'Request completed']);



    }

    public function store(Request $request)
    {
        return fac_control::create($request->all());
    }

    public function generate(Request $request)
    {
        
        $cliente=clientes::where('id','=',$request->cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        if ($request->pro ==3){

            $date= DateTime::createFromFormat('d/m/Y',  $request->fecha)->format('Y-m-d');
            $nservicos= json_encode($request->nservicio);

            fac_prog::create( ['id_cliente' => $request->cliente, 'fecha'=>$date, 'pro'=>$request->pro, 'id_servicio'=>$nservicos,'responsable'=>$request->responsable, 'contador' => 0, 'status'=>1]);

            historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Programación de factura para la fecha'.$request->fecha.', al cliente '.$cli]);

            historico_cliente::create(['history'=>'Programación de factura para la fecha '.$request->fecha, 'modulo'=>'Facturacion', 'cliente'=>$request->cliente, 'responsable'=>$request->responsable]);

        }
        if($request->pro == 1 || $request->pro == 2 ){

            
            historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Generacion de nueva factura para el cliente '.$cli]);

            \Artisan::call('factura:generar', [
                'cliente' => $request->cliente, 'fecha'=>$request->fecha,'fecha2'=>$request->fecha2, 'pro'=>$request->pro, 'nro_servicio'=>$request->nservicio, 'responsable'=>$request->responsable
            ]);
            
            return response()->json($request);
        }
        if($request->pro == 4){
            
            \Artisan::call('factura_promocion', [
                'cliente' => $request->cliente, 'fecha'=>$request->fecha,'fecha2'=>$request->fecha2, 'pro'=>$request->pro, 'nro_servicio'=>$request->nservicio, 'responsable'=>$request->responsable
            ]);

            historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Generacion de nueva factura de Promocion para el cliente '.$cli]);
            
           
            return  response()->json($request);
        }
        
    }
    
    public function generate_fac_blanco(Request $request)
    {

        $cliente=clientes::where('id','=',$request->cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }

        historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Generacion de nueva factura para el cliente '.$cli]);

        \Artisan::call('factura_blanco:generar', [
            'cliente' => $request->cliente, 'fecha'=>$request->fecha, 'pro'=>$request->pro, 'denominacion'=>$request->denominacion, 'serie'=>$request->serie, 'responsable'=>$request->responsable
        ]);
        return 200;
    }
    public function notificar(Request $request)
    {
        //return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8/*este ultimo numero es el limite*/);
        historico::create(['responsable'=>$request->responsable, 'modulo'=>'Facturacion', 'detalle'=>'Envio notificacion masiva general de recordatorio de pago']);
        \Artisan::call('Mensajes:manuales');
        return 200;
    }

    public function update(Request $request, $id)
    {
        $fac = fac_control::findOrFail($id);
        $fac->update($request->all());

        return $fac;
        //->update(['contador'=>$moroso->contador+=1]);
    }


    public function anular(Request $request, $id)
    {


        
        $fac_p= fac_pago::where('fac_id', $id);
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
        $fac = fac_control::findOrFail($id);
        $fac->update(['fac_status'=>'2']); // aqui cambio el estatus de la factura de 1 a 2 (activa a anulada)

        $responsable = $request["id_user"];
        $cliente=clientes::where('id','=',$fac->id_cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }

        $anulacion = DB::insert("INSERT INTO anulacion_factura(id_factura, razon_anulacion,id_responsable) VALUES (?,?,?)",[$id,$request["razon"],$request["id_user"]]);
        historico::create(['responsable'=>$responsable, 'modulo'=>'Facturacion', 'detalle'=>'Anulacion de factura Nº '.$id.' para el cliente '.$cli]);

        historico_cliente::create(['history'=>'Anulacion de factura Nº '.$id, 'modulo'=>'Facturacion', 'cliente'=>$fac->id_cliente, 'responsable'=>$responsable]);
        $fac_p->delete();

     revisar_Balances ($fac->id_cliente);
     
     return response()->json($request);
    }

    public function ComprobarAnulacion(Request $request){

        $comprobacion = DB::select("SELECT * FROM anulacion_factura WHERE id_factura = ?",[$request["id"]]); 

        $comprobacion != [] ? $comprobacion["existe"] = 1 : $comprobacion["existe"] = 0;

        return response()->json($comprobacion);
    }



    public function anular_prog(Request $request, $id)
    {
        $fac = fac_prog::findOrFail($id);
        $fac->update(['status'=>'3']);

        $responsable = $request[1];
        $cliente=clientes::where('id','=',$fac->id_cliente)->first();
        if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            $cli= ucwords(strtolower($cliente->social));
        }else {
            $cli= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        }
        historico::create(['responsable'=>$responsable, 'modulo'=>'Facturacion', 'detalle'=>'Anulacion de progrmacion de factura Nº '.$id.' para el cliente '.$cli]);

        historico_cliente::create(['history'=>'Anulacion de programacion de factura', 'modulo'=>'Facturacion', 'cliente'=>$fac->id_cliente, 'responsable'=>$responsable]);

        return 200;
    }

    public function delete(Request $request, $id)
    {
        $fac = fac_control::findOrFail($id);
        $fac->delete();

        return 204;
    }






    public function update_pricefac(Request $request)
    {






        //   $tasa=configuracion::where('nombre','=','taza')->first()->valor;


        // $clientes=DB::table('clientes')->get();
        // foreach ($clientes as $cliente) {
        //$facturas=$facturas->get();
        /* $facturas=DB::select(
           "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT round(SUM(fac_pagos.pag_monto), 2) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado
      from fac_controls where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion != '$' and fac_controls.serie_fac = 0 ");
*/
        $facturas =DB::select("SELECT fac_controls.* ,
    (SELECT round(SUM(fac_products.precio_articulo), 2)from fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
                (SELECT round(SUM(fac_pagos.pag_monto), 2) from fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
                (SELECT round(SUM(fac_products.precio_articulo), 2) from fac_products where fac_controls.id = fac_products.codigo_factura) - (SELECT round(SUM(fac_pagos.pag_monto), 2) from fac_pagos where fac_controls.id = fac_pagos.fac_id) as deuda
                from fac_controls where fac_controls.serie_fac =0 and fac_controls.denominacion != '$' and MONTH(fac_controls.created_at) = MONTH(CURRENT_DATE()) AND YEAR(fac_controls.created_at) = YEAR(CURRENT_DATE())");


        foreach ($facturas as $fac) {
            /* $cliente=DB::select("SELECT fac_dets.value from fac_dets where fac_dets.fac_id = $fac->id and fac_dets.detail = 'cliente'")[0]->value;
             $monto=DB::select("SELECT round(SUM(fac_products.precio_articulo), 2) as monto from  fac_products where $fac->id = fac_products.codigo_factura")[0]->monto;
             $pagado = DB::select("SELECT round(SUM(fac_pagos.pag_monto), 2) as pagado from  fac_pagos where $fac->id = fac_pagos.fac_id")[0]->pagado;
    $deuda = $monto-$pagado;
      $fac->cliente=$cliente;
      $fac->monto=$monto;
      $fac->pagado=$pagado;*/
            //$deuda= $fac->deuda;
            $monto =  $fac->monto;
            $pagado = $fac->pagado;
            $deuda = $monto-$pagado;
            $facpro = fac_product::where('codigo_factura', '=',$fac->id ) ->get();
            if($fac->monto > $fac->pagado){
                //$fac->estado="pendiente";
                /* $rever = round(($fac->monto/$tasa), 2);
                 $montoN= round(($rever*$request->taza), 2);
                // $monto->update(['precio_articulo'=>$montoN]);
                 $t=fac_product::where('codigo_factura', '=', $fac->id )->update(['precio_articulo'=>$montoN]);*/

                foreach ( $facpro as $facp) {
                    $montoN = round(($facp->precio_dl*$request->taza),2);
                    // $facpro = fac_product::where('codigo_factura', '=',$fac->id );
                    $facp-> update(['precio_articulo'=>$montoN]);
                    $facp-> update(['comment_articulo'=>'Monto ajustado según tasa del día'.' '.  Date::now()->format('l j F Y')]);

                }
            }

        }

        $configuracion = configuracion::where('nombre', '=', "taza");

        $configuracion->update(["valor" => $request->taza]);
        $planes = planes::all();
        foreach ($planes as $plan) {
            if ($plan->taza != null && $plan->taza > 0) {
                $montoNP = $plan->taza * $request->taza;
                $planes = planes::where('id_plan', '=', $plan->id_plan);
                $planes->update(["cost_plan" => $montoNP]);
            }
        }

        return 200;
    }
    //
}
