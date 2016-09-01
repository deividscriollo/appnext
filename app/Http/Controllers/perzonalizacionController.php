<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//--------------------------------------- Autenticacion --------------------------------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//--------------------------------------- Modelos --------------------------------
use App\Sucursales;
use App\Empresas;
use App\Extras;
use App\PasswrdsE;
//--------------------------------------- Extras --------------------------------
use Hash;

class perzonalizacionController extends Controller
{

  public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        //-------------------- Modelos -------------------------------
        $this->tablaE = new PasswrdsE();
        $this->tablaExtras = new Extras();
        //--------------------------------------- Autenticacion --------------------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Id Sucursal ---------------------
        if ($request->input('sucursal')!=null) {
        $datos=$this->sucursal->select('id_sucursal')->where('codigo','=',$request->input('sucursal'))->where('id_empresa','=',$this->user['id_user'])->get();
        $this->id_sucursal=$datos[0]['id_sucursal'];
        }
        //--------------------------------------- Funciones --------------------------------

    }
   public function addExtra(Request $request){

           $this->tablaExtras->tipo = $request->input('tipo');
           $this->tablaExtras->dato = $request->input('dato');
           $this->tablaExtras->id_empresa = $this->user['id_user'];
           $this->tablaExtras->pass_estado = 0;
           $saved = $this->tablaExtras->save();
        if(!$saved){
             return response()->json(["respuesta"=>false],200);
        }else{
            return response()->json(["respuesta"=>true],200);
        }
   }

   public function change_pass(Request $request){

        $result = $this->tablaE->where('id_user','=',$this->user['id_user'])->update(['password'=>bcrypt($request->input('new_pass')),'pass_estado'=>1]);
           // $this->tablaE->where('id_user','=',$this->user['id_user'])->update(['pass_estado'=>1]);
        if(!$result){
            App::abort(500, 'Error');
        }else{
            return response()->json(["respuesta"=>true],200);
        }
   }

   public function pass_state(Request $request){

        $result = $this->tablaE->select('pass_estado')->where('id_user','=',$this->user['id_user'])->get();
        return response()->json(["estado"=>$result['pass_estado']],200);
   }

   public function verify_pass(Request $request){
       
                                                                                                                
        $result = $this->tablaE->select('password')->where('id_user','=',$this->user['id_user'])->first();
        if (Hash::check($request->input('pass'), $result['password'])) {
           return response()->json(["respuesta"=>true],200);
        }
        else return response()->json(["respuesta"=>false],200);
   }

   public function update_info(Request $request){

        // $this->tabla = new Extras(
        //
        // foreach ($request->input('telefonos') as $key => $telefono) {
        //   $resultado=$this->tablaExtras->where('id','=',$telefono['id'])->update(['dato'=>$telefono['dato']]);
        // }

        // $this->tabla = new Sucursales();
        // $resultado=$this->tablaExtras->where('id_empresa','=',$this->user['id_user'])
        //                  ->where('codigo','=',$request->input('codigo'))
        //                  ->update(['direccion'=>$request->input('direccion')]);

        // $this->tabla = new Empresas();
        // $resultado=$this->tablaExtras->where('id_empresa','=',$this->user['id_user'])
        //                  ->update(['actividad_economica'=>$request->input('actividad')]);



// if ($resultado) {
     return response()->json(["respuesta"=>$request->all()],200);
// }

 
   }
}