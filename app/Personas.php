<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
   	protected $connection='pgsql';
   	protected $table='personas';
    protected $fillable=array('id','cedula','Nombres_apellidos','provincia','canton','parroquia','zona','correo','telefono','celular');
}
