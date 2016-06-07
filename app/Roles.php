<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $connection='ingresoconex';
   	protected $table='roles';
    protected $fillable=array('id','nombre_rol');
}
