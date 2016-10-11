<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class maestro_articulo extends Model
{
    protected $connection='inventarioconex';
	protected $primaryKey="id";
	public $incrementing=false;
	protected $fillable=[	'id',
							'codigo',
							'descripcion',
							'grupo',
							'marca',
							'modelo',
							'color',
							'anio_fabricacion',
							'num_partes',
							'num_placa',
							'num_serie',
							'pais_origen',
							'num_factura',
							'num_guia',
							'fecha_adquisicion',
							'valor_compra',
							'modo_adquisicion',
							'estado_bn',
							'observaciones',
							'fecha_inicio',
							'estado', 
							'id_sucursal'];

	protected $hidden=['id','estado','created_at','updated_at'];
}
