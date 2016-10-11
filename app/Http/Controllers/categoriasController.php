<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Categorias;
use App\libs\Funciones;

class categoriasController extends Controller
{
    
     public function addCategoria(Request $request)
    {
    	$categorias=array(  "BELLEZA",
                            "VARIOS",
                            "ALIMENTOS Y BEBIDAS",
                            "AGROPECUARIA",
                            "AUTOMOTRIZ",
                            "BAZAR Y PAPELERÍA",
                            "CALZADOS-CARTERAS",
                            "CONTABILIDAD",
                            "CENTROS COMERCIALES",
                            "COMPUTACIÓN – INFORMÁTICA",
                            "CONSTRUCCIÓN",
                            "ELECTRODOMÉSTICOS",
                            "CARÁMICA",
                            "FARMACIA",
                            "FERRETERÍAS",
                            "ESTÉTICA COSMÉTICA",
                            "GASOLINERAS",
                            "MATERIAL ELÉCTRICO",
                            "PARA EL HOGAR",
                            "PARA LA OFICINA",
                            "PINTURA",
                            "PRENDAS DE VESTIR",
                            "TELEFÓNICA",
                            "TEXTIL MATERIAS PRIMAS",
                            "VIDRIOS-ALUMINIO,S",
                            "ALIMENTICIA",
                            "CERÁMICA",
                            "MECÁNICA",
                            "METÁLICA",
                            "MADERERA",
                            "MUEBLES",
                            "TEXTIL PRENDAS DE VESTIR",
                            "GREMIOS",
                            "PLÁSTICOS",
                            "MECANICA AUTOMOTRÍZ",
                            "AGENCIA DE TURISMOS",
                            "ASEGURADORAS",
                            "ACEITES-LUBRICANTES",
                            "ASESORÍA",
                            "CASA DE SALUD",
                            "CENTROS DE DIVERSIÓN",
                            "CENTROS EDUCATIVOS",
                            "COPIAS",
                            "ENCOMIENDAS",
                            "GASTRONOMÍA",
                            "HOSPEDAJE",
                            "IMPRENTAS",
                            "INS. FINANCIERA",
                            "MEDIOS – COMUNICACIÓN",
                            "PROFESIONALES",
                            "RELIGIOSOS",
                            "TÉCNICOS",
                            "METAL MECÁNICA",
                            "TRANSPORTES",
                            "MODISTA",
                            "PANADEROS");

foreach ($categorias as $key => $value) {
    $tabla = new Categorias();
        $funciones = new Funciones(); 
        $tabla->id=$funciones->generarID();
        $tabla->nombre=$value;
        $tabla->estado=1;
        $resultado =$tabla->save();
}

        // $tabla = new Categorias();
        // $funciones = new Funciones(); 
        // $tabla->id=$funciones->generarID();
        // $tabla->nombre=$request->input('nombre');
        // $resultado =$tabla->save();
        if ($resultado) {
       return response()->json(array(
                'respuesta' => true
            ),200);
        }else  return response()->json(array(
                'respuesta' => false
            ),200);
            
   
        
    }
     public function getCategorias(Request $request)
    {
        $tabla = new Categorias();
        $categorias = $tabla->orderBy('nombre','ASC')->get();
            
            return response()->json(array(
                'categorias' => $categorias
            ));
        
    }
}
