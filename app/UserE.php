<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserE extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guard = "usersE";
    protected $connection='ingresoconex';
    protected $table='passwrdsE';
    protected $fillable = ['email', 'pass_email','password','id_empresa'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password','pass_email','remember_token'];
}
