<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $connection="catalogoconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=['id',
           'codigo',
           'descripcion',
           'nombre',
           'stock',
           'precio_unitario',
           'precio_oferta',
           'estado'];
    protected $hidden = ['created_at','updated_at'];
}
