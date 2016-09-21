<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_consumos extends Model
{
    protected $connection='facturanextconex';
   	protected $primaryKey='id';
    protected $fillable=array('id','descripcion','estado');

    protected $hidden = ['created_at','updated_at'];
}
