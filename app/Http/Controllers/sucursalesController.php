<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Sucursales;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\PasswrdsE;

class sucursalesController extends Controller
{
        public function getsucursales(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $tabla  = new Sucursales();
        $tablaE = new PasswrdsE();
        // $datos  = $tablaE->select('id_user')->where('remember_token', '=', $request->input('token'))->get();
        if (count($user) !== 0) {
            $sucursales = $tabla->select('*')->where('id_empresa', '=', $user['id_user'])->orderBy('codigo','ASC')->get();
            
            return response()->json(array(
                'sucursales' => $sucursales
            ));
        }
    }

public function set_categoria_sucursal(Request $request){

       	  $tabla  = new Sucursales();
       	  $user= JWTAuth::parseToken()->authenticate();
       	  $resultado=$tabla->where('id_empresa','=',$user['id_user'])->where('codigo','=',$request->input('codigo'))->
       	  update(["categoria"=>$request->input('categoria'),"descripcion"=>$request->input('descripcion')]);
if ($resultado) {
	       	return response()->json(array("respuesta"=>true),200);
}else        	return response()->json(array("respuesta"=>false),200);

       }
}
