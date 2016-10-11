<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    protected $connection="mensajeriaconex";
    protected $primaryKey="chat_id";
    public $incrementing=false;
    protected $fillable=[
    'chat_id','user1_id','user2_id','estado'
];

    protected $hidden = ['created_at','updated_at'];
}
