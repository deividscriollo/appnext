<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class regpersona_empresas extends Model
{
    protected $connection='pgsql';
    protected $table='regpersona_empresas';
    protected $primaryKey='idp_regE';
    public $incrementing = false;
    protected $fillable=array('idp_regE','nombres_apellidos','fecha_nacimiento','correo','telefono','celular','estado','id_empresa');

     protected $hidden = ['created_at','updated_at'];
}
