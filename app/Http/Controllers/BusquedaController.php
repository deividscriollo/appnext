<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------------- Modelos ---------------
use App\Empresas;
//----------------------- paginador --------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class BusquedaController extends Controller
{
    public function get_empresas(Request $request)
    {
    $empresas = new Empresas();

	$datos = $empresas->where('estado','=',1)->orderBy('created_at','ASC')->paginate(10)->items();

    return response()->json(['respuesta' => $datos], 200);
    }

}
