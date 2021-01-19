<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_ret extends Model
{
    //
    protected $fillable = [
      'fac_id',
      'rets_tipo',
      'rets_monto',
      'rets_comment'
    ];
}
