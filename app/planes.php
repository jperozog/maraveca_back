<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planes extends Model
{
    protected $fillable = ['name_plan', 'cost_plan', 'taza', 'tipo_plan', 'dmb_plan', 'umb_plan', 'carac_plan', 'descripcion'];//
}
