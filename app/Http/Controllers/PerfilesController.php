<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\libs\Funciones;
use App\img_perfiles;

class PerfilesController extends Controller
{

public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function add_img_perfil(Request $request){
    
	$user = JWTAuth::parseToken()->authenticate();
	$funciones= new Funciones();
	$id_img=$funciones->generarID();

	if (!is_dir("perfiles/".$user['id_user'])) {
    mkdir("perfiles/".$user['id_user']);      
    }
	$base64_string = base64_decode($request->input('img'));
	$image_name= $id_img.'.png';
	$path = public_path() . "/perfiles/".$user['id_user']."/".$image_name;
 	$ifp = fopen($path, "wb"); 
    $data = explode(',', $base64_string);
    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp);
    $tabla_img=new img_perfiles();
    $tabla_img->id_img_perfil=$id_img;
    $tabla_img->img="http://192.168.100.20/appnext/public/perfiles/".$user['id_user']."/".$image_name;
    $tabla_img->estado='1';
    $tabla_img->id_empresa=$user['id_user'];
    $save=$tabla_img->save();

    if ($save) {
    	return response()->json(["img"=>$image_name]);
    }
    
    }

    public function set_img_perfil(Request $request){
    
    $img=explode('/',$request->input('img'));
    $img=explode('.', $img[count($img)-1]);
    $idimg=$img[0];

	$user = JWTAuth::parseToken()->authenticate();
	$funciones= new Funciones();
	$id_img=$funciones->generarID();

    $tabla_img=new img_perfiles();
    $tabla_img->where('id_empresa','=',$user['id_user'])->update(['estado'=>0]);
    $resultado=$tabla_img->where('id_img_perfil','=',$idimg)->update(['estado'=>1]);
    if ($resultado) {
        $resultado=$tabla_img->select('img')->where('estado','=',1)->first();
        return response()->json(["img"=>$resultado['img']]);
    }
    
    }

    public function load_imgs_perfil(Request $request){
    
	$user = JWTAuth::parseToken()->authenticate();

    $tabla_img=new img_perfiles();
    $resultado=$tabla_img->select('img')->where('id_empresa','=',$user['id_user'])->get();
    // print_r($resultado);

    	return response()->json(["imgs"=>$resultado]);
    }

    public function get_img_perfil(Request $request){
    
    $user = JWTAuth::parseToken()->authenticate();

    $tabla_img=new img_perfiles();
    $resultado=$tabla_img->select('img')->where('estado','=',1)->first();
    // print_r($resultado);

        return response()->json($resultado);
    
    }

}
