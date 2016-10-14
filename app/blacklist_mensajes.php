<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class blacklist_mensajes extends Model
{
    protected $connection="mensajeriaconex";
    protected $primaryKey="id";
    protected $fillable=['id_mensaje','id_empresa'];
    protected $hidden = ['created_at','updated_at','id'];
}
