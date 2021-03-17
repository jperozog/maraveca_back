<?php

namespace App\Http\Controllers;
use App\balance_cliente;
use App\balance_clientes_in;
use App\fac_product;
use App\historico;
use App\historico_config_admin;
use App\planes;
use Illuminate\Support\Facades\DB;

use App\configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
   
    public function index()
    {
        $result=DB::table('configuracions')
            // ->orderBy('configuracions.created_at','DSC')
            ->get();
        return $result;
        // return response()->json($result);
    }

    
    public function update(Request $request)
    {
        
        $tasa_bd=configuracion::where('nombre','=',"taza");
        $tasaBCV_bd=configuracion::where('nombre','=',"taza(BCV)");
        $iva_bd=configuracion::where('nombre','=',"iva");
        $moneda_local_bd= configuracion::where('nombre','=',"denominacion");
        $moneda_ex_bd= configuracion::where('nombre','=',"denominacion_in");

        $tasa =$tasa_bd->first()->valor;
        $tasaBCV =$tasaBCV_bd->first()->valor;
        $iva= $iva_bd->first()->valor;
        $moneda_local= $moneda_local_bd->first()->valor;
        $moneda_ex= $moneda_ex_bd->first()->valor;

        if ($tasa != $request->tasa ){


            $planes=planes::all();
            foreach ($planes as $plan) {
                if ($plan->taza != null && $plan->taza > 0){
                    $monto = round($plan->taza * $request->tasa,2);
                    $planes = planes::where('id_plan','=',$plan->id_plan);
                    $planes->update(["cost_plan"=>$monto]);
                }
            }
            historico_config_admin::create(['history' => 'Actualizacion del valor de la tasa de cambio de : '.number_format($tasa, 2, '.', ',').' BS.S'.' a '.number_format($request->tasa, 2, '.', ',').' BS.S', 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Actualizacion del valor de la tasa de cambio de : '.number_format($tasa, 2, '.', ',').' BS.S'.' a'.number_format($request->tasa, 2, '.', ',').' BS.S']);
            $tasa_bd->update(["valor"=>$request->tasa]);
        }


        if ($tasaBCV != $request->tasaBCV ){


            
            historico_config_admin::create(['history' => 'Actualizacion del valor de la tasa(BCV) de cambio de : '.number_format($tasaBCV, 2, '.', ',').' Bs'.' a '.number_format($request->tasaBCV, 2, '.', ',').' Bs', 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Actualizacion del valor de la tasa(BCV) de cambio de : '.number_format($tasaBCV, 2, '.', ',').' Bs'.' a'.number_format($request->tasaBCV, 2, '.', ',').' Bs']);
            $tasaBCV_bd->update(["valor"=>$request->tasaBCV]);
        }


        if ($iva != $request->iva ){


            historico_config_admin::create(['history' => 'Actualizacion del valor del Iva de : '.$iva.'%'.' a '.$request->iva.'%', 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' => 'Facturacion', 'detalle' =>  'Actualizacion del valor del iva de : '.$iva.'%'.' a'.$request->iva.'%']);
            $iva_bd->update(["valor"=>$request->iva]);

        }

        if ($moneda_local != $request->moneda_local ){

            historico_config_admin::create(['history' => 'Actualizacion de la denominacion de moneda local de : '.$moneda_local.' a'.$request->moneda_local , 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' =>  'Actualizacion de la denominacion de moneda local de : '.$moneda_local.' a '.$request->moneda_local]);

            $moneda_local_bd ->update(["valor"=>$request->moneda_local]);
        }
        if ($moneda_ex != $request->moneda_ex ){


            historico_config_admin::create(['history' => 'Actualizacion de la denominacion de moneda local de : '.$moneda_ex.' a'.$request->moneda_ex , 'modulo' => 'Configuracion', 'cliente' => null, 'responsable' => $request->responsable]);

            historico::create(['responsable' => $request->responsable, 'modulo' =>  'Actualizacion de la denominacion de moneda local de : '.$moneda_ex.' a '.$request->moneda_ex]);

            $moneda_ex_bd->update(["valor"=>$request->moneda_ex]);
        }
        
        return response()->json($request);
    }

    public function balances()
    {
        $balancebs = DB::select("SELECT fc.*,fp.*,cl.apellido,cl.nombre,cl.social,cl.serie FROM fac_controls as fc 
		inner join clientes as cl on fc.id_cliente = cl.id
        inner join fac_pagos as fp on fc.id = fp.fac_id
			where fc.fac_num is not null ORDER BY fc.id  DESC");

        $balancedl = balance_clientes_in ::
        select(
            'clientes.kind',
            'clientes.dni',
            'clientes.password',
            'clientes.email',
            'clientes.nombre',
            'clientes.apellido',
            'clientes.direccion',
            'clientes.day_of_birth',
            'clientes.serie',
            'clientes.phone1',
            'clientes.phone2',
            'clientes.comment',
            'clientes.social',
            'balance_clientes_ins.id_bal_in',
            'balance_clientes_ins.bal_cli_in',
            'balance_clientes_ins.bal_tip_in',
            'balance_clientes_ins.bal_stat_in',
            'balance_clientes_ins.bal_from_in',
            'balance_clientes_ins.bal_monto_in',
            'balance_clientes_ins.conversion',
            'balance_clientes_ins.bal_rest_in',
            'balance_clientes_ins.bal_comment_in',
            'balance_clientes_ins.bal_comment_mod_in',
            'balance_clientes_ins.bal_fecha_mod_in',
            'balance_clientes_ins.user_bal_mod_in',
            'balance_clientes_ins.tasa',
           'users.nombre_user',
            'balance_clientes_ins.created_at'
        )
            ->join('clientes', 'balance_clientes_ins.bal_cli_in', '=', 'clientes.id')
            ->join('users','users.id_user','=','balance_clientes_ins.user_bal_mod_in')
            ->orderBy('balance_clientes_ins.bal_stat_in', 'DESC')
            ->orderBy('balance_clientes_ins.updated_at', 'DESC')
            ->get();


        $index = collect(['balanceb' => $balancebs, 'balanced' => $balancedl]);

        return response()->json($index); //lo devuelvo via rest al modulo configuracion
    }










}
