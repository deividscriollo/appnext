<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\regpersona_empresas;
// -------------------------- Autenticacion ----------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//-------------------- modelos ----------------
use App\PasswrdsE;


class persona_q_registraController extends Controller

    {
    public function __construct(Request $request)

        {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        // ------------------------------------ Modelos ----------------------
        $this->tablaPersonareg = new regpersona_empresas();
        $this->tablaPass = new PasswrdsE();
        // --------------------------------------- Autenticacion --------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        }
    public function get_datos(Request $request)

        {
        $resultado = $this->tablaPass->select('pass_estado')->where('id_user', $this->user['id_user'])->first();
        if ($resultado->pass_estado == 0)
            {
            return response()->json(["respuesta" => false], 200);
            }
          else
            {
            $resultado = $this->tablaPersonareg->where('id_empresa', '=', $this->user['id_user'])->first();
            return response()->json(["respuesta" => true, "datosP" => $resultado], 200);
            }
        }
    public function set_datos(Request $request)

        {
        $resultado = $this->tablaPersonareg->where('id_empresa', $this->user['id_user'])->update(['cedula' => $request->input('cedula') , 'nombres_apellidos' => $request->input('nombres') . ' ' . $request->input('apellidos') , 'fecha_nacimiento' => $request->input('fecha_nac') , 'telefono' => $request->input('telefono') , 'celular' => $request->input('telefono') ]);
        // ---------------------------------------- Actualizar Pass ---------------------
        $result = $this->tablaPass->where('id_user', '=', $this->user['id_user'])->update(['password' => bcrypt($request->input('new_pass')) , 'pass_estado' => 1]);
        if ($resultado && $result)
            {
            return response()->json(["respuesta" => true], 200);
            }
          else return response()->json(["respuesta" => false], 200);
        }
    }
