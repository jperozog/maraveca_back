<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\cola_email_notify;
use App\clientes;
use Illuminate\Support\Facades\DB;
use Mail;

class ColaEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ColaEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $request=cola_email_notify::get()->first();
      cola_email_notify::where('id', $request->id)->update(['contador'=>($request->contador+1)]);
      $cli=[];
      $detail1=[];
      $tipo='';
      if ($request->tipo == 1){
          $result=DB::table('servicios')
          ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
          ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->join('aps','aps.id','=','servicios.ap_srv')
          ->join('celdas', 'aps.celda_ap','=','celdas.id_celda')
          ->join('servidores', 'celdas.servidor_celda','=','servidores.id_srvidor')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->where('clientes.id', $request->id_cli)
          ->get()->first();

      }elseif ($request->tipo == 2) {
          $result=DB::table('servicios')
          ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
          ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->join('aps','aps.id','=','servicios.ap_srv')
          ->join('celdas', 'aps.celda_ap','=','celdas.id_celda')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->where('clientes.id', $request->id_cli)
          ->get()->first();

      }elseif ($request->tipo == 3) {
          $result=DB::table('servicios')
          ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
          ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->where('clientes.id', $request->id_cli)
          ->get()->first();

      }elseif ($request->tipo == 4) {
          $result=DB::table('servicios')
          ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.email', 'clientes.kind', 'clientes.social', 'planes.name_plan', 'planes.cost_plan')
          ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
          ->join('clientes','clientes.id','=','servicios.cliente_srv')
          ->join('planes','planes.id_plan','=','servicios.plan_srv')
          ->where('clientes.id', $request->id_cli)
          ->get()->first();

      }elseif ($request->tipo == 5) {
        $result=DB::table('servicios')
        ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
        ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
        ->join('clientes','clientes.id','=','servicios.cliente_srv')
        ->where('clientes.id', $request->id_cli)
        ->get()->first();
        }elseif ($request->tipo == 6) {
        $result=DB::table('clientes')
          ->select('clientes.id', 'clientes.phone1', 'clientes.nombre', 'clientes.apellido', 'clientes.kind', 'clientes.social', 'clientes.email')
          ->groupBy('clientes.phone1', 'clientes.nombre', 'clientes.apellido')
          ->where('clientes.id', $request->id_cli)
          ->get()->first();
      }

        if($request->tipo==4){
          $hashes=array("#nombre_plan", "#valor_plan");
          $valores=array($result->name_plan, "BSS. ".number_format($result->cost_plan, 0));
          $mensaje=str_replace($hashes, $valores, $request->message);
        }else{
          $mensaje=$request->mensaje;
        }
        $cliente="";
        if((strtolower($result->kind)=='g'||strtolower($result->kind)=='j')&&(strtolower($result->social)!= 'null' && $result->kind != null)){
          $cliente=ucwords(strtolower($result->social));
          $message= "MARAVECA: Srs. ".$cliente.", ".$mensaje;
        }else {
          $cliente=ucfirst($result->nombre)." ".ucfirst($result->apellido);
          $message= "MARAVECA: Sr(a) ".$cliente.", ".$mensaje;
        }
        if($request->via == 1){
          Mail::send('emails.Notify', [
            'cliente' => $cliente,
            'fecha'=>date('d-m-Y'),
            'mensaje'=>$mensaje,
            'moroso'=>$result],function ($message) use ($cliente, $result)
            {
              //$message->subject('Notificacion-'.$cliente.'('.date('d-m-Y').')');
              $message->subject('INFORMACION IMPORTANTE');
              $message->from('no-responder@maraveca.com', 'Maraveca Telecomunicaciones(Mail Automatico)');
              $message->to($result->email);
              $message->bcc('hector.diaz@maraveca.com');
              //$message->to("ana.reyes@maraveca.com");
              //$message->to('jesus.orono@maraveca.com');


            });
      cola_email_notify::where('id', $request->id)->delete();
    }elseif ($request->via == 2){

Mail::send('emails.Navidad', [
'cliente' => $cliente,
'fecha'=>date('d-m-Y'),
'mensaje'=>$mensaje,
'moroso'=>$result],function ($message) use ($cliente, $result)
                {
                  //  $message->subject('Notificacion-'.$cliente.'('.date('d-m-Y').')');
                    $message->subject('¡Felices Fiestas!');
                    $message->from('no-responder@maraveca.com', 'Maraveca Telecomunicaciones(Mail Automatico)');
                    $message->to($result->email);
                    $message->bcc('no-responder@maraveca.com');
                    //$message->bcc('hector.diaz@maraveca.com');
                    // $message->to("ana.reyes@maraveca.com");
                    //  $message->to('jesus.orono@maraveca.com');


                });
            cola_email_notify::where('id', $request->id)->delete();
}

    }
}
