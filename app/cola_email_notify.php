<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cola_email_notify extends Model
{
  protected $fillable = [
    'id_cli',
    'mensaje',
    'tipo',
      'via',
    'contador',

   ];
}
