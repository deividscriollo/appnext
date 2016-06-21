<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PasswrdsE;
use App\PasswrdsP;
use App\Personas;
use App\Empresas;
use App\Sucursales;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class datosController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }
    
    public function getsucursales(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $tabla  = new Sucursales();
        $tablaE = new PasswrdsE();
        // $datos  = $tablaE->select('id_user')->where('remember_token', '=', $request->input('token'))->get();
        if (count($user) !== 0) {
            $sucursales = $tabla->select('*')->where('id_empresa', '=', $user['id_user'])->get();
            
            return response()->json(array(
                'sucursales' => $sucursales
            ));
        }
    }
    
    public function getDatosE(Request $request)
    {       
        $user = JWTAuth::parseToken()->authenticate();
        // $token  = JWTAuth::getToken();
        $tabla  = new Empresas();
        $tablaE = new PasswrdsE();
        // $datos  = $tablaE->select('id_user')->where('remember_token', '=', $token)->get();
        if (count($user) !== 0) {
            $empresa = $tabla->select('*')->where('id_empresa', '=', $user['id_user'])->get();
            
            return response()->json(array(
                'empresa' => $empresa
            ));
        }else{
            return response()->json("Error",401);
        }
        
    }
    
    public function getDatosP(Request $request)
    {
        
        $token = JWTAuth::getToken();
        
        $tabla  = new Personas();
        $tablaP = new PasswrdsP();
        $datos  = $tablaP->select('id_user')->where('remember_token', '=', $token)->get();
        if (count($datos) !== 0) {
            $persona = $tabla->select('*')->where('id_persona', '=', $datos[0]['id_user'])->get();
            
            return response()->json(array(
                'persona' => $persona
            ));
        }
        echo $token;
        
    }
}