<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Nomina extends \Eloquent
{
     use SearchableTrait;

    protected $connection="mod_radioconex";
    protected $primaryKey="id";
    public $incrementing=false;
    protected $fillable=[
    'id', 
    'periodicidad', 
    'registro_patronal', 
    'dias',
    'estado', 
    'fecha_inicio','id_sucursal'];
    protected $hidden = ['created_at','updated_at','id_sucursal','estado'];

      protected $searchable = [
        'columns' => [
            'periodicidad' => 10,
             'registro_patronal' => 10
        ]
    ];

    public function posts()
    {
        return $this->hasMany('Post');
    }

}
