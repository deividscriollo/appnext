<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_documentos extends Model
{
    protected $connection='facturanextconex';
   	protected $primaryKey='id_sucursal';
    protected $fillable=array('id_sucursal','codigo','direccion','estado','nombre_sucursal','categoria');

    protected $hidden = ['created_at','updated_at'];
}
