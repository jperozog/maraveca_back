<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lista_ip extends Model
{
    protected $fillable = [
        'ip',
        'cliente_ip',
        'status_ip',
        'ip_servicio'
    ];//
}

