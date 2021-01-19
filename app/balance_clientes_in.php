<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class balance_clientes_in extends Model

{
    protected $fillable = ['bal_cli_in', 'bal_tip_in', 'bal_from_in', 'bal_monto_in', 'bal_rest_in', 'bal_comment_in','bal_comment_mod_in','bal_fecha_mod_in','tasa','user_bal_mod_in', 'created_at'];//
    //


}
