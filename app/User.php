<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection='ingresoconex';
    protected $table='passwrdsP';
    protected $fillable = ['email', 'pass_email','password','remember_token','id_user'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password','pass_email','remember_token'];
}
