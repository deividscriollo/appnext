<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Empresas;
use App\Personas;
use App\Colaboradores;
use App\libs\Funciones;
use App\libs\xmlapi;
use Mail;

class registroController extends Controller
{
    
    public function registrarEmpresa(Request $request)
    {
        date_default_timezone_set('America/Guayaquil'); //puedes cambiar Guayaquil por tu Ciudad
        setlocale(LC_TIME, 'spanish');
        $funciones                    = new Funciones();
        $tabla                        = new Empresas();
        $tabla->id                    = $funciones->generarID();
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
        $tabla->save();
        //****************************************************** Registrar sucursales***********************
        
        
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
        $tabla->telefono            = $request->input('telefono');
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
        $data = []; // Empty array
        
        Mail::send('welcome', $data, function($message)
        {
            $message->from("1003498142001@facturanext.ec",'Admin');
            $message->to('alexdariogc@gmail.com', 'Alex')->subject('Welcome!');
        });
        // return response()->json(array_map('utf8_encode', $datospersona));
        return response()->json(array(
            "mensaje" => "Colaborador creado Correctamente"
        ));
        // return response()->json(array("result"=>$a));
    }
    
    public function crear_email($user)
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
        $email_pass = "356497";
        $result        = false;
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
                    $result = true;
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
