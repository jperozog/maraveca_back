<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class servidores extends Model
{
  protected $fillable = [
    'id_srvidor',
    'nombre_srvidor',
    'ip_srvidor',
    'user_srvidor',
    'password_srvidor'
    ];//
}
