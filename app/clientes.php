<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    //
    protected $fillable = [
      'kind',
      'dni',
      'password',
      'email',
       'nombre',
       'apellido',
       'direccion',
       'day_of_birth',
       'serie',
       'phone1',
       'phone2',
       'comment',
       'social',
       'abonado',
        'tipo_servicio'
     ];
}
