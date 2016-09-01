<?php

namespace App\Http\Controllers\inventario;

use Illuminate\Http\Request;
use App\Http\Requests;
//--------------------- Modelos 
use App\grupo;
use App\Sucursales;
//----------------------- Funciones
use App\libs\Funciones;
//-------------------------- Autenticacione-------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------ controller class-----------------
use App\Http\Controllers\Controller;

class grupoController extends Controller
{
    public function __construct(Request $request){
    	$this->grupo= new grupo();
    	$this->sucursal= new Sucursales();
    	//--------------------- Funciones ----------
    	$this->funciones= new Funciones();
    	//-------------------------- Autenticacione-------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Id Sucursal ---------------------
        if ($request->input('sucursal')!=null) {
        $datos=$this->sucursal->select('id_sucursal')->where('codigo','=',$request->input('sucursal'))->where('id_empresa','=',$this->user['id_user'])->get();
        $this->id_sucursal=$datos[0]['id_sucursal'];
        }
    }
    public function add_grupo(Request $request) {

    	$datos=$this->grupo->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
    	$codigo=(integer)$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);

    	$this->grupo->id=$this->funciones->generarId();
    	$this->grupo->codigo=$codigo;
    	$this->grupo->descripcion=$request->input('descripcion');
    	$this->grupo->estado=1;
    	$this->grupo->id_sucursal=$this->id_sucursal;
    	$save=$this->grupo->save();
    	if ($save) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function update_grupo(Request $request) {
    	$update=$this->grupo->where('id',$request->input('id'))->update(['descripcion'=>$request->input('descripcion')]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function delete_grupo(Request $request) {
    	$update=$this->grupo->where('id',$request->input('id'))->update(['estado'=>0]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function ultimo_code_grupo(Request $request) {

    	$datos=$this->grupo->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
        $codigo=$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);
    	return response()->json(['respuesta'=>true,'codigo'=>$codigo],200);
    	
    }
    public function get_grupos(Request $request) {
		$currentPage = $request->input('pagina_actual');
    	$limit = $request->input('limit');
    	$resultado=$this->grupo->where('id_sucursal',$this->id_sucursal)->where('estado',1)->get();
    	$resultado=$this->funciones->paginarDatos($resultado,$currentPage,$limit);
		return response()->json(['respuesta' => $resultado], 200);
    }
}
