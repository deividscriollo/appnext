<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//---------------------------- Modelos ---------------
use App\Gastos;
//- Funciones
use App\libs\Funciones;

class gastosController extends Controller
{
	public function __construct(){
		//-------------------------- Modelos
		//--------------------------- Funciones
		$this->funciones=new Funciones();
	}
   
   public function add_gasto(){

   	$gastos=array(
                    ["nombre"=>"Auto y Transporte"],
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

   	foreach ($gastos as $key => $gasto) {
		$tablaGastos=new Gastos();
    	$tablaGastos->id=$this->funciones->generarId();
    	$tablaGastos->descripcion=$gasto['nombre'];
    	$resultado=$tablaGastos->save();
		}
    	if ($resultado) {
    		return response()->json(["respuesta"=>true],200);
    	}
   }

   public function get_gastos(){

		$tablaGastos=new Gastos();
    	$resultados=$tablaGastos->get();

    		return response()->json(["respuesta"=>true,'gastos'=>$resultados],200);

   }
}
