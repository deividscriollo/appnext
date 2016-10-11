<?php

namespace App\Http\Controllers\inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
//--------------------- Modelos 
use App\bajas;
use App\Sucursales;
//----------------------- Funciones
use App\libs\Funciones;
//-------------------------- Autenticacione-------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------ controller class-----------------
use App\Http\Controllers\Controller;

class bajasController extends Controller
{
    public function __construct(Request $request){
    	$this->bajas= new bajas();
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
    public function add_bajas(Request $request) {

    	$this->bajas->id=$this->funciones->generarId();
    	$this->bajas->maestro_articulo=$request->input('maestro_articulo');
    	$this->bajas->motivos_baja=$request->input('motivos_baja');
    	$this->bajas->fecha_baja=$request->input('fecha_baja');
    	$this->bajas->estado=1;
    	$this->bajas->id_sucursal=$this->id_sucursal;
    	$save=$this->bajas->save();
    	if ($save) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function update_bajas(Request $request) {
    	$update=$this->bajas->where('id',$request->input('id'))->update(['motivos_baja'=>$request->input('motivos_baja')]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function delete_bajas(Request $request) {
    	$update=$this->bajas->where('id',$request->input('id'))->update(['estado'=>0]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function get_bajas(Request $request) {
		$currentPage = $request->input('pagina_actual');
    	$limit = $request->input('limit');
    	$resultado=$this->bajas->where('id_sucursal',$this->id_sucursal)->where('estado',1)->get();
    	$resultado=$this->funciones->paginarDatos($resultado,$currentPage,$limit);
		return response()->json(['respuesta' => $resultado], 200);
    }
}
