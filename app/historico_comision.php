<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historico_comision extends Model
{
    protected $fillable = [
        'user_comision',
        'user_responsable',
        'comentario'
    ];//
}
