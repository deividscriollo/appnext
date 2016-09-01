<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//--------------------- Modelos --------------------
use App\Proveedores;
use App\blacklist_proveedores;
use App\Sucursales;
// -------------------------- JWT AUTH ----------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//---------------------- funciones generales ------
use App\libs\Funciones;
//---------------------- Paginador -----------------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
//------------------------------- extras ---------------
use GuzzleHttp\Client;


class proveedoresController extends Controller
	{

	public function __construct(Request $request)
    {
    	//----------------------------------- Funciones --------------------------
        $this->funciones = new Funciones();
        //----------------------------------- Modelos --------------------------
        $this->sucursal=new Sucursales();
        $this->blacklist_proveedores = new blacklist_proveedores();
        $this->proveedores = new Proveedores();
        //------------------------------------ Autenticacion -------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Id Sucursal ---------------------
        if ($request->input('sucursal')!=null) {
        $datos=$this->sucursal->select('id_sucursal')->where('codigo','=',$request->input('sucursal'))->where('id_empresa','=',$this->user['id_user'])->get();
        $this->id_sucursal=$datos[0]['id_sucursal'];
    	}else{
    		return response()->json(['respuesta' => false,"mensaje"=>"no-existe-sucursal"], 200);
    	}
       
    }

	public function get_proveedores(Request $request)
		{

	    $currentPage = $request->input('pagina_actual');
	    Paginator::currentPageResolver(function () use($currentPage)
	      {
	      return $currentPage;
	      });

    if ($request->input('filter')!=null) {
         $datos = $this->proveedores->search($request->input('filter'))->get();
    }else{

    $bl_proveedores=$this->blacklist_proveedores->select('id_proveedor')->where('id_sucursal','=',$this->id_sucursal)->get();
	$datos = $this->proveedores->where('id_empresa','=',$this->user['id_user'])->orderBy('razon_social','DESC')->get();

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

	public function add_proveedor(Request $request)
		{

		$sql=$this->proveedores->where('ruc','=',$request->input('ruc'))->get();
		if (count($sql)==0) {
			$funciones = new Funciones();
		$this->proveedores->id = $funciones->generarId();
		$this->proveedores->razon_social = $request->input('razon_social');
		$this->proveedores->nombre_comercial = $request->input('nombre_comercial');
		$this->proveedores->ruc = $request->input('ruc');
		$this->proveedores->dir_matriz = $request->input('dir_matriz');
		$this->proveedores->dir_establecimiento = $request->input('dir_establecimiento');
		$this->proveedores->id_empresa = $this->user['id_user'];
		$this->proveedores->estado = 1;
		$resultado = $this->proveedores->save();
		if ($resultado)
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
		return response()->json(["respuesta" => false], 200);
		}

	// public function update_proveedor(Request $request)
	// 	{
	// 	$resultado = $this->proveedores->where('id', '=', $request->input('id'))->update(
	// 		['razon_social' => $request->input('razon_social') , 
	// 		'nombre_comercial' => $request->input('nombre_comercial') ,
	// 		 'ruc' => $request->input('ruc') , 
	// 		 'dir_matriz' => $request->input('dir_matriz') ,
	// 		  'dir_establecimiento' => $request->input('dir_establecimiento') ]);
	// 	if ($resultado)
	// 		{
	// 		return response()->json(["respuesta" =>true], 200);
	// 		}
	// 	}

	// public function delete_proveedor(Request $request)
	// 	{
	// 	$this->blacklist_proveedores->id_sucursal = $this->id_sucursal;
	// 	$this->blacklist_proveedores->id_proveedor = $request->input('id');
	// 	 $resultado=  $this->blacklist_proveedores->save();
	// 	if ($resultado)
	// 		{
	// 		return response()->json(["respuesta" => true], 200);
	// 		}
	// 	}

	public function get_datos_proveedor_by_Ruc(Request $request)
		{
		$sql=$this->proveedores->where('ruc',$request->input('ruc'))->where('id_empresa',$this->user['id_user'])->get();
		if (count($sql)==0) {
			$client = new Client;
			$res = $client->request('GET', 'http://apiservicios.nextbook.ec/public/getDatos', ['json' => ['tipodocumento' => 'RUC', 'nrodocumento' => $request->input('ruc') ]]);
			$respuesta = json_decode($res->getBody() , true);
			return response()->json(["respuesta" => $respuesta['datosEmpresa']], 200);
				}else return response()->json(["respuesta" => false], 200);	
		}

	}


