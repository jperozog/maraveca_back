<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_adic extends Model
{
    //
    protected $fillable = [
      'id_cli',
      'id_fac',
      'codigo_articulo',
      'nombre_articulo',
      'precio_unitario',
      'IVA',
      'cantidad',
      'precio_articulo',
      'comment_articulo'
    ];
}
