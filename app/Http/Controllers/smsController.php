<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Helpers\Helpers;

class smsController extends Controller
{
    //
    public function update(Request $request)
    {
        $numero = $request->numero;
        $mensaje = $request->mensaje;
        sendsms($numero, $mensaje);
        //$conjunto = $numero + $mensaje;
        echo $numero;
        return "Mensaje Enviado: " . $numero . " " . $mensaje;

    }
}
