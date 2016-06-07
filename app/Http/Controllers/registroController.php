<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Empresas;
use App\Personas;
use App\Colaboradores;
use App\Passwrds;
use App\libs\Funciones;
use App\libs\xmlapi;
use Mail;
use DB;

class registroController extends Controller
{
   
    public function registrarEmpresa(Request $request)
    {
        date_default_timezone_set('America/Guayaquil'); 
        setlocale(LC_TIME, 'spanish');
        $funciones                    = new Funciones();
        $activation_code              = $funciones->generarActivacion(md5($request->input('cedula')));
        $id                           = $funciones->generarID();
        $tabla                        = new Empresas();
        $tabla->id_empresa            = $id;
        $tabla->Ruc                   = $request->input('cedula');
        $tabla->user_nextbook         = $request->input('cedula').'@'.'facturanext.com';
        $tabla->razon_social          = $request->input('razon_social');
        $tabla->nombre_comercial      = $request->input('nombre_comercial');
        $tabla->estado_contribuyente  = $request->input('estado_contribuyente');
        $tabla->tipo_contribuyente    = $request->input('tipo_contribuyente');
        $tabla->obligado_contabilidad = $request->input('obligado_llevar_contabilidad');
        $tabla->actividad_economica   = $request->input('actividad_principal');
        $tabla->nombres_apellidos     = $request->input('nombres_apellidos');
        $tabla->fecha_nacimiento      = utf8_encode(strftime(strftime("%A, %d de %B de %Y", strtotime($request->input('fecha_nacimiento')))));
        $tabla->correo                = $request->input('correo');
        $tabla->telefono              = $request->input('telefono');
        $tabla->celular               = $request->input('celular');
        $tabla->codigo_activacion     = $activation_code;
        $tabla->estado                = '0';
        $tabla->save();

        //******************************************* Enivar email de verificacion**************************
        $data = ["codigo"=>$activation_code,"cuenta"=>"EEE"];
        $correo_enviar=$request->input('correo');
        $nombre_comercial=$request->input('nombre_comercial');
        
        Mail::send('email_registro', $data, function($message)use ($correo_enviar,$nombre_comercial)
            {
                $message->from("1003498142001@facturanext.ec",'no-reply@facturanext.com');
                $message->to($correo_enviar,$nombre_comercial)->subject('Verificación de Email');
            });

        //****************************************************** Registrar sucursales***********************

         // $establecimientos=$getsri->establecimientoSRI($request->input('nrodocumento'));
            // foreach ($establecimientos['adicional'] as $representante) {
            //     print_r($representante);
            // }
        
        return response()->json(array(
            "mensaje" => "Empresa creada correctamente"
        ));
        
    }
    public function registrarPersona(Request $request)
    {
        $funciones                = new Funciones();
        $tabla                    = new Personas();
        $tabla->id                = $funciones->generarID();
        $tabla->Nombres_apellidos = $request->input('nombres_apellidos');
        $tabla->cedula            = $request->input('cedula');
        $tabla->provincia         = $request->input('provincia');
        $tabla->canton            = $request->input('canton');
        $tabla->parroquia         = $request->input('parroquia');
        $tabla->zona              = $request->input('zona');
        $tabla->correo            = $request->input('correo');
        $tabla->telefono          = $request->input('telefono');
        $tabla->celular           = $request->input('celular');
        $tabla->save();
        // return response()->json(array_map('utf8_encode', $datospersona));
        return response()->json(array(
            "mensaje" => "Persona creada Correctamente"
        ));
    }
    
    public function registroColaborador(Request $request)
    {
        $funciones         = new Funciones();
        $tabla             = new Colaboradores();
        $tabla->id         = $funciones->generarID();
        $tabla->correo     = $request->input('correo');
        $tabla->pass       = md5($request->input('pass'));
        $tabla->estado     = '0';
        $tabla->id_empresa = $request->input('id_empresa');
        // $tabla->save();
        
        // $a = $this->crear_email('1003498142', 'root@123');
        // $data = ["nombre"=>"Alex"]; // Empty array
        
        // Mail::send('email_registro', $data, function($message)
        // {
        //     $message->from("1003498142001@facturanext.ec",'Admin');
        //     $message->to('alexdariogc@gmail.com', 'Alex')->subject('Welcome!');
        // });
        // return response()->json(array_map('utf8_encode', $datospersona));
        return response()->json(array(
            "mensaje" => "Colaborador creado Correctamente"
        ));
        // return response()->json(array("result"=>$a));
    }

    function activar_cuenta(Request $request){

        $codigo_email=$request->input('code');
        $tipocuenta=$request->input('cuenta');
        $tabla_pass      = new Passwrds();
        $funciones       = new Funciones();

       switch ($tipocuenta) {
           case 'EEE':
               $tabla = new Empresas();
               break;
           
           case 'PPP':
              $tabla = new Personas();
               break;
       }

       $datos = $tabla->select('id_empresa','codigo_activacion','nombre_comercial','correo','Ruc','user_nextbook')->where('codigo_activacion', $codigo_email)->get();

    // if (array_key_exists(0, $datos)) {   
    if ($codigo_email==$datos[0]['codigo_activacion']) {

        $pass_nextbook                = $funciones->generarPass(12,false,'luds');
        $pass_email                   = $funciones->generarPass(12,false,'luds');
        $tabla                        = new Empresas();

        //***************************************** Registro Clave  *****************************************
        $tabla_pass->pass_email       = $pass_email;
        $tabla_pass->pass_nexbook     = $pass_nextbook;
        $tabla_pass->id_user          = $datos[0]['id_empresa'];
        $tabla_pass->save();

            //***************************************** Registrar Email *****************************************
        $correo= $this->crear_email($datos[0]['Ruc'], $pass_email);
        $data = ["pass_nextbook"=>$pass_nextbook,"user_nextbook"=>$correo];
        //***************************************** Enviar Credenciales Email *****************************************
        $correo_enviar=$datos[0]['correo'];
        $nombre_comercial=$datos[0]['nombre_comercial'];
        Mail::send('credenciales_ingreso', $data, function($message)use ($correo_enviar,$nombre_comercial)
            {
                $message->from("1003498142001@facturanext.ec",'no-reply@facturanext.com');
                $message->to($correo_enviar, $nombre_comercial)->subject('Credenciales de Ingreso');
            });
        
        $tabla::where('id_empresa', '=',$datos[0]['id_empresa'])->update(['estado' => "1"]);
        return redirect()->away('http://192.168.1.27/emailuser');
           // return response()->json(array('status' => '200'));
        }
        // }
        else{
            return response()->json(array('status' => '500'));
        }

        // return $datos;
    }
    
    public function crear_email($user,$email_pass)
    {
        $ip           = "nextbook.ec"; 
        $account      = "nextbook"; 
        $passwd       = "EiCZTO.ePLFIP"; 
        $port         = 2083; 
        $email_domain = 'facturanext.com'; 
        $email_quota  = 50; 
        $xmlapi       = new xmlapi($ip);
        $xmlapi->set_port($port); 
        $xmlapi->password_auth($account, $passwd); 
        // $email_pass = "356497";
        $result        = "";
        if (!empty($user)){
            while (true) {

                $call   = array(
                    'domain' => $email_domain,
                    'email' => $user,
                    'password' => $email_pass,
                    'quota' => $email_quota
                );

                $call_f = array(
                    'domain' => $email_domain,
                    'email' => $user,
                    'fwdopt' => "fwd",
                    'fwdemail' => ""
                );
                $xmlapi->set_debug(0); 
                
                $result         = $xmlapi->api2_query($account, "Email", "addpop", $call);
                $result_forward = $xmlapi->api2_query($account, "Email", "addforward", $call_f); 

                
                if ($result->data->result == 1) {
                    $result = $user.'@'.$email_domain;
                    if ($result_forward->data->result == 1) {
                        $result = $user . '@' . $email_domain . ' forward to ' . $dest_email;
                    }
                } else {
                    $result = $result->data->reason;
                    break;
                }
                
                break;
            }
            }
        return $result;
        
    }
}