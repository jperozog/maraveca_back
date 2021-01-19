<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ticket_history extends Model
{
  protected $fillable = [
    'ticket_th',
    'user_th',
    'comment',
    ];//
}
