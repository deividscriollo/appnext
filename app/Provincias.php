<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincias extends Model
{
    protected $connection="localizacionconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=['id','nombre','codtelefonico'];
    protected $hidden = ['created_at','updated_at'];
}
