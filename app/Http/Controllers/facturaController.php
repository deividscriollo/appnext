<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
//---------------------------------------------- Modelos ---------------------
use App\PasswrdsP;
use App\PasswrdsE;
use App\libs\Funciones_fac;
use App\FacturasRechazadas;
use App\Sucursales;
use App\Proveedores;
use App\facturas;
//--------------------------------------------- Extras -----------------------
use GuzzleHttp\Client;
use Zipper;
use File;
//---------------------------------------------- Paginador---------------------
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
//---------------------------------------------- Funciones -------------------
use App\libs\Funciones;

class facturaController extends Controller
{

public function __construct(Request $request)
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        //-------------------------------------- Funciones ------------------
        $this->funciones = new Funciones();
        //------------------------------------ Modelos ----------------------
        $this->sucursal=new Sucursales();
        $this->proveedor=new Proveedores();
        $this->facturas= new Facturas();
        $this->facturas_rechazadas= new FacturasRechazadas();
        //----------------------------------- Funciones -------------------------------
        $this->Funciones_fac=new Funciones_fac();
        $this->funciones = new Funciones();
        //--------------------------------------- Autentiocacion --------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Id Sucursal ---------------------
        if ($request->input('sucursal')!=null) {
        $datos=$this->sucursal->select('id_sucursal')->where('codigo','=',$request->input('sucursal'))->where('id_empresa','=',$this->user['id_user'])->get();
        $this->id_sucursal=$datos[0]['id_sucursal'];
        }else return response()->json(["respuesta"=>false,"mensaje"=>"sin-id-sucursal"]);
    }
    
    public function add_fac_bdd(Request $request){
    
        $tabla =    new PasswrdsE();
        $datos= $tabla->select('pass_email')->where('email','=',$this->user->email)->get();

        $resultado=$this->Funciones_fac->leer($this->user->email,$datos[0]['pass_email'],$this->user->id_user,$this->id_sucursal);
        if ($resultado==null) {
         return response()->json(["respuesta"=>true],200);
        }else{
            return response()->json([$request->input('token'),"resultados"=>$resultado],500);
        }
        // print_r($resultado);
        // print_r($this->user->email);
    }

public function get_facturas(Request $request){
 // return response()->json(['respuesta' => $request->all()], 200);
    $currentPage = $request->input('pagina_actual');
    $limit = $request->input('limit');

    $tipo="";
    if (($request->exists('fecha_inicio')&&$request->exists('fecha_fin'))&&$request->input('fecha_inicio')!=null&&$request->input('fecha_fin')!=null) {
      //------------------------------ fecha inicio -----------------
      $fecha_inicio=explode('T', $request->input('fecha_inicio'));
      $fecha_inicio=(string)$fecha_inicio[0];
      //-------------------------------fecha fin ------------------ 
      $fecha_fin=explode('T', $request->input('fecha_fin'));
      $fecha_fin=$fecha_fin[0];
      $tipo="por_fecha";
    };
    if (($request->exists('fecha_inicio')&&$request->input('fecha_inicio')!=null)&&$request->exists('ordenarPor')&&$request->input('ordenarPor')!=null) {
       //------------------------------ fecha inicio -----------------
      $fecha_inicio=explode('T', $request->input('fecha_inicio'));
      $fecha_inicio=(string)$fecha_inicio[0];
      //-------------------------------fecha fin ------------------ 
      $fecha_fin=explode('T', $request->input('fecha_fin'));
      $fecha_fin=$fecha_fin[0];
      $tipo="por_fecha_ordenar";
    };
    if ($request->exists('filter')&&$request->input('filter')!=null) {
         $tipo="por_palabra"; 
    };
    if ((!$request->exists('fecha_inicio')&&$request->exists('ordenarPor'))&&$request->input('ordenarPor')!=null) {
         $tipo="por_filtro"; 
    };
    switch ($tipo) {
        case 'por_fecha':
        $resultado=$this->facturas->where('id_sucursal',$this->id_sucursal)
                            ->where('fecha_emision','>=',$fecha_inicio)
                            ->where('fecha_emision','<=',$fecha_fin)->get();
        break;
        case 'por_fecha_ordenar':
        $resultado=$this->facturas->where('id_sucursal',$this->id_sucursal)
                            ->where('fecha_emision','>=',$fecha_inicio)
                            ->where('fecha_emision','<=',$fecha_fin)
                            ->orderBy($request->input('ordenarPor'),'DESC')->get();
        break;
        case 'por_palabra':
        $resultado = $this->facturas->search($request->input('filter'))->get();
        break;
        case 'por_filtro':
        $resultado=$this->facturas->where('id_sucursal',$this->id_sucursal)
                            ->orderBy('fecha_emision','DESC')->get();
        break;
        default:
        $resultado=$this->facturas->where('id_sucursal',$this->id_sucursal)->orderBy('fecha_emision','DESC')->get();
        break;
    }

    foreach ($resultado as $key => $value) {
       $resultado[$key]['url']='http://192.168.0.101/appnext/public/xml?iddocumento='.$resultado[$key]['id_factura'].'&token='.$request->input('token');
      // $resultado[$key]['url']='http://servicios.nextbook.ec/public/xml?iddocumento='.$resultado[$key]['id_factura'].'&token='.$request->input('token');
    }

    $resultado=$this->cambiar_tipo_documento($resultado);
    $resultado=$this->funciones->paginarDatos($resultado,$currentPage,$limit);

    return response()->json(['respuesta' => $resultado], 200);
    }

    public function get_facturas_rechazadas(Request $request){
             
            $resultado=$this->facturas_rechazadas->select('*')->where('id_sucursal',$this->id_sucursal)->get();

     return response()->json(array("misfacturas_rechazadas"=>$resultado),200);
    }

public function cambiar_tipo_documento($array){
  $resultado=$array;
    foreach ($resultado as $key => $value) {
          $proveedor=$this->proveedor->where('ruc',$resultado[$key]['Ruc_prov'])->first();
          $resultado[$key]['razon_social']=$proveedor->razon_social;
            switch ($resultado[$key]['tipo_doc']) {
                case '01':
                   $resultado[$key]['tipo_doc']="Factura";
                    break;
                     case '04':
                    $resultado[$key]['tipo_doc']="Nota de Crédito";
                    break;
                     case '05':
                    $resultado[$key]['tipo_doc']="Nota de Débito";
                    break;
                     case '06':
                    $resultado[$key]['tipo_doc']="Guía de remisión";
                    break;
                     case '07':
                    $resultado[$key]['tipo_doc']="Comprobante de Retención";
                    break;
            }
        } 
        return $resultado;
}

public function upload_xmlfile(Request $request){
  
        $respuesta=$this->Funciones_fac->verificar_autorizacion($request->input('clave'));
        $autorizaciones=$respuesta['respuesta']['autorizaciones'];
        
        if (count($autorizaciones)!='respuesta') {
            $mensajes=$respuesta['respuesta']['autorizaciones']['autorizacion']['mensajes'];
            if(count($mensajes)=='respuesta'){
            $comprobante=$respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante'];
            $resultado=$this->Funciones_fac->save_xml_file($comprobante,$this->user['email'],"999.xml",$request->input('descripcion'),$this->id_sucursal);
            }
                         }else{
                            $resultado=array('respuesta' => false, 'error' => '4', 'methods' => 'registro-no-existente-sri');
                         }
                         if (count($resultado)==0) {
                             return response()->json(["respuesta"=>true]);
                         }else  return response()->json(["respuesta"=>$resultado]);
    }

public function gen_zip($iduser,$idfac)
        {
            // -------------------------------------------GENERAR PDF ---------------------------------------------------
            $xml = public_path().'/facturas/'.$iduser.'/'.$idfac.".xml";
            $xml = file_get_contents($xml);
            // echo $xml;
            $this->Funciones_fac->gen_pdf($xml,$iduser,$idfac);
            // ----------------------------------------- GENERAR ZIP ----------------------------------------------------
            $xml = glob(public_path().'/facturas/'.$iduser.'/'.$idfac.".xml");
            $pdf = glob(public_path().'/facturas/'.$iduser.'/'.$idfac.".pdf");
            $zip=Zipper::make(public_path().'/facturas/'.$iduser.'/'.$idfac.".zip")->add($xml);
            $resul_zip=$zip->add($pdf);
            if ($resul_zip) {
               return true;
            }
        }
public function update_tipo_consumo(Request $request){
  $update=$this->facturas->where('id_factura',$request->input('id_factura'))->update(['descripcion'=>$request->input('descripcion')]);
  if ($update) {
    return response()->json(['respuesta'=>$request->all()],200);
  }
}




}
