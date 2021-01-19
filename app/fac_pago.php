<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fac_pago extends Model
{
    //
    protected $fillable = [
      'fac_id',
      'pag_tip',
      'pag_monto',
      'pag_comment',
      'responsable',
        'balance_pago',
        'balance_pago_in',
        'created_at'
    ];
}
