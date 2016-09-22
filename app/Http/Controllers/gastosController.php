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
                    ["codigo"=>"4" ,"nombre"=>"Alimentación"],
                    ["codigo"=>"1" ,"nombre"=>"Auto y Transporte"],
                    ["codigo"=>"2" ,"nombre"=>"Educación"],
                    ["codigo"=>"9" ,"nombre"=>"Electrónicos"],
                    ["codigo"=>"3" ,"nombre"=>"Entretenimiento"],
                    ["codigo"=>"12" ,"nombre"=>"Financiero / Banco"],
                    ["codigo"=>"6" ,"nombre"=>"Hogar"],
                    ["codigo"=>"17" ,"nombre"=>"Honorarios Profesionales"],
                    ["codigo"=>"18" ,"nombre"=>"Impuestos y Tributos"],
                    ["codigo"=>"15" ,"nombre"=>"Mascota"],
                    ["codigo"=>"11" ,"nombre"=>"Otros"],
                    ["codigo"=>"5" ,"nombre"=>"Salud"],
                    ["codigo"=>"13" ,"nombre"=>"Seguro"],
                    ["codigo"=>"16" ,"nombre"=>"Servicios Básicos"],
                    ["codigo"=>"14" ,"nombre"=>"Telecomunicación / Internet"],
                    ["codigo"=>"7" ,"nombre"=>"Vestimenta"],
                    ["codigo"=>"8" ,"nombre"=>"Viajes"],
                    ["codigo"=>"10" ,"nombre"=>"Vivienda"]);

   	foreach ($gastos as $key => $gasto) {
		$tablaGastos=new Gastos();
    	$tablaGastos->id=$this->funciones->generarId();
        $tablaGastos->codigo=$gasto['codigo'];
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

    		return response()->json(['respuesta'=>$resultados],200);

   }
}
