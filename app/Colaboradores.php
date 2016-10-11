<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Colaboradores extends Model
{
    protected $connection='pgsql';
   	protected $table='colaboradores';
    protected $fillable=array('id','correo','pass','id_empresa','estado');
}
