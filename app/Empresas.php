<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    protected $connection='pgsql';
    protected $table='empresas';
    protected $primaryKey='id_empresa';
    public $incrementing = false;
    protected $fillable=array('id_empresa','Ruc','user_nextbook','razon_social','nombre_comercial','estado_contribuyente','tipo_contribuyente','obligado_contabilidad','actividad_economica','nombres_apellidos','fecha_nacimiento','correo','telefono','celular','codigo_activacion','estado');

     protected $hidden = ['created_at','updated_at','codigo_activacion'];
}
