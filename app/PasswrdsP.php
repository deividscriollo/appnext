<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswrdsP extends Model
{
    protected $connection='ingresoconex';
   	protected $table='passwrdsP';
    protected $fillable=array('id_pass','pass_email','password','remember_token','id_user');

    protected $hidden = ['password','pass_email','remember_token'];
}
