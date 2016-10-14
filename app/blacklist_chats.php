<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class blacklist_chats extends Model
{
     protected $connection="mensajeriaconex";
    protected $primaryKey="id";
    protected $fillable=['id_chat','id_empresa'];
    protected $hidden = ['created_at','updated_at','id'];
}
