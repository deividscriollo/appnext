<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswrdsP extends Model
{
    protected $connection='ingresoconex';
   	protected $table='passwrdsP';
    protected $fillable=array('id_pass','pass_email','pass_nextbook','id_persona');
}
