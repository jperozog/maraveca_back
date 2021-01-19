<?php

namespace App\Console\Commands;
use App\Mail\SendMailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers;
use Mail;
use App\historico_cliente;

class correosfacturacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correos:facturacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correos que se enviara a los que adeudan la ultima facturacion';

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
        echo date('h:i:s')."\n" ; 
   
        // delaying execution of the script for 2 seconds 
        sleep(10); 
        
        // displaying time again 
        echo date('h:i:s'); 
        /*
      $correo = DB::select('SELECT c.id,c.kind,c.dni,c.nombre,c.apellido,c.social,p.name_plan,p.taza FROM envio_correo AS e   
                                     INNER JOIN clientes AS c ON e.cliente = c.id
                                     INNER JOIN servicios AS s ON e.servicio = s.id_srv
                                     INNER JOIN planes AS p ON s.plan_srv = p.id_plan LIMIT 3');
        
        foreach ($correo as $c) {
            if($c->kind == 'V' || $c->kind == 'E' ){
                $cliente = $c->nombre." ".$c->apellido;
            }else{
                $cliente = $c->social;
            }
            $plan = $c->name_plan;
            $precio = $c->taza;
            $correo = 'perozo64@gmail.com';
            echo $cliente.'\n';
            //Mail::to($correo)->send(new SendMailable());
            
            Mail::send('emails.correoPrueba', ['cliente'=>$cliente,'plan'=>$plan,'precio'=>$precio],function ($message) use ($correo)
                {
                    $message->from('info@maraveca.com', 'Maraveca Telecomunicaciones');
                    $message->subject('Mensaje Facturacion');
                    $message->to($correo);
                });
            

        }
        */
    }     
   
}
