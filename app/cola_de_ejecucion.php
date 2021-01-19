<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cola_de_ejecucion extends Model
{
  protected $fillable = [
    'id_srv',
    'accion',
      'soporte_pd',
    'contador'
   ];
}
