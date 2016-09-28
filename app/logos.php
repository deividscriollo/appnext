<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class logos extends Model
{
    protected $connection="personalizacionconex";
    protected $primaryKey="id_img_logo";
    public $incrementing=false;
    protected $fillable=["id_img_logo",'img'];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
