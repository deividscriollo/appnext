<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Extras;
use App\PasswrdsE;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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

   public function change_pass(Request $request){
        $tablaE = new PasswrdsE();
        $user = JWTAuth::parseToken()->authenticate();
           $result = $tablaE->where('id_user','=',$user['id_user'])->update(['password'=>bcrypt($request->input('new_pass'))]);
        if(!$result){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
   }

   public function change_pass_state(Request $request){
        $tablaE = new PasswrdsE();
        $user = JWTAuth::parseToken()->authenticate();
           $result = $tablaE->select('password')->where('id_user','=',$user['id_user'])->get();
        if(!$result){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
   }
}