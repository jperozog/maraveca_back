<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagos_comisiones extends Model
{
    protected $fillable = [
        'user_comision',
        'user_responsable',
        'monto_comision',
        'referencia_comision',
        'tipo_pago_comision',
        'banco_comision',
        'comment_comision',
        'fecha_comision',
        'month',
        'year'
       ];//
}
