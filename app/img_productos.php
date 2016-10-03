<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class img_productos extends Model
{
    protected $connection="catalogoconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=["id",'img','estado','id_producto'];
    protected $hidden = ['created_at','updated_at'];
}
