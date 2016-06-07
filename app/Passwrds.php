<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passwrds extends Model
{
    protected $connection='ingresoconex';
   	protected $table='passwrds';
    protected $fillable=array('id_pass','pass_email','pass_nexbook','id_user');
}
