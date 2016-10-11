<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswrdsE extends Model
{
    protected $connection='ingresoconex';
   	protected $table='passwrdsE';
    protected $fillable=array('id_pass','pass_email','password','remember_token','id_empresa');

    protected $hidden = ['password','pass_email','remember_token'];
}
