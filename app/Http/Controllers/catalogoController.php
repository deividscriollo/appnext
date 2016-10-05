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
use App\CatalogoPortadas;
use App\CatalogoContraportadas;
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
        $this->portadas=new CatalogoPortadas;
        $this->contraportadas=new CatalogoContraportadas;
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
        $this->pathimgCatalogo  = config('global.pathimgCatalogo');
        $this->pathimgCatalogoProductos  = config('global.pathimgCatalogoProductos');
        $this->pathimgCatalogoPortadas  = config('global.pathimgCatalogoPortadas');
        $this->pathimgCatalogoContraportadas  = config('global.pathimgCatalogoContraportadas');
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
		    if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo)) {
		       $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo;
		        File::makeDirectory($path);    
		    }
		    if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoProductos)) {
		        $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoProductos;
		        File::makeDirectory($path); 
		    }
		    $id_img=$this->funciones->generarID();
            $this->img_productos->id=$id_img;
            $this->img_productos->img="storage/app/".$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoProductos.$id_img.'.jpg';
            $this->img_productos->id_producto=$id_producto;
            $this->img_productos->estado=1;
            $saveimgprod=$this->img_productos->save();

            if ($saveprod&$saveimgprod) {
               return response()->json(["respuesta"=>true],200);
            }
    }

public function add_portada(Request $request){

            $id_portada=$this->funciones->generarID();
            $this->portadas->id=$id_portada;
            $this->portadas->descripcion=$request->input('descripcion');
            $this->portadas->img="storage/app/".$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoPortadas.$id_portada.'.jpg';
            $this->portadas->id_sucursal=$this->id_sucursal;
            $this->portadas->estado=1;
            $save=$this->portadas->save();
            //------------------------- guardar img -------
            if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo)) {
               $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo;
                File::makeDirectory($path);    
            }
            if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoPortadas)) {
                $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoPortadas;
                File::makeDirectory($path); 
            }
            if ($save) {
               return response()->json(["respuesta"=>true],200);
            }
    }

    public function add_contraportada(Request $request){

            $id_portada=$this->funciones->generarID();
            $this->contraportadas->id=$id_portada;
            $this->contraportadas->descripcion=$request->input('descripcion');
            $this->contraportadas->img="storage/app/".$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoContraportadas.$id_portada.'.jpg';
            $this->contraportadas->id_sucursal=$this->id_sucursal;
            $this->contraportadas->estado=1;
            $save=$this->contraportadas->save();
            //------------------------- guardar img -------
            if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo)) {
               $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo;
                File::makeDirectory($path);    
            }
            if (!is_dir($this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoContraportadas)) {
                $path = $this->pathLocal.$this->user['id_user'].$this->pathimgCatalogo.$this->pathimgCatalogoContraportadas;
                File::makeDirectory($path); 
            }
            if ($save) {
               return response()->json(["respuesta"=>true],200);
            }
    }


}
