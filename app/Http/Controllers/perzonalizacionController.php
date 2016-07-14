<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Extras;
use App\PasswrdsE;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Hash;

class perzonalizacionController extends Controller
{

  public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }
   public function addExtra(Request $request){

           $tablaE = new PasswrdsE();
           $user = JWTAuth::parseToken()->authenticate();
           $tabla = new Extras();
           $tabla->tipo = $request->input('tipo');
           $tabla->dato = $request->input('dato');
           $tabla->id_empresa = $user['id_user'];
           $tabla->pass_estado = 0;
           $saved = $tabla->save();
        if(!$saved){
             return response()->json(["respuesta"=>false],200);
        }else{
            return response()->json(["respuesta"=>true],200);
        }
   }

   public function change_pass(Request $request){
        $tablaE = new PasswrdsE();
        $user = JWTAuth::parseToken()->authenticate();
           $result = $tablaE->where('id_user','=',$user['id_user'])->update(['password'=>bcrypt($request->input('new_pass')),'pass_estado'=>1]);
           // $tablaE->where('id_user','=',$user['id_user'])->update(['pass_estado'=>1]);
        if(!$result){
            App::abort(500, 'Error');
        }else{
            return response()->json(["respuesta"=>true],200);
        }
   }

   public function pass_state(Request $request){
        $tablaE = new PasswrdsE();
        $user = JWTAuth::parseToken()->authenticate();
        $result = $tablaE->select('pass_estado')->where('id_user','=',$user['id_user'])->get();
        return response()->json(["estado"=>$result['pass_estado']],200);
   }

   public function verify_pass(Request $request){
       
        $tablaE = new PasswrdsE();
        $user = JWTAuth::parseToken()->authenticate();
        $result = $tablaE->select('password')->where('id_user','=',$user['id_user'])->first();
        if (Hash::check($request->input('pass'), $result['password'])) {
           return response()->json(["respuesta"=>true],200);
        }
        else return response()->json(["respuesta"=>false],200);
   }
}