<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $connection="personalizacionconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=[
    'id','nombre'
];

    protected $hidden = ['created_at','updated_at','estado'];
}
