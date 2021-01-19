<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transferencias_equipos extends Model
{
  protected $fillable = [
    'responsable', 'desde', 'equipos', 'hacia', 'confirma', 'status'
    ];//
}
