<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Tipo_documentos;
use App\Tipo_consumos;
use App\libs\Funciones;

class tiposController extends Controller
{
    public function __construct(){
    	$this->tipo_documentos=new Tipos_documentos();
    	$this->tipo_consumos=new Tipos_consumos();
        $this->funciones=new Funciones();
    }
    public function add_tipo_consumos(){
    	
    	$array=array(["nombre"=>"Auto y Transporte"],
                    ["nombre"=>"Educación"],
                    ["nombre"=>"Entretenimiento"],
                    ["nombre"=>"Alimentación"],
                    ["nombre"=>"Salud"],
                    ["nombre"=>"Hogar"],
                    ["nombre"=>"Vestimenta"],
                    ["nombre"=>"Viajes"],
                    ["nombre"=>"Electrónicos"],
                    ["nombre"=>"Vivienda"],
                    ["nombre"=>"Otros"],
                    ["nombre"=>"Financiero / Banco"],
                    ["nombre"=>"Seguro"],
                    ["nombre"=>"Telecomunicación / Internet"],
                    ["nombre"=>"Mascota"],
                    ["nombre"=>"Servicios Básicos"],
                    ["nombre"=>"Honorarios Profesionales"],
                    ["nombre"=>"Impuestos y Tributos"]);

		foreach ($array as $key => $provincia) {
		$tabla=new Tipos_documentos();
    	$tabla->id=$this->funciones->generarId();
    	$tabla->descripcion=$provincia['nombre'];
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

    public function get_tipo_consumos(){
    	$consumos=$this->tipo_consumos->get();
    	return response()->json(['respuesta'=>$consumos],200);
    }
}
