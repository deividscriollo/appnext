<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portadas extends Model
{
    protected $connection="personalizacionconex";
    protected $primaryKey="id_img_portada";
    public $incrementing=false;
    protected $fillable=["id_img_portada",'img'];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
