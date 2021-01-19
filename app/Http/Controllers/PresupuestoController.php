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

  /*$pdf = App::make('dompdf.wrapper');
  $pdf->loadView('email.send', ['title' => "prueba de email", 'content' => "contenido"]);
  $data = $pdf->stream();*/
  $idp=presupuesto::create($request->all());
  $planes=[];
  $fac2=[];
    if($request->planes == 'r'){
      $residenciales= planes::where('name_plan', 'like', '%residencial%')->where('tipo_plan', '=', 1)->orderBy('id_plan', 'ASC')->take(4)->get();
      foreach ($residenciales as $res) {
        array_push($planes, $res);
      }
    }elseif ($request->planes == 'c') {
      $comercial= planes::where('name_plan', 'like', '%comercial%')->where('tipo_plan', '=', 1)->orderBy('id_plan', 'ASC')->take(4)->get();
      foreach ($comercial as $plan) {
        array_push($planes, $plan);
      }
    }elseif ($request->planes=='d') {
      $dedicados= planes::where('name_plan', 'like', '%dedicado%')->where('tipo_plan', '=', 1)->where('taza', '>=', 12)->orderBy('id_plan', 'ASC')->take(4)->get();
      foreach ($dedicados as $plan) {
        array_push($planes, $plan);
      }
    }elseif ($request->planes=='h') {
      $happy= planes::where('name_plan', 'like', '%happy%')->where('tipo_plan', '=', 1)->orderBy('id_plan', 'ASC')->take(4)->get();
      foreach ($happy as $plan) {
        array_push($planes, $plan);
      }
    }
     elseif($request->planes == 'r2'){
         $residenciales= planes::where('name_plan', 'like', '%residencial%')->where('tipo_plan', '=', 4)->orderBy('id_plan', 'ASC')->take(4)->get();
         foreach ($residenciales as $res) {
             array_push($planes, $res);
         }
     }elseif ($request->planes == 'c2') {
         $comercial= planes::where('name_plan', 'like', '%comercial%')->where('tipo_plan', '=', 4)->orderBy('id_plan', 'ASC')->take(4)->get();
         foreach ($comercial as $plan) {
             array_push($planes, $plan);
         }
     }elseif ($request->planes=='d2') {
         $dedicados= planes::where('name_plan', 'like', '%dedicado corporativo%')->where('tipo_plan', '=', 4)->where('taza', '>=', 12)->orderBy('id_plan', 'ASC')->take(4)->get();
         foreach ($dedicados as $plan) {
             array_push($planes, $plan);
         }
     }elseif ($request->planes=='h2') {
         $happy= planes::where('name_plan', 'like', '%happy%')->where('tipo_plan', '=', 4)->orderBy('id_plan', 'ASC')->take(4)->get();
         foreach ($happy as $plan) {
             array_push($planes, $plan);
         }
    }elseif($request->planes == 'r3'){
        $residenciales= planes::where('name_plan', 'like', '%residencial%')->where('tipo_plan', '=', 3)->orderBy('id_plan', 'ASC')->take(4)->get();
        foreach ($residenciales as $res) {
            array_push($planes, $res);
        }

    }elseif ($request->planes == 'c3') {
        $comercial= planes::where('name_plan', 'like', '%comercial%')->where('tipo_plan', '=', 3)->orderBy('id_plan', 'ASC')->take(4)->get();
        foreach ($comercial as $plan) {
            array_push($planes, $plan);
        }
    }
    elseif ($request->planes=='d3') {
        $dedicados= planes::where('name_plan', 'like', '%dedicado corporativo%')->where('tipo_plan', '=', 3)->where('taza', '>=', 12)->orderBy('id_plan', 'ASC')->take(4)->get();
        foreach ($dedicados as $plan) {
            array_push($planes, $plan);
        }
    }elseif ($request->planes=='h3') {
        $happy= planes::where('name_plan', 'like', '%happy%')->where('tipo_plan', '=', 3)->orderBy('id_plan', 'ASC')->take(4)->get();
        foreach ($happy as $plan) {
            array_push($planes, $plan);
        }

     }elseif($request->planes == 'r6'){
      $residenciales= planes::where('name_plan', 'like', '%residencial%')->where('tipo_plan', '=', 6)->orderBy('id_plan', 'ASC')->take(3)->get();
      foreach ($residenciales as $res) {
          array_push($planes, $res);
      }

  }elseif($request->planes == 'c6'){
    $residenciales= planes::where('name_plan', 'like', '%comercial%')->where('tipo_plan', '=', 6)->orderBy('id_plan', 'ASC')->take(3)->get();
    foreach ($residenciales as $res) {
        array_push($planes, $res);
    }

}elseif($request->planes == 'h6'){
  $residenciales= planes::where('name_plan', 'like', '%happy%')->where('tipo_plan', '=', 6)->orderBy('id_plan', 'ASC')->take(3)->get();
  foreach ($residenciales as $res) {
      array_push($planes, $res);
  }

} elseif($request->planes == 'r7'){
  $residenciales= planes::where('name_plan', 'like', '%residencial%')->where('tipo_plan', '=', 7)->orderBy('id_plan', 'ASC')->take(3)->get();
  foreach ($residenciales as $res) {
      array_push($planes, $res);
  }

}elseif($request->planes == 'c7'){
$residenciales= planes::where('name_plan', 'like', '%comercial%')->where('tipo_plan', '=', 7)->orderBy('id_plan', 'ASC')->take(3)->get();
foreach ($residenciales as $res) {
    array_push($planes, $res);
}

}elseif($request->planes == 'h7'){
$residenciales= planes::where('name_plan', 'like', '%happy%')->where('tipo_plan', '=', 7)->orderBy('id_plan', 'ASC')->take(3)->get();
foreach ($residenciales as $res) {
  array_push($planes, $res);
}

}if($request->factibi && $request->factibi!=''){
      $fac2 =$result_det=DB::table('factibilidades_dets') //busco los detalles
          ->select(\DB::raw('nombre, valor'))
          ->where('id_fac','=',$request->factibi)
          ->where('nombre','=','altura')
          ->first();
      $request->factibi = $fac2->valor;
    }
  if(strtolower($request->tipo) == 'c'){
    $cliente = clientes::where('id', $request->cliente)->get()->first();
    $cli = $cliente;
    if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j' ||strtolower($cliente->kind)=='v-')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
      $cliente= ucwords(strtolower($cliente->social));
    }else {
      $cliente= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
    }
  }else{
    $cliente = Pclientes::where('id', $request->cliente)->get()->first();
    $cli = $cliente;
    if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j' ||strtolower($cliente->kind)=='v-')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
      $cliente= ucwords(strtolower($cliente->social));
    }else {
      $cliente= ucfirst($cliente->nombre)." ".ucfirst($cliente->apellido);
    }
  }
  echo $cliente."\n";
  $user = user::where('id_user', $request->responsable)->first();
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

       return response()->json(['message' => 'Request completed']);
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
