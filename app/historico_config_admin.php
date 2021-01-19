<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historico_config_admin extends Model
{
  protected $fillable = ['history', 'modulo', 'cliente', 'responsable'];//
}
