<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class celdas extends Model
{
    //
    protected $fillable = ['nombre_celda', 'ip_celda', 'user_celda', 'password_celda', 'servidor_celda'];
}
