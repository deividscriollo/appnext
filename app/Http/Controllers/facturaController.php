<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\facturas;
use Auth;
use App\PasswrdsP;
use App\PasswrdsE;
use App\libs\Funciones;
use App\libs\Funciones_fac;
use App\FacturasRechazadas;
use Zipper;
// use Codedge\Fpdf\Fpdf\FPDF;
use File;

class facturaController extends Controller
{

public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }
    
    public function add_fac_bdd(Request $request){
        $Funciones_fac=new Funciones_fac();

             $user = JWTAuth::parseToken()->authenticate();
             $pos=stripos($user->email, '@');
             $documento=substr($user->email,0,$pos);
             $tamaño=strlen($documento);
    
switch ($tamaño) {

    // case 10:
    //     // $auth = Auth::guard('web');
    //     $tabla =    new PasswrdsP();
    //     break;
    
    case 13:
        // $auth = Auth::guard('usersE');
        $tabla =    new PasswrdsE();
        $datos= $tabla->select('pass_email')->where('email','=',$user->email)->get();
        break;
}

        $resultado=$Funciones_fac->leer($user->email,$datos[0]['pass_email'],$user->id_user);
        if ($resultado==null) {
         return response()->json(["respuesta"=>true],200);
        }else{
            return response()->json([$request->input('token'),"resultados"=>$resultado],500);
        }
        // print_r($resultado);
        // print_r($user->email);
    }

public function get_facturas(Request $request){
        $Funciones_fac=new Funciones_fac();

             $user = JWTAuth::parseToken()->authenticate();
             $pos=stripos($user->email, '@');
             $documento=substr($user->email,0,$pos);
             $tamaño=strlen($documento);
             $facturas= new Facturas();
             // echo $tamaño;
    
            switch ($tamaño) {
                case 13:
                    // $auth = Auth::guard('usersE');
                    $tabla =    new PasswrdsE();
                    $datos= $tabla->select('id_user')->where('email','=',$user->email)->get();
                    break;
            }

        $resultado=$facturas->select('*')->where('id_empresa','=',$datos[0]['id_user'])->get();
        $i=0;
        foreach ($resultado as $key => $value) {
            switch ($resultado[$i]['tipo_doc']) {
                case '01':
                   $resultado[$i]['tipo_doc']="Factura";
                    break;
                case '02':
                    $resultado[$i]['tipo_doc']="Nota de Venta";
                    break;
                case '03':
                    $resultado[$i]['tipo_doc']="Liquidación de Compra de Bienes o Prestación de Servicios";
                    break;
                     case '04':
                    $resultado[$i]['tipo_doc']="Nota de Crédito";
                    break;
                     case '05':
                    $resultado[$i]['tipo_doc']="Nota de Débito";
                    break;
                     case '06':
                    $resultado[$i]['tipo_doc']="Guía de remisión";
                    break;
                     case '07':
                    $resultado[$i]['tipo_doc']="Comprobante de Retención";
                    break;
                     case '08':
                    $resultado[$i]['tipo_doc']="Entradas a Espectáculos Públicos";
                    break;
            }
            $i++;
        }

            return response()->json(array("misfacturas"=>$resultado),200);
    }

        public function get_facturas_rechazadas(Request $request){
             $user = JWTAuth::parseToken()->authenticate();
             $facturas= new FacturasRechazadas();
            $resultado=$facturas->select('*')->where('id_empresa','=',$user['id_user'])->get();

     return response()->json(array("misfacturas_rechazadas"=>$resultado),200);
    }



public function upload_xmlfile(Request $request){
        $Funciones_fac=new Funciones_fac();
        $respuesta=$Funciones_fac->verificar_autorizacion($request->input('clave'));
        $autorizaciones=$respuesta['respuesta']['autorizaciones'];
        $user = JWTAuth::parseToken()->authenticate();
        if (count($autorizaciones)!='respuesta') {
                 $mensajes=$respuesta['respuesta']['autorizaciones']['autorizacion']['mensajes'];
                         if(count($mensajes)=='respuesta'){
            $comprobante=$respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante'];
            $resultado=$Funciones_fac->save_xml_file($comprobante,$user['email'],"999.xml",$request->input('tipo'));
            }
            // else{
            // $resultado=$Funciones_fac->save_fac_rechazada("",$user['email'],$request->input('clave'),"Documento Vacio");
            //     }
                         }else{
                            $resultado=array('valid' => 'false', 'error' => '4', 'methods' => 'registro-no-existente-sri');
                         }
// echo $request->input('tipo');
            return response()->json($resultado);
    }


public function gen_zip($iduser,$idfac)
        {
            // -------------------------------------------GENERAR PDF ---------------------------------------------------
            $xml = public_path().'/facturas/'.$iduser.'/'.$idfac.".xml";
            $xml = file_get_contents($xml);
            // echo $xml;
            $Funciones_fac=new Funciones_fac();
            $Funciones_fac->gen_pdf($xml,$iduser,$idfac);
            // ----------------------------------------- GENERAR ZIP ----------------------------------------------------
            $xml = glob(public_path().'/facturas/'.$iduser.'/'.$idfac.".xml");
            $pdf = glob(public_path().'/facturas/'.$iduser.'/'.$idfac.".pdf");
            $zip=Zipper::make(public_path().'/facturas/'.$iduser.'/'.$idfac.".zip")->add($xml);
            $resul_zip=$zip->add($pdf);
            if ($resul_zip) {
               return true;
            }
            // File::delete(public_path().'/facturas/'.$iduser.'/'.$idfac.".pdf");
            // File::delete(public_path().'/facturas/'.$user['id_user'].'/'.$idfac.".zip");
        }


public function Download_fac(Request $request)
        {
            $user = JWTAuth::parseToken()->authenticate();
            $headers = array(
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="fac.zip'
                    );
             return response()->download(public_path().'/facturas/'.$user['id_user'].'/'.$request->input('id').".zip",$request->input('id').".zip",$headers);
        }

        public function gen_download_link(Request $request)
        {
             $user = JWTAuth::parseToken()->authenticate();
             $this->gen_zip($user['id_user'],$request->input('id'));

             return response()->json(["link"=>"http://192.168.100.16/appnext/public/Downloadfac?id=".$request->input('id')."&token=".$request->input('token').""]);
             
        }

}
