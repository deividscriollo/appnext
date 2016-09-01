<?php

namespace App\Http\Controllers\inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
//--------------------- Modelos 
use App\estado_bn;
use App\Sucursales;
//----------------------- Funciones
use App\libs\Funciones;
//-------------------------- Autenticacione-------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------ controller class-----------------
use App\Http\Controllers\Controller;


class estadobnController extends Controller
{
    public function __construct(Request $request){
    	$this->estadobn= new estado_bn();
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
    public function add_estadobn(Request $request) {

    	$datos=$this->estadobn->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
    	$codigo=(integer)$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);

    	$this->estadobn->id=$this->funciones->generarId();
    	$this->estadobn->codigo=$codigo;
    	$this->estadobn->descripcion=$request->input('descripcion');
    	$this->estadobn->estado=1;
    	$this->estadobn->id_sucursal=$this->id_sucursal;
    	$save=$this->estadobn->save();
    	if ($save) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function update_estadobn(Request $request) {
    	$update=$this->estadobn->where('id',$request->input('id'))->update(['descripcion'=>$request->input('descripcion')]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function delete_estadobn(Request $request) {
    	$update=$this->estadobn->where('id',$request->input('id'))->update(['estado'=>0]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function ultimo_code_estadobn(Request $request) {

    	$datos=$this->estadobn->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
        $codigo=$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);
    	return response()->json(['respuesta'=>true,'codigo'=>$codigo],200);
    	
    }
    public function get_estadobnes(Request $request) {
		$currentPage = $request->input('pagina_actual');
    	$limit = $request->input('limit');
    	$resultado=$this->estadobn->where('id_sucursal',$this->id_sucursal)->where('estado',1)->get();
    	$resultado=$this->funciones->paginarDatos($resultado,$currentPage,$limit);
		return response()->json(['respuesta' => $resultado], 200);
    }
}
