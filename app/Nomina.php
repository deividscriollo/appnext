<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $connection="mod_radioconex";
    protected $primaryKey="id_nomina";
    public $incrementing=false;
    protected $fillable=["id_nomina",'img'];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
