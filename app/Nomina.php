<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $connection="mod_radioconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=[
    'id', 
    'periodicidad', 
    'registro_patronal', 
    'dias',
    'estado', 
    'fecha_inicio','id_sucursal'];
    protected $hidden = ['created_at','updated_at','id_sucursal','estado'];
}
