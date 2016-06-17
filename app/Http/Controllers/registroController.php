<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Empresas;
use App\regpersona_empresas;
use App\Personas;
use App\Colaboradores;
use App\Sucursales;
use App\passwrdsE;
use App\passwrdsP;
use App\libs\Funciones;
use App\libs\xmlapi;
use Mail;

class registroController extends Controller
{
   
    public function registrarEmpresa(Request $request)
    {
        date_default_timezone_set('America/Guayaquil'); 
        setlocale(LC_TIME, 'spanish');
        $funciones                    = new Funciones();
        $activation_code              = $funciones->generarActivacion(md5($request->input('cedula')));
        $id                           = $funciones->generarID();
        $tablaE                        = new Empresas();
        $tablaPersonareg               = new regpersona_empresas();
        $tablaE->id_empresa            = $id;
        $tablaE->Ruc                   = $request->input('cedula');
        $tablaE->razon_social          = $request->input('razon_social');
        $tablaE->nombre_comercial      = $request->input('nombre_comercial');
        $tablaE->estado_contribuyente  = $request->input('estado_contribuyente');
        $tablaE->tipo_contribuyente    = $request->input('tipo_contribuyente');
        $tablaE->obligado_contabilidad = $request->input('obligado_llevar_contabilidad');
        $tablaE->actividad_economica   = $request->input('actividad_principal');
        $tablaE->codigo_activacion     = $activation_code;
        $tablaE->estado                = 0;
        $saveE=$tablaE->save();
        //************************************************* Persona que Registra ***********************
        $tablaPersonareg->idp_regE              =$funciones->generarID();
        $tablaPersonareg->nombres_apellidos     = $request->input('nombres_apellidos');
        $tablaPersonareg->fecha_nacimiento      = utf8_encode($request->input('fecha_nacimiento'));
        $tablaPersonareg->correo                = $request->input('correo');
        $tablaPersonareg->telefono              = $request->input('telefono');
        $tablaPersonareg->celular               = $request->input('celular');
        $tablaPersonareg->estado                = 1;
        $tablaPersonareg->id_empresa            = $id;
        $saveS=$tablaPersonareg->save();
        //****************************************************** Registrar sucursales***********************

         $establecimientos=$request->input('sucursales')['sucursal'];
            foreach ($establecimientos as $sucursal) {
                $tabla_sucursal=new Sucursales();
                $tabla_sucursal->codigo =$sucursal['codigo'];
                $tabla_sucursal->direccion =$sucursal['direccion'];
                $tabla_sucursal->estado =$sucursal['estado'];
                $tabla_sucursal->nombre_sucursal =$sucursal['nombre_sucursal'];
                $tabla_sucursal->id_empresa =$id;
                $tabla_sucursal->save();
            }

        //******************************************* Enivar email de verificacion**************************
        $nombre_comercial=$request->input('nombre_comercial');
        $data = ["codigo"=>$activation_code,"cuenta"=>"EEE",'nombre_comercial'=>$nombre_comercial];
        $correo_enviar=$request->input('correo');
        
        Mail::send('email_registro', $data, function($message)use ($correo_enviar,$nombre_comercial)
            {
                $message->from("no-reply@facturanext.com",'no-reply@facturanext.com');
                $message->to($correo_enviar,$nombre_comercial)->subject('Verificación de Email');
            });
        
        if(!$saveE&&!$saveS){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
        
    }
    public function registrarPersona(Request $request)
    {
        $funciones                = new Funciones();
        $tabla                    = new Personas();
        $activation_code          = $funciones->generarActivacion(md5($request->input('cedula')));
        $tabla->id_persona        = $funciones->generarID();
        $tabla->Nombres_apellidos = $request->input('nombres_apellidos');
        $tabla->cedula            = $request->input('cedula');
        $tabla->provincia         = $request->input('provincia');
        $tabla->canton            = $request->input('canton');
        $tabla->parroquia         = $request->input('parroquia');
        $tabla->zona              = $request->input('zona');
        $tabla->correo            = $request->input('correo');
        $tabla->telefono          = $request->input('telefono');
        $tabla->celular           = $request->input('celular');
        $tabla->codigo_activacion = $activation_code;
        $tabla->estado                = 0;
        $saveP=$tabla->save();

        //******************************************* Enivar email de verificacion**************************
        $correo_enviar=$request->input('correo');
        $nombre_comercial=$request->input('nombres_apellidos');
        $data = ["codigo"=>$activation_code,"cuenta"=>"PPP","nombre_comercial"=>$nombre_comercial];
        
        Mail::send('email_registro', $data, function($message)use ($correo_enviar,$nombre_comercial)
            {
                $message->from("no-reply@facturanext.com",'no-reply@facturanext.com');
                $message->to($correo_enviar,$nombre_comercial)->subject('Verificación de Email');
            });

        // return response()->json(array_map('utf8_encode', $datospersona));
         if(!$saveP){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
    }
    
    public function registroColaborador(Request $request)
    {
        $funciones         = new Funciones();
        $tabla             = new Colaboradores();
        $tabla->id_colaborador  = $funciones->generarID();
        $tabla->correo     = $request->input('correo');
        $tabla->pass       = bcrypt($request->input('pass'));
        $tabla->estado     = 0;
        $tabla->id_empresa = $request->input('id_empresa');
        $saveC=$tabla->save();
        
        // $a = $this->crear_email('1003498142', 'root@123');
        // $data = ["nombre"=>"Alex"]; // Empty array
        
        // Mail::send('email_registro', $data, function($message)
        // {
        //     $message->from("no-reply@facturanext.com",'Admin');
        //     $message->to('alexdariogc@gmail.com', 'Alex')->subject('Welcome!');
        // });
        if(!$saveC){
            App::abort(500, 'Error');
        }else{
            return response()->json(true,200);
        }
    }

    function activar_cuenta(Request $request){

        $codigo_email=$request->input('code');
        $tipocuenta=$request->input('cuenta');
        $funciones       = new Funciones();

       switch ($tipocuenta) {
           case 'EEE':
               $tabla = new Empresas();
               $tabla_pass      = new passwrdsE();
               $tablaPersonareg = new regpersona_empresas();
               $datos = $tabla->select('id_empresa','codigo_activacion','nombre_comercial','Ruc','estado')->where('codigo_activacion', $codigo_email)->get();
               break;
           
           case 'PPP':
              $tabla_pass      = new passwrdsP();
              $tabla = new Personas();
              $datos = $tabla->select('id_persona','codigo_activacion','Nombres_apellidos','correo','cedula','estado')->where('codigo_activacion', $codigo_email)->get();
               break;
       }
    if (empty($datos[0]['estado'])) {   

switch ($tipocuenta) {
           case 'EEE':
               $datospersonaregE = $tablaPersonareg->select('correo')->where('id_empresa', $datos[0]['id_empresa'])->orderBy('created_at')->first();
               $correo_enviar=$datospersonaregE['correo'];
               $id_user=$datos[0]['id_empresa'];
                $nombre_comercial=$datos[0]['nombre_comercial'];
                $documento=$datos[0]['Ruc'];
               break;
           
           case 'PPP':
           $correo_enviar=$datos[0]['correo'];
              $id_user=$datos[0]['id_persona'];
            $nombre_comercial=$datos[0]['Nombres_apellidos'];
            $documento=$datos[0]['cedula'];
               break;
       }

    if ($codigo_email==$datos[0]['codigo_activacion']&&$datos[0]['estado']=='0') {

        $pass_nextbook                = $funciones->generarPass(12);
        $pass_email                   = $funciones->generarPass(12);

        //***************************************** Registro Clave  *****************************************
        $tabla_pass->email             = $documento.'@'.'facturanext.com';
        $tabla_pass->pass_email       = $pass_email;
        $tabla_pass->password     = bcrypt($pass_nextbook);
        $tabla_pass->remember_token ='';
        $tabla_pass->id_user          = $id_user;
        $tabla_pass->save();

            //***************************************** Registrar Email *****************************************
        $correo= $this->crear_email($documento, $pass_email);
        $data = ["pass_nextbook"=>$pass_nextbook,"user_nextbook"=>$correo,"nombre_comercial"=>$nombre_comercial];
        //***************************************** Enviar Credenciales Email *****************************************
        Mail::send('credenciales_ingreso', $data, function($message)use ($correo_enviar,$nombre_comercial)
            {
                $message->from("no-reply@facturanext.com",'no-reply@facturanext.com');
                $message->to($correo_enviar, $nombre_comercial)->subject('Credenciales de Ingreso');
            });
        
        switch ($tipocuenta) {
            case 'EEE':
                $tabla::where('id_empresa', '=',$id_user)->update(['estado' => "1"]);
                break;
            
            case 'PPP':
                $tabla::where('id_persona', '=',$id_user)->update(['estado' => "1"]);
                break;
        }
        return redirect()->away('http://192.168.1.34/emailuser');
           // return response()->json(array('status' => '200'));
        }else{
            return response()->json(false, 500);
        }
        }
        else{
            return response()->json(false, 500);
        }

        // return $datos;
    }
    
    private function crear_email($user,$email_pass)
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
