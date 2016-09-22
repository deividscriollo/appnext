<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_documentos extends Model
{
    protected $connection='facturanexconex';
   	protected $primaryKey='id';
   	public $incrementing=false;
    protected $fillable=array('id','descripcion','estado');

    protected $hidden = ['created_at','updated_at','estado'];
}
