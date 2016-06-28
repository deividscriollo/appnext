<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacturasRechazadas extends Model
{
    protected $connection="facturanexconex";
    protected $primaryKey="id_factura_r";
    public $incrementing=false;
    protected $fillable=['id_factura_r',
    // 'num_factura',
    // 'nombre_comercial',
    // 'Ruc_prov',
    // 'fecha_emision',
    // 'clave_acceso',
    // 'ambiente',
    // 'tipo_doc',
    // 'total',
    'razon_rechazo',
    'contenido_fac'
    ];
    protected $hidden = ['created_at','updated_at','id_empresa'];
}
