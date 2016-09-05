<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------------------------ Autenticacion --------------------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------------ Funciones --------------------
use App\libs\Funciones;
//------------------------------------ Modelos --------------------
use App\Portadas;
//---------------------------- Extras 
use Storage;
class PortadasController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        //----------------------------------------------- Modelos --------------
        $this->tabla_img=new Portadas();
        // --------------------------------------- Autenticacion --------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Funciones -------------------------------
        $this->funciones=new Funciones();
        //------------------------------------ Paths -------------------------------
        $this->imgsPortada  = Storage::disk('imgsPortada')->getDriver()->getAdapter()->getPathPrefix();
    }

    public function add_img_portada(Request $request){
    
	$id_img=$this->funciones->generarID();

	if (!is_dir($this->imgsPortada.$this->user['id_user'])) {
    mkdir($this->imgsPortada.$this->user['id_user']);      
    } 
	$base64_string = base64_decode($request->input('img'));
	$image_name= $id_img.'.png';
	$path = $this->imgsPortada.$this->user['id_user']."/".$image_name;
 	$ifp = fopen($path, "wb"); 
    $data = explode(',', $base64_string);
    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp);
    $this->tabla_img->where('id_empresa','=',$this->user['id_user'])->update(['estado'=>0]);

    $this->tabla_img->id_img_portada=$id_img;
    $this->tabla_img->img="http://192.168.111.35/appnext/storage/app/portadas/".$this->user['id_user']."/".$image_name;
    $this->tabla_img->estado='1';
    $this->tabla_img->id_empresa=$this->user['id_user'];
    $save=$this->tabla_img->save();

    if ($save) {
    return response()->json(["img"=>$image_name]);
    }
    
    }

    public function set_img_portada(Request $request){
    
    $img=explode('/',$request->input('img'));
    $img=explode('.', $img[count($img)-1]);
    $idimg=$img[0];

	$id_img=$this->funciones->generarID();

    $this->tabla_img->where('id_empresa','=',$this->user['id_user'])->update(['estado'=>0]);
    $resultado=$this->tabla_img->where('id_img_portada','=',$idimg)->update(['estado'=>1]);
    if ($resultado) {
        $resultado=$this->tabla_img->select('img')->where('estado','=',1)->where('id_empresa','=',$this->user['id_user'])->first();
        return response()->json(["img"=>$resultado['img']]);
    }
    
    }

    public function load_imgs_portada(Request $request){

    $resultado=$this->tabla_img->select('img')->where('id_empresa','=',$this->user['id_user'])->where('estado','=',0)->get();
    	return response()->json(["imgs"=>$resultado]);
    }

    public function get_img_portada(Request $request){
    $resultado=$this->tabla_img->select('img')->where('id_empresa','=',$this->user['id_user'])->where('estado','=',1)->first();
        return response()->json($resultado);
    
    }
}
