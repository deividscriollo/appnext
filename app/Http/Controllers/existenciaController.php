<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//-------------------------------- Modelos ---
use App\Empresas;
//-------------------------------- Funciones ---
use App\libs\Funciones;
//-------------------------------- Autenticacion ---
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//-------------------------------- Extras ---
use GuzzleHttp\Client;

class existenciaController extends Controller
	{

	public function __construct(Request $request){
		//------------------------------------------ Modelos -----------------
	    	$this->tableEmpresas= new Empresas();
	    	$this->client = new Client;
    }
	// ----------------------------------------------- CERIFICAR EXISTENCIA USER NEXTBOOK ----------------------------
	public function usernext_exist(Request $request)
		{
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $this->tableEmpresas->select('ruc')->where('ruc', '=', $request->input('ruc'))->get();
		if (count($resultado) == 0)
			{
			$res = $this->client->request('GET', config('global.appserviciosnext').'/public/getDatos', ['json' => ['tipodocumento' => 'RUC', 'nrodocumento' => $request->input('ruc') ]]);
			$respuesta = json_decode($res->getBody() , true);
			return response()->json(["respuesta" => $respuesta], 200);
			}
		  else
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
	}
