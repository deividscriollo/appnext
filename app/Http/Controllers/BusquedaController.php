<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------------- Modelos ---------------
use App\Empresas;
use App\img_perfiles;
//----------------------- paginador --------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class BusquedaController extends Controller
{
    public function get_empresas(Request $request)
    {
    $empresas = new Empresas();
    $img_perfil = new img_perfiles();

	$datos = $empresas->where('estado','=',1)->orderBy('created_at','ASC')->paginate(10)->items();
	foreach ($datos as $key => $value) {
		if ($datos[$key]['nombre_comercial']=='no disponible') {
			$datos[$key]['nombre_comercial']=$datos[$key]['razon_social'];
		}
	}

	foreach ($datos as $key => $value) {
		$perfil=$img_perfil->where('id_empresa',$datos[$key]['id_empresa'])->where('estado',1)->first();
		$datos[$key]['img']=$perfil['img'];
	}



    return response()->json(['respuesta' => $datos], 200);
    }

}
