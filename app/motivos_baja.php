<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class motivos_baja extends Model
{
	protected $connection='inventarioconex';
	protected $primaryKey="id";
	public $incrementing=false;
	protected $fillable=[	'id',
							'codigo',
							'descripcion',
							'estado',
							'id_sucursal'];
	protected $hidden=['estado','updated_at'];
}
