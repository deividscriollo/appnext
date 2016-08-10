<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    protected $connection='pgsql';
   	protected $table='sucursales';
   	protected $primaryKey='id_sucursal';
    protected $fillable=array('id_sucursal','codigo','direccion','estado','nombre_sucursal','categoria');

    protected $hidden = ['id_empresa','created_at','updated_at'];
}
