<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Empresas extends Model
{
	use SearchableTrait;

    protected $connection='pgsql';
    protected $table='empresas';
    protected $primaryKey='id_empresa';
    public $incrementing = false;
    protected $fillable=array('id_empresa','ruc','user_nextbook','razon_social','nombre_comercial','estado_contribuyente','tipo_contribuyente','obligado_contabilidad','actividad_economica','nombres_apellidos','fecha_nacimiento','correo','telefono','celular','codigo_activacion','estado','id_provincia');

     protected $hidden = ['created_at','updated_at','codigo_activacion'];

     protected $searchable = [
        'columns' => [
            'ruc' => 13,
             'nombre_comercial' => 25,
             'razon_social' => 25
        ]
    ];
}
