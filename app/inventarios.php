<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class inventarios extends Model
{
  protected $fillable = ['model_inventario', 'serial_inventario', 'zona_inventario', 'status'];
}
