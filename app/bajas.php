<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bajas extends Model
{
    protected $connection='inventarioconex';
	protected $primaryKey="id";
	public $incrementing=false;
	protected $fillable=[	'id',
							'maestro_articulo',
							'motivos_baja',
							'fecha_baja',
							'estado',
							'id_sucursal'];
	protected $hidden=['id','estado','updated_at'];
}
