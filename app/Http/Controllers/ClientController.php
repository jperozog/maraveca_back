<?php

namespace App\Http\Controllers;
use App\fac_product;
use App\Http\Controllers\Controller;
use App\planes;
use Illuminate\Support\Facades\Response;
//use App\Helpers\sendsms as Helper;
//use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Session;
use App\soportes;
use App\servicios;
use App\clientes;
use App\historico_cliente;
use App\historico;
use App\configuracion;
use App\balance_cliente;
use App\balance_clientes_in;
use Mail;
use PDF;


class ClientController extends Controller
{
    public function login(Request $request){
        $request->session()->forget('dni');

        return view('login');
    }

    public function loginprocess(Request $request){

        $kind = $request->get('kind');
        $dni = $request->get('dni');
        $password = $request->get('password');
        $checkuser =  clientes::selectRaw("Count(*) as Total")->where('dni', '=', $dni)->first();


        if(intval($checkuser->Total) > 0){
            //esto es correcto en uno

            $getpassword = DB::table('clientes')->where('kind', '=', $kind)->where('dni', '=', $dni)->where('password', '=', $password)->get();
            $get = $getpassword->isNotEmpty();

            if($get == true){

                $request->session()->put('dni', $getpassword->first());

                return redirect('/clientesover/');

            }else{
                return redirect('login')->withErrors(['Creendenciales erroneas']);
            }
        }else{
            return redirect('login')->withErrors(['Creendenciales erroneas']);
        }
    }

    function walk($val, $key, $new_array){
        $nums = explode('-',$val);
        $bancos[$nums[0]] = $nums[1];
    }

    public function downloadPDF(Request $request)
    {
        //return $request->factura;
        $factura = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id = ".$request->factura.";");
        $factura = $factura[0];
        $productos = DB::table('fac_products')
            ->select('*')
            ->orderBy('precio_articulo','DSC')
            ->where('codigo_factura', '=', $request->factura)
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

        return PDF::loadView('emails.factura', [
            'factura' => $factura,
            'productos' => $productos,
            'cliente'=>$cliente,
            'impuesto'=>$impuesto,
            'monto'=>$monto,
            'montosi'=>$montosi,
            'iva'=>$iva])
            ->setPaper([0, 0, 595.276, 447.874])
            ->setWarnings(false)
            ->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            //->stream()
            ->download('Recibo Maraveca-'.ucwords($factura->cliente).'('.date('d-m-Y', strtotime($factura->created_at)).').pdf');
        //return $data;

    }

    public function overview(Request $request)
    {

        // $cliente = $request->session()->get('dni');
        // return response()->json($cliente);
        $balanceo=0;
        $balanceoB=0;
        $balanceoD=0;
        $afavor=0;
        $afavord=0;
        $afavorb=0;
        $cliente = $request->session()->get('dni');

        $tasa=configuracion::where('nombre','=','taza')->first()->valor;


        $bancos_pre=configuracion::where('nombre', 'banco')->get()->first()->valor;
        $chunks = array_chunk(preg_split('/(-|,|:|;|,)/', $bancos_pre), 5);
        $bancos=array();
        //return  $chunks;
        foreach ($chunks as $key => $value) {
            $bancos[$key]=['banco'=>$value[0], 'numero'=>$value[1], 'perm'=>$value[2], 'titular'=>$value[3], 'dni'=>$value[4]];
        }


        //facturacion total del cliente
        $facturacion = DB::select(
            "SELECT fac_controls.*, 
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 ORDER BY created_at DESC limit 3;");

        $facturacionB = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = 'Bs.S' ORDER BY created_at DESC limit 3;");
        $facturacionD = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = '$' ORDER BY created_at DESC limit 3;");
        //servicios contratos por el cliente
        $servicios=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan', 'planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->get();
        $serviciosB=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan', 'planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '!=','3' )
            ->get();

        $serviciosD=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan', 'planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '=','3' )
            ->get();



        foreach ($facturacion as $fac) {
            $bal=$fac->pagado-$fac->monto;
            $fac->bal=$bal;
            if($bal!=0){
                $balanceo+=$bal;
            }

        }
        $balanceo= $balanceo*-1;
        $balance=balance_cliente::where('bal_cli', '=', $cliente->id)->get();




        foreach ($balance as $bal) {
            if($bal->bal_stat == '1'){
                if($bal->bal_rest>0){
                    $afavor+=$bal->bal_rest;
                }
            }
        }
        //solo para palanes en bolivares
        foreach ($facturacionB as $facb) {
            $balb=$facb->pagado-$facb->monto;
            $facb->bal=$balb;
            if($balb!=0){
                $balanceoB+=$balb;
            }



        }
        $balanceoB= $balanceoB*-1;
        $balanceb=balance_cliente::where('bal_cli', '=', $cliente->id)->get();

        foreach ($balanceb as $balb) {
            if($balb->bal_stat == '1'){
                if($balb->bal_rest>0){
                    $afavorb+=$balb->bal_rest;
                }
            }
        }
        //fin planes en bs

        //inicio de calculo de facturacion para planes en dolares

        foreach ($facturacionD as $facd) {
            $bald=$facd->pagado-$facd->monto;
            $facd->bal=$bald;
            if($bald!=0){
                $balanceoD+=$bald;
            }
        }
        $balanceoD= $balanceoD*-1;
        $balance_in=balance_clientes_in::where('bal_cli_in', '=', $cliente->id)->get();

        foreach ($balance_in as $bald) {
            if($bald->bal_stat_in == '1'){
                if($bald->bal_rest_in>0){
                    $afavord+=$bald->bal_rest_in;
                }
            }
        }
        $total_mensual=0;
        foreach ($servicios as $plan) {
            $total_mensual+=$plan->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensual=$total_mensual*1.16;
        }
        $total_mensualB=0;
        foreach ($serviciosB as $planb) {
            $total_mensualB+=$planb->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensualB=$total_mensualB*1.16;
        }
        $total_mensualD=0;
        foreach ($serviciosD as $pland) {
            $total_mensualD+=$pland->cost_plan;
        }
// fin de calculo de planes en dolares

        $soportes=DB::table('soportes')
            ->select('soportes.*', 'users.nombre_user', 'users.apellido_user')
            ->join('servicios','servicios.id_srv','=','soportes.servicio_soporte')
            ->join('clientes','servicios.cliente_srv','=','clientes.id')
            ->join('users','users.id_user','=','soportes.user_soporte')
            ->where('clientes.id', '=', $cliente->id)
            ->where('tipo_soporte', '=', '2')
            ->where('status_soporte', '=', '2')
            ->orderBy('soportes.created_at', 'DSC')
            ->get();
        foreach($soportes as $item){
            $problemas=[];
            $problems = DB::table('ticket_problems')
                ->select('problem_pb')
                ->where('ticket_pb', '=', $item->id_soporte)
                ->get();
            foreach ($problems as $key) {
                array_push($problemas, $key->problem_pb);
            }
            $item->problems = implode(', ', $problemas);
        };
        $historial=historico_cliente::where('cliente', $cliente->id)
            ->select('historico_clientes.*', 'users.nombre_user', 'users.apellido_user')
            ->leftjoin('users','users.id_user','=','responsable')
            ->orderBy('created_at', 'DSC')
            ->limit('3')
            ->get();

        $listaclientes = DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan','planes.tipo_plan')
            ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
            ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            ->get();
        $pagosp =DB::table('balance_clientes')
            ->where('bal_stat', '=', '2')
            ->where('bal_cli', '=', $cliente->id)
            ->get();

        $pagosp_in =DB::table('balance_clientes_ins')
            ->where('bal_stat_in', '=', '2')
            ->where('bal_cli_in', '=', $cliente->id)
            ->get();
        Date::setLocale('es');

        $fecha_tasa = Date::now()->format(' j \\d\\e F \\d\\e Y');
        $hora_tasa = Date::now()->format('  h:i A');

      /*  if(( count($facturacion) <= 0) ||  (count($facturacionB) <= 0)||  (count($facturacionD) <= 0)) {

        $sin_fac = 1;
        }
        else{
            $sin_fac = 0;
        }*/
        if($cliente->tipo_planes == 3 ){
            return view('clientover',
                ['cliente'=>$cliente,
                    'facturacion'=>$facturacionD,
                    'servicios'=>$servicios,
                    'serviciosB'=>$serviciosB,
                    'serviciosD'=>$serviciosD,
                    'balance'=>$balance,
                    'total_mensual'=>$total_mensual,
                    'total_mensualD'=>$total_mensualD,
                    'total_mensualB'=>$total_mensualB,
                    'tasa'=>$tasa,
                    'soportes' => $soportes,
                    'historial' => $historial,
                    'balanceo' =>$balanceo,
                    'balanceoB' =>$balanceoB,
                    'balanceoD' =>$balanceoD,
                    'afavor' =>$afavor,
                    'afavord' =>$afavord,

                    'bancos' => $bancos,
                    'pagosp' => $pagosp,
                    'pagosp_in' => $pagosp_in,
                   // 'sin_fac' => $sin_fac
                ]);
        } elseif (  ($cliente->tipo_planes == 1 && $cliente->serie ==0) ||  ($cliente->tipo_planes == 4 && $cliente->serie ==0) ){

            return view('clientoverb',
                ['cliente'=>$cliente,
                    'facturacion'=>$facturacionD,
                    'servicios'=>$servicios,
                    'serviciosB'=>$serviciosB,
                    'serviciosD'=>$serviciosD,
                    'balance'=>$balance_in,
                    'total_mensual'=>$total_mensual,
                    'total_mensualD'=>$total_mensualD,
                    'total_mensualB'=>$total_mensualB,
                    'tasa'=>$tasa,
                    'fecha_tasa' => $fecha_tasa,
                    'hora_tasa' => $hora_tasa,
                    'soportes' => $soportes,
                    'historial' => $historial,
                    'balanceo' =>$balanceo,
                    'balanceoB' =>$balanceoB,
                    'balanceoD' =>$balanceoD,
                    'afavor' =>$afavor,
                    'bancos' => $bancos,
                    'pagosp' => $pagosp,
                   // 'sin_fac' => $sin_fac,
                    'balanceoD' =>$balanceoD,
                    'afavord' =>$afavord,

                ]);
        } elseif (  $cliente->serie ==1  ){

            return view('clientoverb_fiscales',
                ['cliente'=>$cliente,
                    'facturacion'=>$facturacionB,
                    'servicios'=>$servicios,
                    'serviciosB'=>$serviciosB,
                    'serviciosD'=>$serviciosD,
                    'balance'=>$balance,
                    'total_mensual'=>$total_mensual,
                    'total_mensualD'=>$total_mensualD,
                    'total_mensualB'=>$total_mensualB,
                    'tasa'=>$tasa,
                    'fecha_tasa' => $fecha_tasa,
                    'hora_tasa' => $hora_tasa,
                    'soportes' => $soportes,
                    'historial' => $historial,
                    'balanceo' =>$balanceo,
                    'balanceoB' =>$balanceoB,
                    'balanceoD' =>$balanceoD,
                    'afavor' =>$afavor,
                    'bancos' => $bancos,
                    'pagosp' => $pagosp,
                   // 'sin_fac' => $sin_fac
                ]);
        }

        elseif ( $cliente->tipo_planes == 2 ||  $cliente->tipo_planes == 5){

            return view('clientoverd',
                ['cliente'=>$cliente,
                    'facturacion'=>$facturacionD,
                    'servicios'=>$servicios,
                    'serviciosB'=>$serviciosB,
                    'serviciosD'=>$serviciosD,
                    'balance'=>$balance,
                    'total_mensual'=>$total_mensual,
                    'total_mensualD'=>$total_mensualD,
                    'total_mensualB'=>$total_mensualB,
                    'afavord' =>$afavord,
                    'tasa'=>$tasa,
                    'soportes' => $soportes,
                    'historial' => $historial,
                    'balanceo' =>$balanceo,
                    'balanceoB' =>$balanceoB,
                    'balanceoD' =>$balanceoD,
                    'afavor' =>$afavor,
                    'bancos' => $bancos,
                    'pagosp_in' => $pagosp_in
                ]);
        }



    }
    public function logout(Request $request)
    {
        $request->session()->forget('dni');

        return redirect('login');
    }

    public function register(){
        return view('register');
    }

    public function register1(Request $request){

        $kind = $request->get('kind');
        $dni = $request->get('dni');

        $search = DB::table('clientes')->where('kind', '=', $kind)->where('dni', '=', $dni)->get()->first();

        if(empty($search)){
            $respuesta = "Usted no se encuentra registrado en nuestro portal";
            return view('register.error2',['respuesta'=> $respuesta]);
        }elseif($search->password == null){
            return view('register.emailclient',['search'=>$search]);
        }else{
            $respuesta =  "Cliente ya registrado. Si desconoce esta operacion por favor contactar al telefono 0261-7725180";
            return view('register.error2',['respuesta'=> $respuesta]);
        }
    }

    public function emailclient(Request $request){
        $id = $request->get('id');
        $email = $request->get('email');

        $verify = DB::table('clientes')->where('id', '=', $id)->where('email', '=', $email)->get()->first();

        if(empty($verify)){
            $respuesta ='El correo ingresado no coincide con el de nuestro sistema.';
            return view('register.error2', ['respuesta'=>$respuesta]);
        }else{
            $cliente = Clientes::find($verify->id);
            $cliente->password = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
            $cliente->abonado = '1';
            $cliente->save();

            Mail::send('register.TestRegister', ['cliente'=>$cliente], function ($message) use ($verify) {
                $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
                $message->to($verify->email);
                //$message->to('henryaog@hotmail.com');
                //$message->bcc('henry.orono@maraveca.com');
                $message->bcc('hector.diaz@maraveca.com');
                //$message->bcc('henryaog@hotmail.com');
                //$message->bcc('haorono@gmail.com');
                //$message->bcc('gerencia@maraveca.com');
                //$message->bcc('ramon.velazquez@maraveca.com');
                //$message->bcc('ana.reyes@maraveca.com');
                $message->subject('Correo de registro');
                $message->priority(3);
            });
            historico_cliente::create(['history'=>'Solicitud de registro', 'modulo'=>'Mi ventana', 'cliente'=>$verify->id, 'responsable'=>'-1']);
            return redirect('login')->withSuccess('Correo enviado(revisar la bandeja de spam o correo no deseado)');
        }
    }

    public function changepassword($id){
        $password= substr($id, -6);
        $id=substr($id,0, -6);
        $cliente = Clientes::find($id);
        //echo $id;
        if($cliente && $cliente->password == $password){
            return view('register.createclient', ['cliente' => $cliente, 'id' => $id]);
        }else {
            return view('register.changepass');
        }
    }

    public function registerf(Request $request){
        if(empty($request)){
            echo 'Return';
        }else{
            $password = $request->get('password');
            $cpassword = $request->get('cpassword');
            $id = $request->get('id');
            if($password == $cpassword){
                $cliente = Clientes::find($id);
                $cliente->password = $password;
                $cliente->abonado = null;
                $cliente->save();
                historico_cliente::create(['history'=>'Cliente registrado', 'modulo'=>'Mi ventana', 'cliente'=>$id, 'responsable'=>'-1']);
                return redirect('login')->withSuccess('Cliente registrado');
            }else{
                $respuesta = 'las contraseñas no son iguales';
                return view('register.errorpass',['respuesta' => $respuesta]) ;
            }
        }
    }

    public function facturacionc(Request $request){
        $factura = DB::select(
            "SELECT fac_controls.*,
    (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
    (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
    (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
    (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
    (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
    (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
    (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
    where fac_controls.id = ".$request->id.";");
        $factura = $factura[0];
        $productos = DB::table('fac_products')
            ->select('*')
            ->orderBy('precio_articulo','DSC')
            ->where('codigo_factura', '=', $request->id)
            ->get();
        $pagosfac = DB::table('fac_pagos')
            ->select('*')
            ->orderBy('fac_pagos.created_at','ASC')
            ->where('fac_id', '=', $factura->id)
            ->get();


        $cliente=clientes::where('id','=',$factura->id_cliente)->first();
$pagado =  $factura->pagado;
        $monto = $factura->monto;

$resto= $monto -$pagado;
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
        if (  $cliente->serie ==0){

            return view('facturaB', ['factura' => $factura,'productos' => $productos,'cliente'=>$cliente,
                'impuesto'=>$impuesto,'monto'=>$monto,'montosi'=>$montosi,'iva'=>$iva, 'pagado'=>$pagado , 'resto'=>$resto,
                'pagosfac' => $pagosfac]);

        } else {

        //return response()->json($pagosfac);
        return view('factura', ['factura' => $factura,'productos' => $productos,'cliente'=>$cliente,
            'impuesto'=>$impuesto,'monto'=>$monto,'montosi'=>$montosi,'iva'=>$iva,
            'pagosfac' => $pagosfac]);
        }
    }

    public function reportarpago(Request $req){
        $cliente = $req->session()->get('dni');
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        if($req->pagot == "bolivar"){
            $baltip = $req->baltip;
            $balfrom = $req->balfrom;
            $comment = $req->comment;
            $monto = str_replace(',', '.', str_replace('.', '', $req->monto));
            $fecha = $req->fecha;

            $conversion = $monto/$tasa;

            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = $baltip;
            $balance->bal_from_in = $balfrom;
            $balance->bal_monto_in = $conversion;
            $balance->bal_rest_in = $conversion;
            $balance->conversion = $monto;
            $balance->bal_comment_in = $comment;
            $balance->tasa = $tasa;
            $balance->created_at = $fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            // });

            // $mensaje='su pago ha sido cargado y sera verificado en las proximas 72 horas habiles';
            // if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            //   $client= ucwords(strtolower($cliente->social));
            //   $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$mensaje;
            // }else {
            //   $client= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
            //   $message= "MARAVECA: Sr(a) ".ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido).", ".$mensaje;
            // }
            // sendsms($cliente->phone1, $message);

            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "zelle"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '12';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->bal_comment_in = $req->titular;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            //});



            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "wire"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '12';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->bal_comment_in = $req->titular;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            // });
            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "efectivo"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '14';
            $balance->bal_from_in = $req->balfrom;
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();

            /* =================================================================================================================================*/


            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "zellepd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '12';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->bal_comment_in = $req->titular;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();




            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "wirepd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '13';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->bal_comment_in = $req->codigo;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();


            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "efectivopd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '14';
            $balance->bal_from_in = $req->balfrom;
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->conversion = $req->monto;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();


            /* =================================================================================================================================*/
            Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
                $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
                $message->to($cliente->email);
                //$message->to('gerencia@maraveca.com');
                //$message->bcc('haorono@gmail.com');
                //$message->bcc('henry.orono@maraveca.com');
                $message->bcc('hector.diaz@maraveca.com');
                //$message->bcc('ramon.velazquez@maraveca.com');
                //$message->bcc('ana.reyes@maraveca.com');
                $message->subject('Pago cargado exitosamente');
                $message->priority(3);
            });
            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }

        // $mensaje='su pago ha sido cargado y sera verificado en las proximas 72 horas habiles';
        // if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
        //   $client= ucwords(strtolower($cliente->social));
        //   $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$mensaje;
        // }else {
        //   $client= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        //   $message= "MARAVECA: Sr(a) ".ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido).", ".$mensaje;
        // }
        //
        // sendsms($cliente->phone1, $message);
        return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
    }

    public function reportarpago_juridicos(Request $req){
        $cliente = $req->session()->get('dni');
        $tasa=configuracion::where('nombre','=','taza')->first()->valor;
        if($req->pagot == "bolivar"){
            $baltip = $req->baltip;
            $balfrom = $req->balfrom;
            $comment = $req->comment;
            $monto = str_replace(',', '.', str_replace('.', '', $req->monto));
            $fecha = $req->fecha;



            $balance = new balance_cliente;
            $balance->bal_cli = $cliente->id;
            $balance->bal_stat = '2';
            $balance->bal_tip = $baltip;
            $balance->bal_from = $balfrom;
            $balance->bal_monto = $monto;
            $balance->bal_rest = $monto;
            $balance->bal_comment = $comment;
            $balance->tasa = $tasa;
            $balance->created_at = $fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            // });

            // $mensaje='su pago ha sido cargado y sera verificado en las proximas 72 horas habiles';
            // if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
            //   $client= ucwords(strtolower($cliente->social));
            //   $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$mensaje;
            // }else {
            //   $client= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
            //   $message= "MARAVECA: Sr(a) ".ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido).", ".$mensaje;
            // }
            // sendsms($cliente->phone1, $message);

            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "zelle"){
            $balance = new balance_cliente;
            $balance->bal_cli = $cliente->id;
            $balance->bal_stat = '2';
            $balance->bal_tip = '9';
            $balance->bal_monto = $req->monto;
            $balance->bal_rest = $req->monto;
            $balance->tasa = $tasa;
            $balance->bal_comment = $req->titular;
            $balance->created_at = $req->fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            //});



            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "wire"){
            $balance = new balance_cliente;
            $balance->bal_cli = $cliente->id;
            $balance->bal_stat = '2';
            $balance->bal_tip = '10';
            $balance->bal_monto = $req->monto;
            $balance->bal_rest = $req->monto;
            $balance->tasa = $tasa;
            $balance->bal_comment = $req->codigo;
            $balance->created_at = $req->fecha;
            $balance->save();

            // Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
            //   $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
            //   //$message->to($verify->email);
            //   $message->to('henryaog@hotmail.com');
            //   $message->bcc('haorono@gmail.com');
            //   $message->bcc('henry.orono@maraveca.com');
            //   //$message->bcc('ramon.velazquez@maraveca.com');
            //   //$message->bcc('ana.reyes@maraveca.com');
            //   $message->subject('Pago cargado exitosamente');
            //   $message->priority(3);
            // });
            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "efectivo"){
            $balance = new balance_cliente;
            $balance->bal_cli = $cliente->id;
            $balance->bal_stat = '2';
            $balance->bal_tip = '11';
            $balance->bal_from = $req->balfrom;
            $balance->bal_monto = $req->monto;
            $balance->bal_rest = $req->monto;
            $balance->tasa = $tasa;
            $balance->created_at = $req->fecha;
            $balance->save();
            /* =================================================================================================================================*/


            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "zellepd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '12';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->bal_comment_in = $req->titular;
            $balance->created_at = $req->fecha;
            $balance->save();




            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "wirepd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '13';
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->bal_comment_in = $req->codigo;
            $balance->created_at = $req->fecha;
            $balance->save();


            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }elseif($req->pagot == "efectivopd"){
            $balance = new balance_clientes_in;
            $balance->bal_cli_in = $cliente->id;
            $balance->bal_stat_in = '2';
            $balance->bal_tip_in = '14';
            $balance->bal_from_in = $req->balfrom;
            $balance->bal_monto_in = $req->monto;
            $balance->bal_rest_in = $req->monto;
            $balance->created_at = $req->fecha;
            $balance->save();


            /* =================================================================================================================================*/
            Mail::send('rpagos', ['cliente'=>$cliente], function ($message) use ($cliente) {
                $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
                $message->to($cliente->email);
                //$message->to('gerencia@maraveca.com');
                //$message->bcc('haorono@gmail.com');
                //$message->bcc('henry.orono@maraveca.com');
                $message->bcc('hector.diaz@maraveca.com');
                //$message->bcc('ramon.velazquez@maraveca.com');
                //$message->bcc('ana.reyes@maraveca.com');
                $message->subject('Pago cargado exitosamente');
                $message->priority(3);
            });
            historico_cliente::create(['history'=>'Pago Cargado', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
            return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
        }

        // $mensaje='su pago ha sido cargado y sera verificado en las proximas 72 horas habiles';
        // if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
        //   $client= ucwords(strtolower($cliente->social));
        //   $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$mensaje;
        // }else {
        //   $client= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
        //   $message= "MARAVECA: Sr(a) ".ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido).", ".$mensaje;
        // }
        //
        // sendsms($cliente->phone1, $message);
        return redirect('clientesover')->withSuccess('Pago registrado satifactoriamente');
    }

    public function chpassword(){
        return view('password.password');
    }

    public function password1(Request $request){

        $kind = $request->get('kind');
        $dni = $request->get('dni');

        $search = DB::table('clientes')->where('kind', '=', $kind)->where('dni', '=', $dni)->get()->first();


        if(empty($search)){
            $respuesta = "Identificacion no encontrada en la base de datos";
            return view('register.errorpass',['respuesta' => $respuesta]);
        }elseif($search->abonado == null) {
            if(isset($search->password)){
                return view('password.password1',['search'=>$search]);
            }else{
                return redirect('login')->withErrors('Usted no esta registrado');
            }
        }else{
            return redirect('login')->withErrors('Ya se le ha enviado el correo(revisar la bandeja de spam)');
        }
    }

    public function restablecer(Request $request){
        $id = $request->get('id');
        $email = $request->get('email');

        $verify = DB::table('clientes')->where('id', '=', $id)->where('email', '=', $email)->get()->first();

        if(empty($verify)){
            $respuesta ='El correo ingresado no coincide con el de nuestro sistema.';
            return view('register.error2', ['respuesta'=>$respuesta]);
        }else{
            $cliente = Clientes::find($verify->id);
            if($cliente->count != '3'){
                $count = ++$cliente->count;

                $cliente->password = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
                $cliente->abonado = '1';
                $cliente->count = $count;
                $cliente->save();

                Mail::send('password.RRegister', ['cliente'=>$cliente], function ($message) use ($verify) {
                    $message->from('no-responder@maraveca.com', 'Sistema Administrativo(Correo Automatico)');
                    $message->to($verify->email);
                    $message->bcc('haorono@gmail.com');
                    //$message->bcc('henry.orono@maraveca.com');
                    $message->bcc('hector.diaz@maraveca.com');
                    //$message->bcc('gerencia@maraveca.com');
                    //$message->bcc('ramon.velazquez@maraveca.com');
                    //$message->bcc('ana.reyes@maraveca.com');
                    $message->subject('Restablecer contraseña');
                    $message->priority(3);
                });
                historico_cliente::create(['history'=>'Solicitud de cambio de contraseña', 'modulo'=>'Mi ventana', 'cliente'=>$cliente->id, 'responsable'=>'-1']);
                return redirect('login')->withSuccess('Correo enviado(revisar la bandeja de spam o correo no deseado)');
            }else{
                return redirect('login')->withErrors('Cantidad maxima de restablecer superada, contacte a administracion');
            }
        }
    }

    public function restablecerpass($id){
        $password= substr($id, -6);
        $id=substr($id,0, -6);
        $cliente = Clientes::find($id);
        //echo $id;
        if($cliente && $cliente->password == $password){
            return view('password.restclient', ['cliente' => $cliente, 'id' => $id]);
        }else {
            return view('register.changepass');
        }
    }
    public function restpassword(Request $request){
        if(empty($request)){
            echo 'Return';
        }else{
            $password = $request->get('password');
            $cpassword = $request->get('cpassword');
            $id = $request->get('id');
            if($password == $cpassword){
                $cliente = Clientes::find($id);
                $cliente->password = $password;
                $cliente->abonado = null;
                $cliente->save();
                historico_cliente::create(['history'=>'Contraseña cambiada', 'modulo'=>'Mi ventana', 'cliente'=>$id, 'responsable'=>'-1']);
                return redirect('login')->withSuccess('Contraseña cambiada');
            }else{
                $respuesta = 'Las contraseñas no son iguales';
                return view('password.restclient',['respuesta' => $respuesta]) ;
            }
        }
    }
    // funcion para retornar al formulario de reporte de pago, para planes en moneda local 6/8/19
    public function formreportbs(Request $request){

        // $cliente = $request->session()->get('dni');
        // return response()->json($cliente);
        $balanceo=0;
        $balanceoB=0;
        $balanceoD=0;
        $afavor=0;
        $afavord=0;
        $afavorb=0;
        $cliente = $request->session()->get('dni');

        $bancos_pre=configuracion::where('nombre', 'banco')->get()->first()->valor;
        $chunks = array_chunk(preg_split('/(-|,|:|;|,)/', $bancos_pre), 5);
        $bancos=array();
        //return  $chunks;
        foreach ($chunks as $key => $value) {
            $bancos[$key]=['banco'=>$value[0], 'numero'=>$value[1], 'perm'=>$value[2], 'titular'=>$value[3], 'dni'=>$value[4]];
        }


        //facturacion total del cliente
        $facturacion = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 ORDER BY created_at DESC limit 3;");

        $facturacionB = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = 'Bs.S' ORDER BY created_at DESC limit 3;");
        $facturacionD = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = '$' ORDER BY created_at DESC limit 3;");
        //servicios contratos por el cliente
        $servicios=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan', 'planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->get();
        $serviciosB=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan','planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '!=','3' )
            ->get();

        $serviciosD=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan','planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '=','3' )
            ->get();
        foreach ($facturacion as $fac) {
            $bal=$fac->pagado-$fac->monto;
            $fac->bal=$bal;
            if($bal!=0){
                $balanceo+=$bal;
            }
        }
        $balanceo= $balanceo*-1;
        $balance=balance_cliente::where('bal_cli', '=', $cliente->id)->get();

        foreach ($balance as $bal) {
            if($bal->bal_stat == '1'){
                if($bal->bal_rest>0){
                    $afavor+=$bal->bal_rest;
                }
            }
        }
        //solo para palanes en bolivares
        foreach ($facturacionB as $facb) {
            $balb=$facb->pagado-$facb->monto;
            $facb->bal=$balb;
            if($balb!=0){
                $balanceoB+=$balb;
            }
        }
        $balanceoB= $balanceoB*-1;
        $balanceb=balance_cliente::where('bal_cli', '=', $cliente->id)->get();

        foreach ($balanceb as $balb) {
            if($balb->bal_stat == '1'){
                if($balb->bal_rest>0){
                    $afavorb+=$balb->bal_rest;
                }
            }
        }
        //fin planes en bs

        //inicio de calculo de facturacion para planes en dolares
        foreach ($facturacionD as $facd) {
            $bald=$facd->pagado-$facd->monto;
            $facd->bal=$bald;
            if($bald!=0){
                $balanceoD+=$bald;
            }
        }
        $balanceoD= $balanceoD*-1;
        $balanced=balance_cliente::where('bal_cli', '=', $cliente->id)->get();

        foreach ($balanced as $bald) {
            if($bald->bal_stat == '1'){
                if($bald->bal_rest>0){
                    $afavord+=$bald->bal_rest;
                }
            }
        }
        $total_mensual=0;
        foreach ($servicios as $plan) {
            $total_mensual+=$plan->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensual=$total_mensual*1.16;
        }
        $total_mensualB=0;
        foreach ($serviciosB as $planb) {
            $total_mensualB+=$planb->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensualB=$total_mensualB*1.16;
        }
        $total_mensualD=0;
        foreach ($serviciosD as $pland) {
            $total_mensualD+=$pland->cost_plan;
        }
// fin de calculo de planes en dolares

        $soportes=DB::table('soportes')
            ->select('soportes.*', 'users.nombre_user', 'users.apellido_user')
            ->join('servicios','servicios.id_srv','=','soportes.servicio_soporte')
            ->join('clientes','servicios.cliente_srv','=','clientes.id')
            ->join('users','users.id_user','=','soportes.user_soporte')
            ->where('clientes.id', '=', $cliente->id)
            ->where('tipo_soporte', '=', '2')
            ->where('status_soporte', '=', '2')
            ->orderBy('soportes.created_at', 'DSC')
            ->get();
        foreach($soportes as $item){
            $problemas=[];
            $problems = DB::table('ticket_problems')
                ->select('problem_pb')
                ->where('ticket_pb', '=', $item->id_soporte)
                ->get();
            foreach ($problems as $key) {
                array_push($problemas, $key->problem_pb);
            }
            $item->problems = implode(', ', $problemas);
        };
        $historial=historico_cliente::where('cliente', $cliente->id)
            ->select('historico_clientes.*', 'users.nombre_user', 'users.apellido_user')
            ->leftjoin('users','users.id_user','=','responsable')
            ->orderBy('created_at', 'DSC')
            ->limit('3')
            ->get();

        $listaclientes = DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan','planes.tipo_plan')
            ->join('clientes', 'clientes.id', '=', 'servicios.cliente_srv')
            ->join('planes', 'planes.id_plan', '=', 'servicios.plan_srv')
            ->get();
        $pagosp =DB::table('balance_clientes')
            ->where('bal_stat', '=', '2')
            ->where('bal_cli', '=', $cliente->id)
            ->get();

        return view('reportarpagos.moneda_local',
            ['cliente'=>$cliente,
                'facturacion'=>$facturacion,
                'servicios'=>$servicios,
                'serviciosB'=>$serviciosB,
                'serviciosD'=>$serviciosD,
                'balance'=>$balance,
                'total_mensual'=>$total_mensual,
                'total_mensualD'=>$total_mensualD,
                'total_mensualB'=>$total_mensualB,

                'soportes' => $soportes,
                'historial' => $historial,
                'balanceo' =>$balanceo,
                'balanceoB' =>$balanceoB,
                'balanceoD' =>$balanceoD,
                'afavor' =>$afavor,
                'bancos' => $bancos,
                'pagosp' => $pagosp
            ]);
    }







// funcion para retornar al formulario de reporte de pago, para planes en moneda internacional 6/8/19
    public function formreportdl(Request $request){


// $cliente = $request->session()->get('dni');
        // return response()->json($cliente);
        $balanceo=0;
        $balanceoB=0;
        $balanceoD=0;
        $afavor=0;
        $afavord=0;
        $afavorb=0;
        $cliente = $request->session()->get('dni');

        $bancos_pre=configuracion::where('nombre', 'banco')->get()->first()->valor;
        $chunks = array_chunk(preg_split('/(-|,|:|;|,)/', $bancos_pre), 5);
        $bancos=array();
        //return  $chunks;
        foreach ($chunks as $key => $value) {
            $bancos[$key]=['banco'=>$value[0], 'numero'=>$value[1], 'perm'=>$value[2], 'titular'=>$value[3], 'dni'=>$value[4]];
        }


        //facturacion total del cliente
        $facturacion = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 ORDER BY created_at DESC limit 3;");

        $facturacionB = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = 'Bs.S' ORDER BY created_at DESC limit 3;");
        $facturacionD = DB::select(
            "SELECT fac_controls.*,
      (SELECT round(SUM(fac_products.precio_articulo), 2) from  fac_products where fac_controls.id = fac_products.codigo_factura) as monto,
      (SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id) as pagado,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'cliente') as cliente,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'dni') as dni,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'email') as email,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'phone') as phone,
      (select fac_dets.value from fac_dets where fac_dets.fac_id = fac_controls.id and fac_dets.detail = 'address') as address from fac_controls
      where fac_controls.id_cliente = ".$cliente->id." and fac_controls.fac_status = 1 and fac_controls.denominacion = '$' ORDER BY created_at DESC limit 3;");
        //servicios contratos por el cliente
        $servicios=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan','planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->get();
        $serviciosB=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan','planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '!=','3' )

            ->get();

        $serviciosD=DB::table('servicios')
            ->select('planes.name_plan', 'servicios.*', 'planes.cost_plan', 'planes.tipo_plan','planes.taza')
            ->join('clientes','clientes.id','=','servicios.cliente_srv')
            ->join('aps','aps.id','=','servicios.ap_srv')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->where('clientes.id','=',$cliente->id)
            ->where('servicios.tipo_plan_srv', '=','3' )

            ->get();

        foreach ($facturacion as $fac) {
            $bal=$fac->pagado-$fac->monto;
            $fac->bal=$bal;
            if($bal!=0){
                $balanceo+=$bal;
            }
        }
        $balanceo= $balanceo*-1;
        $balance=balance_cliente::where('bal_cli', '=', $cliente->id)->get();




        foreach ($balance as $bal) {
            if($bal->bal_stat == '1'){
                if($bal->bal_rest>0){
                    $afavor+=$bal->bal_rest;
                }
            }
        }
        //solo para palanes en bolivares
        foreach ($facturacionB as $facb) {
            $balb=$facb->pagado-$facb->monto;
            $facb->bal=$balb;
            if($balb!=0){
                $balanceoB+=$balb;
            }
        }
        $balanceoB= $balanceoB*-1;
        $balanceb=balance_cliente::where('bal_cli', '=', $cliente->id)->get();

        foreach ($balanceb as $balb) {
            if($balb->bal_stat == '1'){
                if($balb->bal_rest>0){
                    $afavorb+=$balb->bal_rest;
                }
            }
        }
        //fin planes en bs

        //inicio de calculo de facturacion para planes en dolares

        foreach ($facturacionD as $facd) {
            $bald=$facd->pagado-$facd->monto;
            $facd->bal=$bald;
            if($bald!=0){
                $balanceoD+=$bald;
            }
        }
        $balanceoD= $balanceoD*-1;
        $balance_in=balance_clientes_in::where('bal_cli_in', '=', $cliente->id)->get();

        foreach ($balance_in as $bald) {
            if($bald->bal_stat_in == '1'){
                if($bald->bal_rest_in>0){
                    $afavord+=$bald->bal_rest_in;
                }
            }
        }
        $total_mensual=0;
        foreach ($servicios as $plan) {
            $total_mensual+=$plan->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensual=$total_mensual*1.16;
        }
        $total_mensualB=0;
        foreach ($serviciosB as $planb) {
            $total_mensualB+=$planb->cost_plan;
        }
        if($cliente->serie == 1){
            $total_mensualB=$total_mensualB*1.16;
        }
        $total_mensualD=0;
        foreach ($serviciosD as $pland) {
            $total_mensualD+=$pland->cost_plan;
        }
// fin de calculo de planes en dolares

        $soportes=DB::table('soportes')
            ->select('soportes.*', 'users.nombre_user', 'users.apellido_user')
            ->join('servicios','servicios.id_srv','=','soportes.servicio_soporte')
            ->join('clientes','servicios.cliente_srv','=','clientes.id')
            ->join('users','users.id_user','=','soportes.user_soporte')
            ->where('clientes.id', '=', $cliente->id)
            ->where('tipo_soporte', '=', '2')
            ->where('status_soporte', '=', '2')
            ->orderBy('soportes.created_at', 'DSC')
            ->get();
        foreach($soportes as $item){
            $problemas=[];
            $problems = DB::table('ticket_problems')
                ->select('problem_pb')
                ->where('ticket_pb', '=', $item->id_soporte)
                ->get();
            foreach ($problems as $key) {
                array_push($problemas, $key->problem_pb);
            }
            $item->problems = implode(', ', $problemas);
        };
        $historial=historico_cliente::where('cliente', $cliente->id)
            ->select('historico_clientes.*', 'users.nombre_user', 'users.apellido_user')
            ->leftjoin('users','users.id_user','=','responsable')
            ->orderBy('created_at', 'DSC')
            ->limit('3')
            ->get();


        $pagosp_in =DB::table('balance_clientes_ins')
            ->where('bal_stat_in', '=', '2')
            ->where('bal_cli_in', '=', $cliente->id)
            ->get();







        return view('reportarpagos.moneda_int',
            ['cliente'=>$cliente,
                'facturacion'=>$facturacion,
                'servicios'=>$servicios,
                'serviciosB'=>$serviciosB,
                'serviciosD'=>$serviciosD,
                'balance'=>$balance,
                'total_mensual'=>$total_mensual,
                'total_mensualD'=>$total_mensualD,
                'total_mensualB'=>$total_mensualB,
                'afavord' =>$afavord,
                'soportes' => $soportes,
                'historial' => $historial,
                'balanceo' =>$balanceo,
                'balanceoB' =>$balanceoB,
                'balanceoD' =>$balanceoD,
                'afavor' =>$afavor,
                'bancos' => $bancos,
                'pagosp_in' => $pagosp_in
            ]);
    }
}
