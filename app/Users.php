<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{

  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'nombre_user', 'email_user', 'password', 'username', 'apellido_user', 'phone_user', 'installer', 'installs', 'comision'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
      'password', 'remember_token',
  ];
  public function findForPassport($username) {
     return self::where('username', $username)->first(); // change column name whatever you use in credentials
  }
}
