<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PasswrdsE;
use App\Personas;
use App\Empresas;
use App\Sucursales;

class datosController extends Controller
{
   public function __construct(){
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

   public function  getsucursales(Request $request){

   	$tabla=new Sucursales();
   	$tablaE=new PasswrdsE();
   	$datos=$tablaE->select('id_user')->where('remember_token','=',$request->input('token'))->get();
   	// echo count($datos);
   	if (count($datos)!==0) {
   		   	$sucursales=$tabla->select('*')->where('id_empresa','=',$datos[0]['id_user'])->get();

   	return response()->json(array('sucursales'=>$sucursales));
   	}
   	// else{
   	// 	// return response()->json(false,401);
   	// }

   }

   public function  getDatosE(Request $request){

   	$tabla=new Empresas();
   	$tablaE=new PasswrdsE();
   	$datos=$tablaE->select('id_user')->where('remember_token','=',$request->input('token'))->get();
   	// echo count($datos);
   	if (count($datos)!==0) {
   		   	$empresa=$tabla->select('*')->where('id_empresa','=',$datos[0]['id_user'])->get();

   	return response()->json(array('empresa'=>$empresa));
   	}
   	// else{
   	// 	// return response()->json(false,401);
   	// }

   }
}
