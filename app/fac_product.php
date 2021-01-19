<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_product extends Model
{
    //
    protected $fillable = [
      'codigo_factura',
      'codigo_articulo',
      'nombre_articulo',
      'precio_unitario',
      'IVA',
      'cantidad',
        'precio_articulo',
        'precio_dl',
        'precio_bs',

      'comment_articulo'
    ];
}
