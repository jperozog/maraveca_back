<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class instpag extends Model
{
  protected $fillable = ['n', 'monto', 'referencia', 'banco', 'fecha', 'responsable', 'installer', 'comment'];//
    //
}
