<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------------- Modelos ---------------
use App\Empresas;
use App\img_perfiles;
use App\PasswrdsE;
use App\Portadas;
//-------------------------  autenticacion -------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//----------------------- paginador --------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
//----------------------- Funciones ------------
use App\libs\Funciones;
// ----------- Extras
use Storage;
use File;
use DB;

class BusquedaController extends Controller
{
    function __construct(){
        // modelos------
        $this->empresas = new Empresas();
        $this->img_perfil = new img_perfiles();
        $this->img_portada = new Portadas();
        // /-funciones
        $this->funciones=new Funciones();
        // ------- paths --------------------
        $this->pathImg  = config('global.pathimgPerfiles');
        $this->pathImgPortada  = config('global.pathimgPortadas');
        $this->pathLocal  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //-------------------------- Autenticacion-------
        // $this->user = JWTAuth::parseToken()->authenticate();

    }
    public function get_empresas(Request $request)
    {

    if ($request->input('filter')!=null) {
         $datos = DB::select("SELECT id_empresa, ruc, razon_social, nombre_comercial, estado_contribuyente, 
       tipo_contribuyente, actividad_economica FROM empresas WHERE (ruc||nombre_comercial) like '%".strtoupper ($request->input('filter'))."%' LIMIT 5");
    }else{
    	$datos = $this->empresas->where('estado','=',1)->orderBy('created_at','ASC')->paginate(10)->items();
    }
	foreach ($datos as $key => $value) {
		if ($value->nombre_comercial=='no disponible') {
			$value->nombre_comercial=$value->razon_social;
		}
    }

	foreach ($datos as $key => $value) {
		$resultado=$this->img_perfil->where('id_empresa',$value->id_empresa)->where('estado',1)->first();
        $resultado_portada=$this->img_portada->where('id_empresa',$value->id_empresa)->where('estado',1)->first();

    if (File::exists($this->pathLocal.$value->id_empresa.$this->pathImg.$resultado['id_img_perfil'].'.png')) {
        $img['perfil']=config('global.appnext').'/'.$resultado['img'];
    }else{
        $img['perfil']=config('global.appnext').config('global.pathAvartarDefault');
    }

     if (File::exists($this->pathLocal.$value->id_empresa.$this->pathImgPortada.$resultado_portada['id_img_portada'].'.png')) {
        $img['portada']=config('global.appnext').'/'.$resultado_portada['img'];
    }else{
        $img['portada']=config('global.appnext').config('global.pathPortadaDefault');
    }

		$value->img=$img;
	}

    return response()->json(['respuesta' => $datos], 200);
    }


    public function get_perfil_empresas(Request $request)
    {

        $datos = $this->empresas->where('estado','=',1)->where('ruc',$request->input('ruc'))->get();

    foreach ($datos as $key => $value) {
        if ($value->nombre_comercial=='no disponible') {
            $value->nombre_comercial=$value->razon_social;
        }
    }

    foreach ($datos as $key => $value) {
        $resultado=$this->img_perfil->where('id_empresa',$value->id_empresa)->where('estado',1)->first();
        $resultado_portada=$this->img_portada->where('id_empresa',$value->id_empresa)->where('estado',1)->first();

    if (File::exists($this->pathLocal.$value->id_empresa.$this->pathImg.$resultado['id_img_perfil'].'.png')) {
        $img['perfil']=config('global.appnext').'/'.$resultado['img'];
    }else{
        $img['perfil']=config('global.appnext').config('global.pathAvartarDefault');
    }

     if (File::exists($this->pathLocal.$value->id_empresa.$this->pathImgPortada.$resultado_portada['id_img_portada'].'.png')) {
        $img['portada']=config('global.appnext').'/'.$resultado_portada['img'];
    }else{
        $img['portada']=config('global.appnext').config('global.pathPortadaDefault');
    }
        $value->img=$img;
    }

    return response()->json(['respuesta' => $datos[0]], 200);
    }

    public function add_empresas(Request $request)
    {

        $json = file_get_contents($this->pathLocal."IMBABURA.json");
        $json = json_decode($json);

    foreach ($json as $item) {
        // if ($item->ESTADO_CONTRIBUYENTE=="ACTIVO") {
            if ($item->NOMBRE_FANTASIA_COMERCIAL!='') {
                $nombre_comercial=$item->NOMBRE_FANTASIA_COMERCIAL;
            }else{
                $nombre_comercial=$item->RAZON_SOCIAL;
            }
        $id                           = $this->funciones->generarID();
        $tablaE                        = new Empresas();
        $tablaE->id_empresa            = $id;
        $tablaE->ruc                   = $item->NUMERO_RUC;
        $tablaE->razon_social          = $item->RAZON_SOCIAL;
        $tablaE->nombre_comercial      = $nombre_comercial;
        $tablaE->estado_contribuyente  = $item->ESTADO_CONTRIBUYENTE;
        $tablaE->tipo_contribuyente    = $item->TIPO_CONTRIBUYENTE;
        $tablaE->obligado_contabilidad = $item->OBLIGADO;
        $tablaE->actividad_economica   = $item->ACTIVIDAD_ECONOMICA;
        $tablaE->codigo_activacion     = 'Pruebas';
        $tablaE->estado                = 1;
        $tablaE->id_provincia          = '----';
        $saveE=$tablaE->save();
        $id_empresa=$tablaE->id_empresa;
         //***************************************** Registro Clave  *****************************************
        $tabla_pass=new PasswrdsE();
        $tabla_pass->email             = $item->NUMERO_RUC.'@'.'facturanext.com';
        $tabla_pass->pass_email       = '123456';
        $tabla_pass->password     = bcrypt('123456');
        $tabla_pass->remember_token ='';
        $tabla_pass->pass_estado      = 1;
        $tabla_pass->id_user          = $id_empresa;
        $tabla_pass->save();
        // }
    }

    return response()->json(['respuesta' => true], 200);
    }


}
