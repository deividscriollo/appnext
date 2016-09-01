<?php

namespace App\Http\Controllers\inventario;

use Illuminate\Http\Request;

use App\Http\Requests;
//--------------------- Modelos 
use App\maestro_articulo;
use App\Sucursales;
//----------------------- Funciones
use App\libs\Funciones;
//-------------------------- Autenticacione-------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------ controller class-----------------
use App\Http\Controllers\Controller;

class maestro_articuloController extends Controller
{
    public function __construct(Request $request){
    	$this->maestro_articulo= new maestro_articulo();
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
    public function add_maestro_articulo(Request $request) {

    	$datos=$this->maestro_articulo->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
    	$codigo=(integer)$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);

		$this->maestro_articulo->id=$this->funciones->generarId();
		$this->maestro_articulo->codigo=$codigo;
		$this->maestro_articulo->descripcion=$request->input('descripcion');
		$this->maestro_articulo->grupo=$request->input('grupo');
		$this->maestro_articulo->marca=$request->input('marca');
		$this->maestro_articulo->modelo=$request->input('modelo');
		$this->maestro_articulo->color=$request->input('color');
		$this->maestro_articulo->anio_fabricacion=$request->input('anio_fabricacion');
		$this->maestro_articulo->num_partes=$request->input('num_partes');
		$this->maestro_articulo->num_placa=$request->input('num_placa');
		$this->maestro_articulo->num_serie=$request->input('num_serie');
		$this->maestro_articulo->pais_origen=$request->input('pais_origen');
		$this->maestro_articulo->num_factura=$request->input('num_factura');
		$this->maestro_articulo->num_guia=$request->input('num_guia');
		$this->maestro_articulo->fecha_adquisicion=$request->input('fecha_adquisicion');
		$this->maestro_articulo->valor_compra=$request->input('valor_compra');
		$this->maestro_articulo->modo_adquisicion=$request->input('modo_adquisicion');
		$this->maestro_articulo->estado_bn=$request->input('estado_bn');
		$this->maestro_articulo->observaciones=$request->input('observaciones');
		$this->maestro_articulo->fecha_inicio=$request->input('fecha_inicio');
		$this->maestro_articulo->estado=1; 
		$this->maestro_articulo->id_sucursal=$this->id_sucursal;

    	$save=$this->maestro_articulo->save();
    	if ($save) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function update_maestro_articulo(Request $request) {
    	$update=$this->maestro_articulo->where('id',$request->input('id'))->update([
		'descripcion'=>$request->input('descripcion'),
		'grupo'=>$request->input('grupo'),
		'marca'=>$request->input('marca'),
		'modelo'=>$request->input('modelo'),
		'color'=>$request->input('color'),
		'anio_fabricacion'=>$request->input('anio_fabricacion'),
		'num_partes'=>$request->input('num_partes'),
		'num_placa'=>$request->input('num_placa'),
		'num_serie'=>$request->input('num_serie'),
		'pais_origen'=>$request->input('pais_origen'),
		'num_factura'=>$request->input('num_factura'),
		'num_guia'=>$request->input('num_guia'),
		'fecha_adquisicion'=>$request->input('fecha_adquisicion'),
		'valor_compra'=>$request->input('valor_compra'),
		'modo_adquisicion'=>$request->input('modo_adquisicion'),
		'estado_bn'=>$request->input('estado_bn'),
		'observaciones'=>$request->input('observaciones'),
		'fecha_inicio'=>$request->input('fecha_inicio')
    		]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function delete_maestro_articulo(Request $request) {
    	$update=$this->maestro_articulo->where('id',$request->input('id'))->update(['estado'=>0]);
    	if ($update) {
    		return response()->json(['respuesta'=>true],200);
    	}
    }

    public function ultimo_code_maestro_articulo(Request $request) {
    	$datos=$this->maestro_articulo->select('codigo')->where('id_sucursal',$this->id_sucursal)->orderBy('id','DESC')->first();
        $codigo=$datos['codigo']+1;
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);
    	return response()->json(['respuesta'=>true,'codigo'=>$codigo],200);
    }
    public function get_maestro_articulos(Request $request) {
		$currentPage = $request->input('pagina_actual');
    	$limit = $request->input('limit');
    	$resultado=$this->maestro_articulo->where('id_sucursal',$this->id_sucursal)->where('estado',1)->get();
    	$resultado=$this->funciones->paginarDatos($resultado,$currentPage,$limit);
		return response()->json(['respuesta' => $resultado], 200);
    }
}
