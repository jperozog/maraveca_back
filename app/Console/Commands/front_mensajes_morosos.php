<?php

namespace App\Console\Commands;

use App\historico_mensaje;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
//use App\Helpers;
class front_mensajes_morosos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MensajesAMorososFront
  {mensaje?} {Responsable?}';

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
        
        
        //selecciono todos los clientes
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
            ROUND((SELECT SUM(fac_products.precio_articulo) from  fac_products where fac_controls.id = fac_products.codigo_factura), 2) as monto,
            ROUND((SELECT SUM(fac_pagos.pag_monto) from  fac_pagos where fac_controls.id = fac_pagos.fac_id), 2) as pagado
            from fac_controls
            WHERE fac_controls.id_cliente = $cliente->id
            and fac_controls.fac_status = 1 ORDER BY created_at DESC;");
            foreach ($facturas as $factura) {
                if ($factura->pagado != null || $factura->pagado != 'null' || $factura->pagado != 'NULL'){
                        if($factura->fac_status==1){
                            $monto=$monto+$factura->monto;
                            $pagado=$pagado+$factura->pagado;
                        }
                }else{
                        if($factura->fac_status==1){
                            $monto=$monto+$factura->monto;
                        }
                }
            }
            $servicios=DB::table('servicios')
                ->where('servicios.cliente_srv','=',$cliente->id)
                ->where('servicios.stat_srv','!=','4')
                ->where('servicios.stat_srv','!=','5')
                ->where('servicios.stat_srv','!=','3')
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
            if ($moroso->serie == 0 || $moroso->serie == null || $moroso->serie == 'null' || $moroso->serie == 'NULL' ) {
                $monto = number_format($moroso->deuda, 2);
                $txt1=  $this->argument('mensaje');
               $remp_txt= array('{{monto}}');
                $correct_txt=  $monto.' US$';
                $txt = str_replace($remp_txt, $correct_txt, $txt1);
         
            } else{

                $txt1=  $this->argument('mensaje');
                $monto = number_format($moroso->deuda, 2);
                $remp_txt= array('{{monto}}');
                $correct_txt= $monto.' Bs.S';
                $txt = str_replace($remp_txt, $correct_txt, $txt1);
              
            }
           
            if((strtolower($moroso->kind)=='g'||strtolower($moroso->kind)=='j')&&(strtolower($moroso->social)!= 'null' && $moroso->kind != null)){
                $message= "MARAVECA: Srs. ".ucwords(strtolower($moroso->social)).", ".$txt;
            }else {
                $message= "MARAVECA: Sr.(a) ".ucwords(strtolower($moroso->apellido)).", ".$txt;
            }
            
            
            $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);
                
        		if (!$fp) {
        			//echo "ERROR: $errno - $errstr<br />\n";
        		}
        		else {
        			fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
        			fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($moroso->phone1)." \"".urlencode($message)."\" ".rand()." \r\n\r\n");
        			
        			fclose($fp);
                }
                
                
                historico_mensaje::create(['responsable'=>$this->argument('Responsable'), 'modulo'=>'mensaje a deudores', 'detalle'=>$message]);
   
}

}
}

