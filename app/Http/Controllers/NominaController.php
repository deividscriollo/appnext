<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Nomina;
use App\libs\Funciones;

class NominaController extends Controller
{
  public function add_nomina(Request $request){
  	$nomina=new Nomina();
  	$funciones=new Funciones();
  	$nomina->id=$funciones->generarID();
  	$nomina->periodicidad = $request->input('periodicidad');
  	$nomina->registro_patronal=$request->input('registro_patronal');
  	$nomina->dias =$request->input('dias');
  	$nomina->fecha_inicio=$request->input('fecha_inicio');
  	$nomina->id_sucursal=$request->input('id_sucursal');
  	$nomina->estado=1;
  	$save=$nomina->save();
  	if ($save) {
  		  	return response()->json(['respuesta'=>true],200);
  	}else{
  		return response()->json(['respuesta'=>false],200);
  	}

  }

   public function update_nomina(Request $request){

  	$nomina=new Nomina();
	$update=$nomina->where('id','=',$request->input('id'))->update([
    'periodicidad'=>$request->input('periodicidad'),
  	'registro_patronal'=>$request->input('registro_patronal'),
  	'dias'=>$request->input('dias'),
  	'fecha_inicio'=>$request->input('fecha_inicio')
		]);

  	if ($update) {
  		  	return response()->json(['respuesta'=>true],200);
  	}else{
  		return response()->json(['respuesta'=>false],200);
  	}
  }

  public function delete_nomina(Request $request){

  	$nomina=new Nomina();
	$delete=$nomina->where('id','=',$request->input('id'))->update([
    'estado'=>0
		]);
  	if ($delete) {
  		  	return response()->json(['respuesta'=>true],200);
  	}else{
  		return response()->json(['respuesta'=>false],200);
  	}
  }

   public function get_nomina(Request $request){

   	// $items = Item::paginate(10);

  	$nomina=new Nomina();
	$datos=$nomina->paginate($request->input('items'));

  		  	return response()->json(['respuesta'=>$datos],200);
  	
  }
}
