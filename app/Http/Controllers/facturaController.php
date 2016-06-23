<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Facturas;
use Auth;
use App\PasswrdsP;
use App\PasswrdsE;
use App\libs\Funciones;
use App\libs\Funciones_fac;

class facturaController extends Controller
{
    public function add_fac_bdd(Request $request){
        $Funciones_fac=new Funciones_fac();

             $user = JWTAuth::parseToken()->authenticate();
             $pos=stripos($user->email, '@');
             $documento=substr($user->email,0,$pos);
             $tamaño=strlen($documento);
    
switch ($tamaño) {

    // case 10:
    //     // $auth = Auth::guard('web');
    //     $tabla =    new PasswrdsP();
    //     break;
    
    case 13:
        // $auth = Auth::guard('usersE');
        $tabla =    new PasswrdsE();
        $datos= $tabla->select('pass_email')->where('email','=',$user->email)->get();
        break;
}

        $resultado=$Funciones_fac->leer($user->email,$datos[0]['pass_email'],$user->id_user);
        if ($resultado==null) {
         return response()->json(true,200);
        }else{
            return response()->json([$request->input('token'),"resultados"=>$resultado],500);
        }
        // print_r($resultado);
        // print_r($user->email);
    }
}
