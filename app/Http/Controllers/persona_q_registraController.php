<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\regpersona_empresas;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\libs\Funciones;
use App\PasswrdsE;


class persona_q_registraController extends Controller
{
    public function get_datos(Request $request){
        
        $tablaPersonareg  = new regpersona_empresas();

        $user = JWTAuth::parseToken()->authenticate();

        $resultado=$tablaPersonareg->select('nombres_apellidos')->where('id_empresa','=',$user['id_user'])->get();
        if (count($resultado)==0) {
        	return response()->json(["respuesta"=>false],200);
        }else
        return response()->json(["respuesta"=>true],200);

    }

     public function set_datos(Request $request){
        
        $tablaPersonassreg  = new regpersona_empresas();
        $tablaPass  = new PasswrdsE();
        $funciones=new Funciones();
        $user = JWTAuth::parseToken()->authenticate();

		$tablaPersonassreg->idp_regE=$funciones->generarID();
		$tablaPersonassreg->nombres_apellidos=$request->input('nombres').' '.$request->input('apellidos');
		$tablaPersonassreg->fecha_nacimiento=$request->input('fecha_nac');
		$tablaPersonassreg->correo=$user['email'];
		$tablaPersonassreg->telefono=$request->input('telefono');
		$tablaPersonassreg->celular=$request->input('nombres');
		$tablaPersonassreg->estado=1;
		$tablaPersonassreg->id_empresa=$user['id_user'];
        $resultado=$tablaPersonassreg->save();

        // ---------------------------------------- Actualizar Pass ---------------------
        $result = $tablaPass->where('id_user','=',$user['id_user'])->update(['password'=>bcrypt($request->input('pw2')),'pass_estado'=>1]);

        if ($resultado&&$result) {
        	return response()->json(["respuesta"=>true],200);
        }else
        return response()->json(["respuesta"=>false],200);

    }
}
