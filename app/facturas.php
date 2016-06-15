<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    protected $connection="facturanexconex";
    protected $primaryKey="id_factura";
    public $incrementing=false;
    protected $fillable=['id_factura','nombre_fac','contenido_fac'];
    protected $hidden = ['created_at','updated_at'];
}
