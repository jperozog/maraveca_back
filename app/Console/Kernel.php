<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use app\servicios;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     **/
    protected $commands = [
        //
        Commands\agregarPPPOE::class,
        Commands\facturacion::class,
        Commands\factura::class,
        Commands\factura_programada::class,
        Commands\factura_promocion::class,
        Commands\factura_blanco::class,
        Commands\mensajesmorosos::class,
        Commands\mensajesfacturacion::class,
        Commands\mensajescortes::class,
        Commands\mensajesmanuales::class,
        Commands\mensajespersonales::class,
        Commands\correosfacturacion::class,
        Commands\corte_compromisoServicio::class,
        Commands\suspender::class,
        Commands\suspender_nuevo::class,
        Commands\suspender_nuevo2::class,
        Commands\ColaEjecucion::class,
        Commands\ColaEmail::class,
        Commands\clientes_servicios::class,
        Commands\Agregar_Activos::class,
        Commands\Agregar_plan_lista::class,
        Commands\front_mensajes_morosos::class,
        Commands\cortes_prog::class,
        Commands\corte_promo::class,
        Commands\cierre_caja::class,
        Commands\msj_cortes_prog::class,
         Commands\mensajesfacturacion_prov::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('facturacion:correr')->monthlyOn(1, '00:00');
        // $schedule->command('Suspender')->monthlyOn(20, '8:40');
        // $schedule->command('Suspender')->monthlyOn(27, '09:00'); //temporal
        // $schedule->command('Mensajes:Facturacion')->monthlyOn(5, '08:00');
      //  $schedule->command('MensajesAMorosos')->monthlyOn(07, '09:00');//cambio de dia de la semana realizado el 07/06/2019
       // $schedule->command('MensajesAMorosos')->monthlyOn(10, '09:00');//cambio de dia de la semana  realizado el 07/06/2019
       // $schedule->command('Mensajes:Corte')->monthlyOn(13, '09:00'); //cambio y activacion de dia de la semana  realizado el 07/06/2019
        $schedule->command('ColaEjecucion')->everyTenMinutes();
        $schedule->command('ColaEmail')->everyMinute();
      $schedule->command('factura_programadas')->dailyAt('08:00');
      $schedule->command('cortes_prog')->dailyAt('14:00');
      $schedule->command('corte_promo')->dailyAt('09:00');
      $schedule->command('cierre_caja')->dailyAt('18:00');
      $schedule->command('compromisos_servicios')->dailyAt('09:00');
      $schedule->command('correos:facturacion')->everyMinute();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    /*  protected function commands()//
      {
          require base_path('routes/console.php');
      }
  */
}