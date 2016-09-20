<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Proveedores extends Model
{
	use SearchableTrait;

    protected $connection="pgsql";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=['id','razon_social','nombre_comercial','ruc','dir_matriz','dir_establecimiento','id_empresa'];
    protected $hidden = ['created_at','updated_at'];

     protected $searchable = [
        'columns' => [
            'razon_social' => 10,
             'nombre_comercial' => 10,
             'ruc' => 10
        ]
    ];
}
