<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class img_perfiles extends Model
{
    protected $connection="personalizacionconex";
    protected $primaryKey="id_img_perfil";
    public $incrementing=false;
    protected $fillable=["id_img_perfil",'img'];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
