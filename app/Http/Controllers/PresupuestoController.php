<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\presupuesto;
use App\equipos;
use App\planes;
use App\clientes;
use App\Pclientes;
use App\User;
use Illuminate\Http\Request;
use Mail;
use PDF;

class PresupuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

/**
*este es el metodo de envio de presupuestos
*creara un presupuesto en formato pdf y lo adjuntara en un correo electronico
*el cual sera enviado a los clientes

 */

 public function sendPresupuesto(Request $request){
  $fecha = date("Y-m-d H:i:s");
  $client = $request->cliente;
  $idp2=DB::insert("INSERT INTO presupuestos(cliente,tipo,planes,instalacion,moneda,responsable,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)",[$client["id"],"r","r1",$request->costo,$request->moneda,$request->user,$fecha,$fecha]);
  $idp = DB::select("SELECT * FROM presupuestos ORDER BY id DESC LIMIT 1")["0"];
  $planes=[];
  $fac2=[];
    if($request->servicio == 3){
      $residenciales= DB::select("SELECT * FROM planes WHERE tipo_plan = 4 AND name_plan LIKE '%residencial%' ORDER BY id_plan ASC LIMIT 4");
      foreach ($residenciales as $res) {
        array_push($planes, $res);
      }
    }
    if ($request->servicio == 4) {
      $comercial= DB::select("SELECT * FROM planes WHERE tipo_plan = 4 AND name_plan LIKE '%comercial%' ORDER BY id_plan ASC LIMIT 4");
      foreach ($comercial as $plan) {
        array_push($planes, $plan);
      }
    }
    if ($request->servicio == 5) {
      $dedicados= DB::select("SELECT * FROM planes WHERE tipo_plan = 4 AND name_plan LIKE '%dedicado%' ORDER BY id_plan ASC LIMIT 4");
      foreach ($dedicados as $plan) {
        array_push($planes, $plan);
      }
    }
     
    if($request->servicio == 1){
      $fibraResidenciales= DB::select("SELECT * FROM planes WHERE tipo_plan = 7 AND (name_plan NOT LIKE '%comercial%' OR name_plan NOT LIKE '%dedicado%') ORDER BY id_plan ASC LIMIT 4");
      foreach ($fibraResidenciales as $res) {
          array_push($planes, $res);
      }

    }

    if($request->servicio == 2){
    $fibraComerciales= DB::select("SELECT * FROM planes WHERE tipo_plan = 7 AND name_plan LIKE '%comercial%' ORDER BY id_plan ASC LIMIT 4");
    foreach ($fibraComerciales as $res) {
        array_push($planes, $res);
    }

    }

  
  

    $cliente = Pclientes::where('id', $client["id"])->get()->first();
    $cli = $cliente;
    if((strtolower($client["kind"])=='g'||strtolower($client["kind"])=='j' ||strtolower($client["kind"])=='v-')&&(strtolower($client["social"])!= 'null' && $client["kind"] != null)){
      $cliente= ucwords(strtolower($client["social"]));
    }else {
      $cliente= ucfirst($client["nombre"])." ".ucfirst($client["apellido"]);
    }
    /*
    $cliente = Pclientes::where('id', $request->cliente)->get()->first();
    $cli = $cliente;
    if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j' ||strtolower($cliente->kind)=='v-')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
      $cliente= ucwords(strtolower($cliente->social));
    }else {
      $cliente= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
    }
    */
    
  echo $cliente."\n";
  $user = user::where('id_user', $request->user)->first();
  $date = date('d/m/Y', time());
  //return $planes;
    Mail::send('emails.cuerpoPresupuesto', ['fecha'=>$date, 'moroso'=>$cli, 'cliente'=>$cliente],function ($message) use ($request, $planes, $cliente, $date, $cli, $user, $idp)
       {
         $data = PDF::loadView('emails.presupuestos', [
         'fecha' => $date,
         'cliente'=>$cliente,
         'planes' =>$planes,
         'solicitud' => $request,
         'detalle' => $cli,
         'idp'=>$idp
       ])
         ->setPaper('a4')->setWarnings(false)->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->stream();
         //$data = '%PDF-1.2 6 0 obj << /S /GoTo /D (chapter.1) >>';
           $message->from($user->email_user, 'Maraveca Telecomunicaciones');
           $message->subject('Presupuesto-'.$cliente.'-'.$date);
           $message->to($cli->email);
          //$message->bcc('karen.arino@maraveca.com');
           $message->bcc($user->email_user);
           $message->attach(public_path('ppt/Brochure_Maraveca_Telecomunicaciones.pps'));
           $message->attachData($data, 'Presupuesto-'.$cliente.'-'.$date.'.pdf');
           echo public_path('ppt/Brochure_Maraveca_Telecomunicaciones.pps');
       });
       
       return response()->json($planes);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\presupuesto  $presupuesto
     * @return \Illuminate\Http\Response
     */
     public function show(Request $request)
     {

       return presupuesto::where('tipo', '=', $request->tipo)->where('cliente', '=', $request->cliente)->get();
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\presupuesto  $presupuesto
     * @return \Illuminate\Http\Response
     */
    public function edit(presupuesto $presupuesto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\presupuesto  $presupuesto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, presupuesto $presupuesto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\presupuesto  $presupuesto
     * @return \Illuminate\Http\Response
     */
    public function destroy(presupuesto $presupuesto)
    {
        //
    }
}
