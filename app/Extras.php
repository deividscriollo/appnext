<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extras extends Model
{
    protected $connection='personalizacionconex';
   	protected $table='extras';
    protected $fillable=['id','dato','tipo','id_empresa'];

    protected $hidden = ['tipo','id_empresa','created_at','updated_at'];
}
