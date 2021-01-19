<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class corte_prog extends Model
{
    //
    protected $fillable = [
        'id_cliente',
        'id_servicio',
        'fecha',
        'responsable',
        'status',
        'contador'
    ];
}
