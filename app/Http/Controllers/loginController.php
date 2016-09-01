<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//-------------------------------------- Autenticacion ---------------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
//-------------------------------------- Modelos ---------------
use App\User;
use App\Empresas;
use App\Personas;
use App\PasswrdsE;
use App\PasswrdsP;
use App\regpersona_empresas;
use App\Extras;
//-------------------------------------- Extras ---------------
use GuzzleHttp\Client;


class loginController extends Controller
{

  public function __construct(Request $request)
    {
        //----------------------------------- Modelos -----------
      $this->regpersona_empresas=new regpersona_empresas();
      $tipo_user=$request->input('tipo');
        switch ($tipo_user) {
          default:
                $this->auth = Auth::guard('web');
                $this->tabla =    new PasswrdsP();
          break;
            case 'P':
                $this->auth = Auth::guard('web');
                $this->tabla =    new PasswrdsP();
                $this->tablaDatos =    new Personas();
                break;
            
            case 'E':
                $this->tablaEx = new extras();
                $this->auth = Auth::guard('usersE');
                $this->tabla =    new PasswrdsE();
                $this->tablaDatos =    new Empresas();
                break;
        }

    }

public function login(Request $request) {

 $credentials = array('email' => $request->input('email'), 'password' => $request->input('password'));

   if (!$token = $this->auth->attempt($credentials)) {

   //    $client = new Client;
   //    $res = $client->request('POST', 'http://192.168.100.20/serviciosradio/public/login', [
   //        'json' => ['email' => $request->input('email'), 'password' => $request->input('password')]
   //    ]);
   //    $respuesta= json_decode($res->getBody(), true);
   //    if (isset($respuesta['error'])) {
         return response()->json(array("respuesta"=>false), 200);
   //    }else 
   //     return response()->json(["token"=>$respuesta['token']], 200);
       
      }

    $datos=$this->tabla->select('id','remember_token','id_user','pass_estado','email')
              ->where('email','=',$request->input('email'))->first();
 //*********************************** Datos persona que registro ********************
  $persona_registroE=$this->regpersona_empresas->select('*')
              ->where('id_empresa','=',$datos['id_user'])->first();
  //***************************************************** EXTRAS ***********************
  $extras = $this->tablaEx->select('*')->where('id_empresa', '=', $datos['id_user'])->get();

   $datosE=$this->tablaDatos->select('*')
              ->where('id_empresa','=',$datos['id_user'])->first();
              $datosE['extras']=$extras;
              $datosE['correo']=$datos['email'];
              // if ($datos['remember_token']!='') {
              //   JWTAuth::setToken($datos['remember_token'])->invalidate();
              // }
   $token = JWTAuth::fromUser($datos);
   // JWTAuth::setToken($token);

       // $id=$tabla->select('id')
       //        ->where('email','=',$request->input('email'))->first();  
     $this->tabla->where('id', '=', $datos['id'])->update(['remember_token' => $token]);
     $datosE['pass_estado']=$datos['pass_estado'];

   return response()->json(["datosE"=>$datosE,"datosPersona"=>$persona_registroE,compact('token')]);

}

public function logoutE(Request $request){

switch ($request->input('tipo')) {
    case 'E':
        $datos=$this->tabla->select('email')->where('remember_token','=',$request->input('token'))->get();
        break;
    
    case 'P':
        $datos=$this->tabla->select('email')->where('remember_token','=',$request->input('token'))->get();
        break;
}

  $user = JWTAuth::parseToken()->authenticate();
  
    JWTAuth::setToken($request->input('token'))->invalidate();
    $this->tabla->where('email', '=', $user['email'])->update(['remember_token' => '']);
// echo $datos['email'];

    return response()->json(true, 200);
}


// /**
//      * Return the authenticated user
//      *
//      * @return Response
//      */
//     public function getAuthenticatedUser()
//     {
//         try {

//             if (! $user = JWTAuth::parseToken()->authenticate()) {
//                 return response()->json(['user_not_found'], 404);
//             }

//         } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

//             return response()->json(['token_expired'], $e->getStatusCode());

//         } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

//             return response()->json(['token_invalid'], $e->getStatusCode());

//         } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

//             return response()->json(['token_absent'], $e->getStatusCode());

//         }

//         // the token is valid and we have found the user via the sub claim
//         return response()->json(compact('user'));
//     }

}
