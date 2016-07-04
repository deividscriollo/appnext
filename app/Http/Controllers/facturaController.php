<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Facturas;
use Auth;
use App\PasswrdsP;
use App\PasswrdsE;
use App\libs\Funciones;
use App\libs\Funciones_fac;

class facturaController extends Controller
{
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
         return response()->json(true,200);
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

            return response()->json(array("misfacturas"=>$resultado),200);
    }


public function upload_xmlfile(Request $request){
        $Funciones_fac=new Funciones_fac();
        $respuesta=$Funciones_fac->verificar_autorizacion($request->input('clave'));
        $autorizaciones=$respuesta[0]['autorizaciones'];
                     $user = JWTAuth::parseToken()->authenticate();
        if (count($autorizaciones)!=0) {
                 $mensajes=$respuesta[0]['autorizaciones']['autorizacion']['mensajes'];
                         if(count($mensajes)==0){
            $comprobante=$respuesta[0]['autorizaciones']['autorizacion']['comprobante'];
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

public function Download_fac(Request $request)
        {
            $user = JWTAuth::parseToken()->authenticate();
            // echo $user;
    //PDF file is stored under project/public/download/info.pdf
    // $file= public_path().'/facturas/'.$user['id_user'].'/'.$request->input('id').".xml";
    $Factura=new Facturas();
    $resultado=$Factura->select('contenido_fac')->where('id_factura','=',$request->input('id'))->get();


    // $headers = array(
    //           'Content-Type: application/xml',
    //         );

    // return response()->download($file, $request->input('id').'xml', $headers);
    return response()->json(["fac"=>$resultado]);
        }

}
