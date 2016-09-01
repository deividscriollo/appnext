<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//----------------------------------- Modelos -----------
use App\PasswrdsE;
use App\PasswrdsP;
use App\Personas;
use App\Empresas;
use App\Sucursales;
//----------------------------------- Autenticacion -----------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class datosController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        //----------------------------------- Modelos -----------
        $this->tablaEmpresas  = new Empresas();
        $this->tablapassE = new PasswrdsE();
        $this->tablaPersonas  = new Personas();
        $this->tablapassP = new PasswrdsP();
        //-------------------------- Autenticacione-------
        $this->user = JWTAuth::parseToken()->authenticate();

    }
    
    public function getDatosE(Request $request)
    {       
    $empresa = $this->tablaEmpresas->select('*')->where('id_empresa', '=', $this->user['id_user'])->get();
    return response()->json(array('empresa' => $empresa));
    }
    
    public function getDatosP(Request $request)
    {
    $persona = $tablaPersonas->select('*')->where('id_persona', '=', $this->user['id_user'])->get();
    return response()->json(array('persona' => $persona));
    }
}