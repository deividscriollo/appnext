<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Empresas;
use App\Personas;
use App\libs\Funciones;

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
        $funciones                    = new Funciones();
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
}
