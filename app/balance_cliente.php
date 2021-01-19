<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class balance_cliente extends Model
{
  protected $fillable = ['bal_cli', 'bal_tip', 'bal_from', 'bal_monto', 'bal_rest', 'bal_comment','bal_comment_mod','bal_fecha_mod','user_bal_mod', 'tasa','uso_bal_in','created_at'];//
    //
}
