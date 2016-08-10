<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Provincias;
use App\libs\Funciones;

class provinciasController extends Controller
{
    public function add_provincia(Request $request){
    	$funciones=new Funciones();
		$array=array([
            "id"=> "20150326115500551439e48586a", "nombre"=> "Azuay", "codtelefonico"=> '072'
        ]
        , [
            "id"=> "20150326115500551439e48dac4", "nombre"=> "Bolivar", "codtelefonico"=> '032'
        ]
        , [
            "id"=> "20150326115500551439e48dd2d", "nombre"=> "Cañar", "codtelefonico"=> '072'
        ]
        , [
            "id"=> "20150326115500551439e48df24", "nombre"=> "Carchi", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48e114", "nombre"=> "Chimborazo", "codtelefonico"=> '032'
        ]
        , [
            "id"=> "20150326115500551439e48e30a", "nombre"=> "Cotopaxi", "codtelefonico"=> '032'
        ]
        , [
            "id"=> "20150326115500551439e48e503", "nombre"=> "El Oro", "codtelefonico"=> '072'
        ]
        , [
            "id"=> "20150326115500551439e48e716", "nombre"=> "Esmeraldas", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48e8dd", "nombre"=> "Galápagos", "codtelefonico"=> '052'
        ]
        , [
            "id"=> "20150326115500551439e48eaa8", "nombre"=> "Guayas", "codtelefonico"=> '042'
        ]
        , [
            "id"=> "20150326115500551439e48ec62", "nombre"=> "Imbabura", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48ee16", "nombre"=> "Loja", "codtelefonico"=> '072'
        ]
        , [
            "id"=> "20150326115500551439e48ef9b", "nombre"=> "Los Rios", "codtelefonico"=> '052'
        ]
        , [
            "id"=> "20150326115500551439e48f0fa", "nombre"=> "Manabí", "codtelefonico"=> '052'
        ]
        , [
            "id"=> "20150326115500551439e48f290", "nombre"=> "Morona Santiago", "codtelefonico"=> '072'
        ]
        , [
            "id"=> "20150326115500551439e48f43d", "nombre"=> "Napo", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48f5b8", "nombre"=> "Orellana", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48f72a", "nombre"=> "Pastaza", "codtelefonico"=> '032'
        ]
        , [
            "id"=> "20150326115500551439e48f899", "nombre"=> "Pichincha", "codtelefonico"=> '022'
        ]
        , [
            "id"=> "20150326115500551439e48fa09", "nombre"=> "Santa Elena", "codtelefonico"=> '042'
        ]
        , [
            "id"=> "20150326115500551439e48fb5f", "nombre"=> "Santo Domingo de los Tsáchilas", "codtelefonico"=> '022'
        ]
        , [
            "id"=> "20150326115500551439e48fcc6", "nombre"=> "Sucumbíos", "codtelefonico"=> '062'
        ]
        , [
            "id"=> "20150326115500551439e48fe2f", "nombre"=> "Tungurahua", "codtelefonico"=> '032'
        ]
        , [
            "id"=> "20150326115500551439e48ff9d", "nombre"=> "Zamora Chinchipe", "codtelefonico"=> '072'
        ]
        );

foreach ($array as $key => $provincia) {
		$tabla=new Provincias();
    	$tabla->id=$funciones->generarId();
    	$tabla->nombre=$provincia['nombre'];
    	$tabla->codtelefonico=$provincia['codtelefonico']."";
    	$resultado=$tabla->save();
}
    	if ($resultado) {
    		return response()->json(["respuesta"=>true],200);
    	}
    }

    public function get_provincias(Request $request){
        $tabla=new Provincias();
        $resultado=$tabla->orderBy('nombre','ASC')->get();

       return response()->json(["provincias"=>$resultado],200);
    }
}
