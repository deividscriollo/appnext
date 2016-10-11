<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Clientes;
use App\libs\Funciones;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp\Client;

class ClientesController extends Controller

	{

 public function __construct() {
        $this-> middleware('cors');
    }

	// --------------------------------------------AÑADIR CLIENTE--------------------------
	public function save(Request $request)

		{
		$table = new Clientes();
		$funciones = new funciones();
		$user = JWTAuth::parseToken()->authenticate();
		if (!is_dir(public_path() . "/clientes/" . $user['id_user']))
			{
			mkdir(public_path() . "/clientes/" . $user['id_user']);
			}
		$file = $request->file('imagen');
		$id_cliente = $funciones->generarID();
		$table->id = $id_cliente;
		$table->ruc_empresa = $request->input('ruc_empresa');
		$table->nombre_comercial = $request->input('nombre_comercial');
		$table->actividad_economica = $request->input('actividad_economica');
		$table->razon_social = $request->input('razon_social');
		$table->representante_legal = $request->input('representante_legal');
		$table->cedula_representante = $request->input('cedula_representante');
		$table->celular = $request->input('celular');
		$table->telefono = $request->input('telefono');
		$table->direccion = $request->input('direccion');
		$table->correo = $request->input('correo');
		$table->sitio_web = $request->input('sitio_web');
		$table->facebook = $request->input('facebook');
		$table->twitter = $request->input('twitter');
		$table->google = $request->input('google');
		$table->observaciones = $request->input('observaciones');
		$table->imagen = "default.png";
		$table->estado = '1';
		$table->id_empresa = $user['id_user'];
		$resultado = $table->save();
		$ultimo_cliente = $table->id;
		// ----------------------------------guardar Imagen--------------------
		// $extension = $file->getClientOriginalExtension();
		// $file->move(public_path() . "/clientes/" . $user['id_user'], $id_cliente . "." . $extension);
		// $table->where('id', '=', $ultimo_cliente)->update(['imagen' => "http://192.168.100.16/appnext/public/clientes/" . $user['id_user'] . '/' . $id_cliente . "." . $extension]);
		if ($resultado)
			{
			return response()->json(['respuesta' => true], 200);
			}
		}
	// ------------------------------------- EDITAR CLIENTE ----------------------------
	public function edit(Request $request)

		{
		$table = new Clientes();
		$funciones = new funciones();
		$user = JWTAuth::parseToken()->authenticate();
		$file = $request->file('imagen');
		$id_cliente = $funciones->generarID();
		$id_cliente = $request->input('id');
		// ----------------------------------editar Imagen--------------------
		if (!is_null($request->file('imagen')))
			{
			$file = $request->file('imagen');
			$extension = $file->getClientOriginalExtension();
			$file->move(public_path() . "/clientes/" . $user['id_user'], $id_cliente . "." . $extension);
			$img = "http://192.168.100.16/appnext/public/clientes/" . $user['id_user'] . '/' . $id_cliente . "." . $extension;
			$table->where('id', '=', $id_cliente)->update(['imagen' => $img]);
			}
		$resultado = $table->where('id', '=', $id_cliente)->update(['nombre_comercial' => $request->input('nombre_comercial') , 'actividad_economica' => $request->input('actividad_economica') , 'razon_social' => $request->input('razon_social') , 'representante_legal' => $request->input('representante_legal') , 'cedula_representante' => $request->input('cedula_representante') , 'celular' => $request->input('celular') , 'telefono' => $request->input('telefono') , 'direccion' => $request->input('direccion') , 'correo' => $request->input('correo') , 'sitio_web' => $request->input('sitio_web') , 'facebook' => $request->input('facebook') , 'twitter' => $request->input('twitter') , 'google' => $request->input('google') , 'observaciones' => $request->input('observaciones') ]);
		if ($resultado)
			{
			return response()->json(true, 200);
			}
		}
	public function delete(Request $request)

		{
		$table = new Clientes();
		$user = JWTAuth::parseToken()->authenticate();
		$id_cliente = $request->input('id');
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $table->where('id', '=', $id_cliente)->update(['estado' => '0']);
		if ($resultado)
			{
			return response()->json(true, 200);
			}
		}
	public function get(Request $request)

		{
		$table = new Clientes();
		$user = JWTAuth::parseToken()->authenticate();
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $table->select(['nombre_comercial', 'actividad_economica', 'razon_social', 'representante_legal', 'cedula_representante', 'celular', 'telefono', 'direccion', 'correo', 'sitio_web', 'facebook', 'twitter', 'google', 'observaciones', 'imagen'])->where('id_empresa', '=', $user['id_user'])->where('estado', '=', '1')->orderBy('id', 'DESC')->get();
		if ($resultado)
			{
			return response()->json($resultado, 200);
			}
		}
	public function cliente_exist(Request $request)

		{
		$table = new Clientes();
		$user = JWTAuth::parseToken()->authenticate();
		// ------------------------------ Eliminar Cliente --------------------
		$resultado = $table->select('id')->where('ruc_empresa', '=', $request->input('ruc_empresa'))->orderBy('id', 'DESC')->get();
		if (count($resultado) == 0)
			{
			$client = new Client;
			$res = $client->request('GET', 'http://localhost/appserviciosnext/public/getDatos', ['json' => ['tipodocumento' => 'RUC', 'nrodocumento' => $request->input('ruc_empresa') ]]);
			$respuesta = json_decode($res->getBody() , true);
			return response()->json(["respuesta" => $respuesta], 200);
			}
		  else
			{
			return response()->json(["respuesta" => true], 200);
			}
		}
	}

