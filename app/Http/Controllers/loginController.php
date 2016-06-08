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

class loginController extends Controller
{


public function login(Request $request) {
	$tabla =	new Personas();
	$datos=$tabla->select('id_persona','estado','user_nextbook')
			  ->where('correo','=',$request->input('email'))
			  ->orWhere('user_nextbook', '=',$request->input('email'))->get();

			  if ($datos[0]['estado']=='1') {
			 $pass_bdd=PasswrdsP::select('pass_nextbook')
			  ->where('id_user','=',$datos[0]['id_persona'])->get();
			  }
if ($pass_bdd[0]['pass_nextbook']==$request->input('password')) {
	$credentials = $request->only('email', 'password');
	$token = JWTAuth::attempt($credentials);
	echo "OK";
}
else{
	echo "FAIL";
}

   // $credentials = $request->only('email', 'password');
   //      try {
   //          // attempt to verify the credentials and create a token for the user
   //          if (! $token = JWTAuth::attempt($credentials)) {
   //              return response()->json(['error' => 'Usuario / Contraseña incorrectos'], 401);
   //          }
   //      } catch (JWTException $e) {
   //          // something went wrong whilst attempting to encode the token
   //          return response()->json(['error' => 'could_not_create_token'], 500);
   //      }
   //      // all good so return the token
   //       return response()->json(compact('token'));

	 return response()->json(compact('token'));
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
