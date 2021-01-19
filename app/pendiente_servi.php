<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pendiente_servi extends Model
{
    //
    protected $fillable = [
        'soporte_pd',
        'cliente_pd',
        'celda_pd',
        'plan_pd',
        'ip_pd',
        'status_pd',

    ];
}