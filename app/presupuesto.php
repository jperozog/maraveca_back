<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class presupuesto extends Model
{
  protected $fillable = ['cliente', 'tipo', 'planes', 'instalacion', 'moneda', 'responsable'];//
    //
}
