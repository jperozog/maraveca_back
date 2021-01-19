<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class oinstallpgo extends Model
{
    protected $fillable = ['monto', 'referencia', 'banco', 'fecha', 'responsable', 'installer', 'comment'];//
}
