<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswrdsE extends Model
{
    protected $connection='ingresoconex';
   	protected $table='passwrdsE';
    protected $fillable=array('id_pass','pass_email','pass_nextbook','id_empresa');
}
