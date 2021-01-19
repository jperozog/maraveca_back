<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pclientes extends Model
{
    //
    protected $fillable = [
      'id_cli',
      'kind',
       'dni',
       'email',
       'nombre',
       'apellido',
       'direccion',
       'day_of_birth',
       'serie',
       'phone1',
       'phone2',
       'comment',
       'social'
     ];
}
