<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat_mensajes extends Model
{
        protected $connection="mensajeriaconex";
    protected $primaryKey="chat_mensajes_id";
    public $incrementing=false;
    protected $fillable=[
    'chat_mensajes_id','chat_id','user_id','mensaje','estado_view'
];

    protected $hidden = ['created_at','updated_at','chat_mensajes_id'];
}
