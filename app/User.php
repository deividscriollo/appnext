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
    protected $fillable = ['id','email','pass_nextbook','id_user'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pass_nextbook',
    ];
}
