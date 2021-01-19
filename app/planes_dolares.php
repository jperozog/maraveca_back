<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planes_dolares extends Model
{
    protected $fillable = ['name_plan', 'cost_plan', 'dmb_plan', 'umb_plan', 'carc_plan', 'descripcion'];  //
}
