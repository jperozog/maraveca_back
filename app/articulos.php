<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class articulos extends Model
{
    protected $fillable = ['modelo_articulo', 'serial_articulo', 'id_tipo_articulo'];
}
