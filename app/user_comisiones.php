<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_comisiones extends Model
{
    protected $fillable = [
        'user_comision',
        'cliente_comision',
        'porcentaje_comision',
        'denominacion_comision'
    ];//
}