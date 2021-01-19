<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
  protected $fillable = [
    'user',
    'perm',
    ];//
}
