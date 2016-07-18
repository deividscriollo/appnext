<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Empresas;
use App\Personas;
use App\PasswrdsE;
use App\PasswrdsP;
use App\regpersona_empresas;
use Auth;
use GuzzleHttp\Client;

class loginController extends Controller
{

public function login(Request $request) {
$tipo_user=$request->input('tipo');
$regpersona_empresas=new regpersona_empresas();
switch ($tipo_user) {
  default:
          $auth = Auth::guard('web');
          $tabla =    new PasswrdsP();
  break;
    case 'P':
               $auth = Auth::guard('web');
       $tabla =    new PasswrdsP();
       $tablaDatos =    new Personas();
        break;
    
    case 'E':
              $auth = Auth::guard('usersE');
              $tabla =    new PasswrdsE();
              $tablaDatos =    new Empresas();
        break;
}

 $credentials = array('email' => $request->input('email'), 'password' => $request->input('password'));

   if ( ! $token = $auth->attempt($credentials)) {

      $client = new Client;
      $res = $client->request('POST', 'http://192.168.100.17/serviciosradio/public/login', [
          'json' => ['email' => $request->input('email'), 'password' => $request->input('password')]
      ]);
      $respuesta= json_decode($res->getBody(), true);
      if (isset($respuesta['error'])) {
        return response()->json(false, 404);
      }else 
       return response()->json(["token"=>$respuesta['token']], 200);
      }

    $datos=$tabla->select('id','remember_token','id_user','pass_estado')
              ->where('email','=',$request->input('email'))->first();
 //*********************************** Datos persona que registro ********************
  $persona_registroE=$regpersona_empresas->select('*')
              ->where('id_empresa','=',$datos['id_user'])->first();

   $datosE=$tablaDatos->select('*')
              ->where('id_empresa','=',$datos['id_user'])->first();
              // if ($datos['remember_token']!='') {
              //   JWTAuth::setToken($datos['remember_token'])->invalidate();
              // }
   $token = JWTAuth::fromUser($datos);
   // JWTAuth::setToken($token);

       // $id=$tabla->select('id')
       //        ->where('email','=',$request->input('email'))->first();  
     $tabla->where('id', '=', $datos['id'])->update(['remember_token' => $token]);
     $datosE['pass_estado']=$datos['pass_estado'];

   return response()->json(["datosE"=>$datosE,"datosPersona"=>$persona_registroE,compact('token')]);

}

public function logoutE(Request $request){

switch ($request->input('tipo')) {
    case 'E':
            $tabla=new PasswrdsE();
            $datos=$tabla->select('email')->where('remember_token','=',$request->input('token'))->get();
        break;
    
    case 'P':
         $tabla =    new PasswrdsP();
         $datos=$tabla->select('email')->where('remember_token','=',$request->input('token'))->get();
        break;
}

  $user = JWTAuth::parseToken()->authenticate();
  
    JWTAuth::setToken($request->input('token'))->invalidate();
    $tabla->where('email', '=', $user['email'])->update(['remember_token' => '']);
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
