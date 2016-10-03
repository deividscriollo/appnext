<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\img_perfiles;
//-------------------------  autenticacion -------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------- Modelos ---------------
use App\Productos;
use App\img_productos;
use App\Sucursales;
//----------------------- Funciones ------------
use App\libs\Funciones;
// extras
use File;
use Storage;

class catalogoController extends Controller
{
    public function __construct(Request $request){
    	//----------------------------------- Modelos -----------
        $this->productos  = new Productos();
        $this->img_productos = new img_productos();
        $this->sucursal=new Sucursales;
        //-------------------------- Autenticacion-------
        $this->user = JWTAuth::parseToken()->authenticate();
        //--------------  Funciones ------
        $this->funciones=new Funciones();
        //----------------------------------- Id Sucursal ---------------------
        if ($request->input('sucursal')!=null) {
        $datos=$this->sucursal->select('id_sucursal')->where('codigo','=',$request->input('sucursal'))->where('id_empresa','=',$this->user['id_user'])->get();
        $this->id_sucursal=$datos[0]['id_sucursal'];
        }else return response()->json(["respuesta"=>false,"mensaje"=>"sin-id-sucursal"]);
            //------------------------------------ Paths -------------------------------
        $this->pathImg  = config('global.pathimgProductos');
        $this->pathLocal  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    }

    public function add_producto(Request $request){
    	$resultado=$this->productos->select('codigo')->orderBy('created_at','DESC')->first();
    	$codigo=$resultado['codigo']+1;
    	// echo($codigo);
    	$codigo=str_pad($codigo, 4, '0', STR_PAD_LEFT);
            $id_producto=$this->funciones->generarID();
            $this->productos->id=$id_producto;
            $this->productos->codigo=$codigo;
            $this->productos->descripcion=$request->input('descripcion');
            $this->productos->nombre=$request->input('nombre');
            $this->productos->stock=$request->input('stock');;
            $this->productos->precio_unitario=$request->input('precio_unitario');;
            $this->productos->precio_oferta=$request->input('precio_oferta');;
            $this->productos->id_sucursal=$this->id_sucursal;
            $this->productos->estado=1;
            $saveprod=$this->productos->save();
            //------------------------- guardar img -------
		    if (!is_dir($this->pathLocal.$this->user['id_user'])) {
		       $path = $this->pathLocal.$this->user['id_user'];
		        File::makeDirectory($path);    
		    }
		    if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathImg)) {
		        $path = $this->pathLocal.$this->user['id_user'].$this->pathImg;
		        File::makeDirectory($path); 
		    }
		    $id_img=$this->funciones->generarID();
            $this->img_productos->id=$id_img;
            $this->img_productos->img="storage/app/".$this->user['id_user'].$this->pathImg.$id_img.'.jpg';
            $this->img_productos->id_producto=$id_producto;
            $this->img_productos->estado=1;
            $saveimgprod=$this->img_productos->save();

            if ($saveprod&$saveimgprod) {
               return response()->json(["respuesta"=>true],200);
            }
    }
}
