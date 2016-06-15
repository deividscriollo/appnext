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
use Auth;

class loginController extends Controller
{

public function login(Request $request) {
$tipo_user=$request->input('tipo');

switch ($tipo_user) {
  default:
          $auth = Auth::guard('web');
          $tabla =    new PasswrdsP();
  break;
    case 'P':
               $auth = Auth::guard('web');
       $tabla =    new PasswrdsP();
        break;
    
    case 'E':
              $auth = Auth::guard('usersE');
              $tabla =    new PasswrdsE();
        break;
}

 $credentials = array('email' => $request->input('email'), 'password' => $request->input('password'));

   if ( ! $token = $auth->attempt($credentials)) {
       return response()->json(false, 404);
   }

    $datos=$tabla->select('id')
              ->where('email','=',$request->input('email'))->first();

   $token = JWTAuth::fromUser($datos);
   // JWTAuth::setToken($token);

       // $id=$tabla->select('id')
       //        ->where('email','=',$request->input('email'))->first();  
     $tabla->where('id', '=', $datos['id'])->update(['remember_token' => $token]);

   return response()->json(compact('token'));

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

  // $user = JWTAuth::parseToken()->authenticate();
  
    JWTAuth::setToken($request->input('token'))->invalidate();
    $tabla->where('email', '=', $datos[0]['email'])->update(['remember_token' => '']);
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
