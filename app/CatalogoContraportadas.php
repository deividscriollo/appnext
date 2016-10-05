<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogoContraportadas extends Model
{
    protected $connection="catalogoconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=['id',
           'img','descripcion',
           'estado'];
    protected $hidden = ['created_at','updated_at','id_sucursal'];
}
