<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class soportes extends Model
{
  protected $fillable = [
    'servicio_soporte',
    'status_soporte',
    'comment_soporte',
    'user_soporte',
    'afectacion_soporte',
    'tipo_soporte',
    ];//
}
