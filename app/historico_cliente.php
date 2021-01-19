<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historico_cliente extends Model
{
  protected $fillable = ['history', 'modulo', 'cliente', 'responsable'];//
}
