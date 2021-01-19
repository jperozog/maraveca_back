<?php

namespace App\Console\Commands;

use App\fac_product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers;
use Jenssegers\Date\Date;

class mensajesfacturacion_prov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Mensajes:Facturacion_p';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mensajes que se enviara a los que adeudan la ultima facturacion';

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
        Date::setLocale('es');

        $fecha_fac = Date::now()->format('F\\-Y');



      //selecciono todos los clientes
        $clientes = DB::table('clientes')
            ->join('servicios','servicios.cliente_srv','=','clientes.id')
            ->join('planes','planes.id_plan','=','servicios.plan_srv')
            ->get();

        foreach ($clientes as $cliente) {
          // busco las facturas

          $facturas = DB::table('fac_controls')
              ->join('fac_products', 'fac_products.codigo_factura', '=','fac_controls.id' )
->where('fac_controls.id_cliente','=', $cliente->id)
->where('fac_controls.denominacion','=', '$')
              ->orderBy('fac_products.id', 'DESC')
              ->whereRaw('MONTH(fac_controls.created_at) = MONTH(CURRENT_DATE())')
              ->whereRaw('YEAR(fac_controls.created_at) = YEAR(CURRENT_DATE())')
              ->take(1)
              ->get();
            foreach ($facturas as $factura) {







                $txt = "se ha generado una nueva factura correspondiente al mes de ".ucfirst($fecha_fac).", por un monto de: " . number_format($factura->precio_articulo, 2) . " US$, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana";//cambio del formato del link de mi pago a mi ventana 07/06/2019,


               // $txt = "se ha generado una nueva factura correspondiente al mes en curso, su saldo actual es de: " . number_format($moroso->deuda, 2) . " Bs.S, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana";//cambio del formato del link de mi pago a mi ventana 07/06/2019,


            //$txt="se ha generado una nueva factura correspondiente al mes en curso, su saldo actual es de: ".number_format($moroso->deuda, 2)." Bs.S, reporte su pago a traves de info@maraveca.com, https://maraveca.com/mi-ventana";//cambio del formato del link de mi pago a mi ventana 07/06/2019,
          //$txt="le informamos que su servicio se encuentra suspendido. Actualmente presenta un saldo vencido en su facturación DE ".number_format($moroso->deuda, 2)." Bs.S";
          //$txt="recuerde que este es un sistema de mensajeria masiva no monitoreada. para cualquier informacion comuniquese a traves de: maraveca.com info@maraveca.com o al master 02617725180 ó 02687755100";
            if((strtolower($cliente->kind)=='g'||strtolower($cliente->kind)=='j')&&(strtolower($cliente->social)!= 'null' && $cliente->kind != null)){
              $message= "MARAVECA: Srs. ".ucwords(strtolower($cliente->social)).", ".$txt;
            }else {
              $message= "MARAVECA: Sr.(a) ".ucwords(strtolower($cliente->apellido)).", ".$txt;
            }
            //echo $message."\n";
          //sendsms($moroso->phone1, $message);
       $fp = stream_socket_client("tcp://192.168.12.251:5038", $errno, $errstr);

          if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
          }
          else {
            fwrite($fp, "Action: Login\r\nUsername: maraveca\r\nSecret: x31y0x\r\n\r\n");
            fwrite($fp, "Action: smscommand\r\ncommand: gsm send sms 3 ".urlencode($cliente->phone1)." \"".urlencode($message)."\" ".rand()." \r\n\r\n");
            //while (!feof($fp)) 	echo fgets($fp, 1024);
            //echo fread($fp, 4096);
            fclose($fp);
         
          }
              echo $cliente->phone1." \ ".$message."\n";
            }
        }
   }
}
