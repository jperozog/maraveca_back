<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_control extends Model
{
    //
    protected $fillable = [
        'fac_num',
        'id_cliente',
        'fac_status',
        'denominacion',
        'serie_fac',
        'fac_serv',
        'tasa_pago'
    ];
}
