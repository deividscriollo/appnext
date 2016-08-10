<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class blacklist_proveedores extends Model
{
    protected $connection="facturanexconex";
    protected $primaryKey="id";
    protected $fillable=['id_sucursal','id_proveedor'];
    protected $hidden = ['created_at','updated_at','id'];
}
