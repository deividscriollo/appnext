<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Proveedores;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\libs\Funciones;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use App\blacklist_proveedores;
use App\Sucursales;

class proveedoresController extends Controller

	{
	public

	function get_proveedores(Request $request)
		{
		$user = JWTAuth::parseToken()->authenticate();
		$tabla = new Proveedores();
		$blacklist_proveedores = new blacklist_proveedores();
		$tabla_sucursal = new Sucursales();
		$sucursal=$tabla_sucursal->select('id_sucursal')->where('codigo','=',$request->input('codigo'))->where('id_empresa','=',$user['id_user'])->get();
if (count($sucursal)==0) {
	return response()->json(['respuesta' => false,"mensaje"=>"no-existe-sucursal"], 200);
}

    $currentPage = $request->input('pagina_actual');
    Paginator::currentPageResolver(function () use($currentPage)
      {
      return $currentPage;
      });

    if ($request->input('filter')!=null) {
         $datos = $tabla->search($request->input('filter'))->get();
    }else{

    	$bl_proveedores=$blacklist_proveedores->select('id_proveedor')->where('id_sucursal','=',$sucursal[0]['id_sucursal'])->get();
$datos = $tabla->where('id_empresa','=',$user['id_user'])->orderBy('razon_social','DESC')->get();

foreach ($bl_proveedores as $key => $value) {
	$id_proveedor=$bl_proveedores[$key]['id_proveedor'];
	foreach ($datos as $key => $value) {
		if ($id_proveedor==$datos[$key]['id']) {
			unset($datos[$key]);
		}
		
	}


}

	}

    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    // Create a new Laravel collection from the array data
    $collection = new Collection($datos);
    // Define how many items we want to be visible in each page
    $perPage = $request->input('limit');
    // Slice the collection to get the items to display in current page
    $currentPageSearchResults = $collection->forPage($currentPage,$perPage)->all();
    // Create our paginator and pass it to the view
    $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection) , $perPage);
    
    return response()->json(['respuesta' => $paginatedSearchResults], 200);

		}

	public

	function add_proveedor(Request $request)
		{
		$user = JWTAuth::parseToken()->authenticate();
		$tabla = new Proveedores();
		$sql=$tabla->where('ruc','=',$request->input('ruc'))->get();
		if (count($sql)==0) {
			$funciones = new Funciones();
		$tabla->id = $funciones->generarId();
		$tabla->razon_social = $request->input('razon_social');
		$tabla->nombre_comercial = $request->input('nombre_comercial');
		$tabla->ruc = $request->input('ruc');
		$tabla->dir_matriz = $request->input('dir_matriz');
		$tabla->dir_establecimiento = $request->input('dir_establecimiento');
		$tabla->id_empresa = $user['id_user'];
		$tabla->estado = 1;
		$resultado = $tabla->save();
		if ($resultado)
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
		return response()->json(["respuesta" => false], 200);
		}

	public

	function update_proveedor(Request $request)
		{
		$tabla = new Proveedores();
		$resultado = $tabla->where('id', '=', $request->input('id'))->update(
			['razon_social' => $request->input('razon_social') , 
			'nombre_comercial' => $request->input('nombre_comercial') ,
			 'ruc' => $request->input('ruc') , 
			 'dir_matriz' => $request->input('dir_matriz') ,
			  'dir_establecimiento' => $request->input('dir_establecimiento') ]);
		if ($resultado)
			{
			return response()->json(["respuesta" =>true], 200);
			}
		}

	public

	function delete_proveedor(Request $request)
		{
		$user = JWTAuth::parseToken()->authenticate();	
		$blacklist_proveedores = new blacklist_proveedores();
		$tabla_sucursal = new Sucursales();
		$sucursal=$tabla_sucursal->select('id_sucursal')->where('codigo','=',$request->input('codigo'))->where('id_empresa','=',$user['id_user'])->get();


		$blacklist_proveedores->id_sucursal = $sucursal[0]['id_sucursal'];
		$blacklist_proveedores->id_proveedor = $request->input('id');
		 $resultado=  $blacklist_proveedores->save();
		if ($resultado)
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
	}


