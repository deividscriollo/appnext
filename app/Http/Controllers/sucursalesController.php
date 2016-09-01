<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//---------------------------- Autenticacion ------------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//---------------------------- Modelos ------------
use App\PasswrdsE;
use App\Sucursales;

class sucursalesController extends Controller
{

  public function __construct()
    {
        //----------------------------------------------- Modelos --------------
        $this->tablaSucursales  = new Sucursales();
        $this->tablapassE = new PasswrdsE();
        // --------------------------------------- Autenticacion --------------------
        $this->user = JWTAuth::parseToken()->authenticate();
    }

public function getsucursales(Request $request)
    {
        // $datos  = $tablaE->select('id_user')->where('remember_token', '=', $request->input('token'))->get();
            $sucursales = $this->tablaSucursales->select('*')->where('id_empresa', '=', $this->user['id_user'])->orderBy('codigo','ASC')->get();
            foreach ($sucursales as $key => $value) {
              $value['direccion']=trim(preg_replace('/\n+/', '', $value['direccion']));
              $value['direccion']=trim(preg_replace('/\t+/', '', $value['direccion']));
            }
            return response()->json(array(
                'sucursales' => $sucursales
            ));
        
    }

public function set_categoria_sucursal(Request $request){

       	  $this->tablaSucursales  = new Sucursales();
       	  $resultado=$this->tablaSucursales->where('id_empresa','=',$this->user['id_user'])->where('codigo','=',$request->input('sucursal'))->
       	  update(["categoria"=>$request->input('categoria'),"descripcion"=>$request->input('descripcion')]);
if ($resultado) {
	       	return response()->json(array("respuesta"=>true),200);
}else    	return response()->json(array("respuesta"=>false),200);

       }
}
