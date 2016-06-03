<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    protected $connection='pgsql';
    protected $table='empresas';
    protected $fillable=array('id','razon_social','nombre_comercial','estado_contribuyente','tipo_contribuyente','obligado_contabilidad','actividad_economica','nombres_apellidos','fecha_nacimiento','correo','telefono','celular');
}
