<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
   	protected $connection='pgsql';
   	protected $table='personas';
   	protected $primaryKey='id_persona';
   	public $incrementing = false;
    protected $fillable=array('id_persona','cedula','Nombres_apellidos','provincia','canton','parroquia','zona','correo','telefono','celular','codigo_activacion','estado');
}
