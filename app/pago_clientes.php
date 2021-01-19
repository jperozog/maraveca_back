<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pago_clientes extends Model
{
  protected $fillable = ['monto', 'credito', 'referencia', 'cliente'];//
}
