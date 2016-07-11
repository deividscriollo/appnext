<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
   protected $connection="pgsql";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=[
    'id',
    'id_empresa',
    'nombre_comercial',
    'actividad_economica',
    'razon_social',
    'representante_legal',
    'cedula_representante',
    'celular',
    'telefono',
    'direccion',
    'correo',
    'sitio_web',
    'facebook',
    'twitter',
    'google',
    'observaciones',
    'imagen',
    'estado',
    'fecha_creacion'];

    protected $hidden = ['fecha_creacion','estado'];
}
