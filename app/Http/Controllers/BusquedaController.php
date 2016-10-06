<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------------- Modelos ---------------
use App\Empresas;
use App\img_perfiles;
use App\PasswrdsE;
//----------------------- paginador --------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
//----------------------- Funciones ------------
use App\libs\Funciones;
// ----------- Extras
use Storage;
use File;

class BusquedaController extends Controller
{
    function __construct(){
        // modelos------
        $this->funciones=new Funciones();
        // ------- paths --------------------
        $this->pathImg  = config('global.pathimgPerfiles');
        $this->pathLocal  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    }
    public function get_empresas(Request $request)
    {
    $empresas = new Empresas();
    $img_perfil = new img_perfiles();

    if ($request->input('filter')!=null) {
         $datos = $empresas->search($request->input('filter'))->get();
    }else{
    	$datos = $empresas->where('estado','=',1)->orderBy('created_at','ASC')->paginate(10)->items();
    }
	foreach ($datos as $key => $value) {
		if ($datos[$key]['nombre_comercial']=='no disponible') {
			$datos[$key]['nombre_comercial']=$datos[$key]['razon_social'];
		}
	}

	foreach ($datos as $key => $value) {
		$resultado=$img_perfil->where('id_empresa',$datos[$key]['id_empresa'])->where('estado',1)->first();

    if (File::exists($this->pathLocal.$datos[$key]['id_empresa'].$this->pathImg.$resultado['id_img_perfil'].'.png')) {
        $img=config('global.appnext').'/'.$resultado['img'];
    }else{
        $img=config('global.pathAvartarDefault');
    }
		$datos[$key]['img']=$img;
	}

    return response()->json(['respuesta' => $datos], 200);
    }

    public function add_empresas(Request $request)
    {

        $json = file_get_contents($this->pathLocal."IMBABURA.json");
        $json = json_decode($json);

    foreach ($json as $item) {
        if ($item->ESTADO_CONTRIBUYENTE=="ACTIVO") {
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
        }
    }

    return response()->json(['respuesta' => true], 200);
    }


}
