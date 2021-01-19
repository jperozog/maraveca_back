<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_prog extends Model
{
    //
    protected $fillable = [
        'id_cliente',
        'fecha',
        'id_servicio',
        'pro',
        'responsable',
        'status',
        'contador'
    ];
}
