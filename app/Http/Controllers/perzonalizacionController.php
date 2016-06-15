<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Extras;
use App\PasswrdsE;

class perzonalizacionController extends Controller
{
   public function addExtra(Request $request){

           $tablaE = new PasswrdsE();
           $datos = $tablaE->select('id_user')->where('remember_token','=',$request->input('token'))->get();
           $tabla = new Extras();
           $tabla->dato = $request->input('dato');
           $tabla->tipo = $request->input('tipo');
           $tabla->id_empresa = $datos[0]['id_user'];
           $saved = $tabla->save();
        if(!$saved){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
   }
}