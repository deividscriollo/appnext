<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    protected $connection="facturanexconex";
    protected $primaryKey="id_factura";
    public $incrementing=false;
    protected $fillable=['id_factura','clave_acceso','num_factura','nombre_comercial','Ruc_prov','fecha_emision','clave_acceso','ambiente','tipo_doc','tipo_consumo','total','contenido_fac','estado_view'];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
