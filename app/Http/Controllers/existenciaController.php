<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Clientes;
use App\Empresas;
use App\libs\Funciones;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp\Client;

class existenciaController extends Controller

	{
	// ----------------------------------------------- CERIFICAR EXISTENCIA CLIENTE ----------------------------
	public function cliente_exist(Request $request)

		{
		$table = new Clientes();
		$user = JWTAuth::parseToken()->authenticate();
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $table->select('id')->where('ruc_empresa', '=', $request->input('ruc_empresa'))->orderBy('id', 'DESC')->get();
		if (count($resultado) == 0)
			{
			$client = new Client;
			$res = $client->request('GET', 'http://192.168.100.17/appserviciosnext/public/getDatos', ['json' => ['tipodocumento' => 'RUC', 'nrodocumento' => $request->input('ruc_empresa') ]]);
			$respuesta = json_decode($res->getBody() , true);
			return response()->json(["respuesta" => $respuesta], 200);
			}
		  else
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
	// ----------------------------------------------- CERIFICAR EXISTENCIA USER NEXTBOOK ----------------------------
	public function usernext_exist(Request $request)

		{
		$table = new Empresas();
		// $user = JWTAuth::parseToken()->authenticate();
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $table->select('Ruc')->where('Ruc', '=', $request->input('ruc'))->get();
		if (count($resultado) == 0)
			{
			$client = new Client;
			$res = $client->request('GET', 'http://192.168.100.17/appserviciosnext/public/getDatos', ['json' => ['tipodocumento' => 'RUC', 'nrodocumento' => $request->input('ruc') ]]);
			$respuesta = json_decode($res->getBody() , true);
			return response()->json(["respuesta" => $respuesta], 200);
			}
		  else
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
	}
