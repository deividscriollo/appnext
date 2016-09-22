<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Tipo_documentos;
use App\libs\Funciones;

class tiposController extends Controller
{
    public function __construct(){
    	$this->tipo_documentos=new Tipo_documentos();
        $this->funciones=new Funciones();
    }
    public function add_tipo_documentos(){
    	
    	$array=array(["codigo"=>"01","nombre"=>"FACTURA"],
                    ["codigo"=>"04","nombre"=>"NOTA DE CRÉBITO"],
                    ["codigo"=>"05","nombre"=>"NOTA DE DÉBITO"],
                    ["codigo"=>"06","nombre"=>"GUÍA DE REMISIÓN"],
                    ["codigo"=>"07","nombre"=>"COMPROBANTE DE RETENCIÓN"],
                    ["codigo"=>"00","nombre"=>"FACTURAS FÍSICAS"]);

		foreach ($array as $key => $provincia) {
		$tabla=new Tipo_documentos();
    	$tabla->id=$this->funciones->generarID();
    	$tabla->descripcion=$provincia['nombre'];
        $tabla->codigo=$provincia['codigo'];
    	$tabla->estado=TRUE;
    	$resultado=$tabla->save();
		}
    	if ($resultado) {
    		return response()->json(["respuesta"=>true],200);
    	}

    }
    public function get_tipo_documentos(){
    	$documentos=$this->tipo_documentos->get();
    	return response()->json(['respuesta'=>$documentos],200);
    }

}
