<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class factibilidades extends Model
{
    //
    protected $fillable = [
      'id_pot',
      'coordenadaslat',
      'coordenadaslon',
      'status',
      'factible',
      'ptp',
      'comentario',
        'ciudad'
    ];
}
