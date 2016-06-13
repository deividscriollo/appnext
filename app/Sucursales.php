<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    protected $connection='pgsql';
   	protected $table='sucursales';
    protected $fillable=array('id','codigo','direccion','estado','nombre_sucursal');

    protected $hidden = ['created_at','updated_at'];
}
