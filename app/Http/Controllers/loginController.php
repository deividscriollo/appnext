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

public function __construct(){
		// $this->middleware('CORS');
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}
	
public function getUsers(){
	$tabla =new Personas();
	$lista=$tabla->get();

	return $lista;

}
public function login(Request $request) {
	$tabla =	new PasswrdsP();
	$datos=$tabla->select('id_user')
			  ->where('email','=',$request->input('email'))->first();

			  if ($datos['id_user']!='') {
			  	$pass_bdd=PasswrdsP::select('email','pass_nextbook')
			  ->where('id_user','=',$datos['id_user'])->get();
if ($pass_bdd[0]['pass_nextbook']==$request->input('password')) {
	echo "OK";
	$credentials = $request->only('email', 'password');
	$token = JWTAuth::fromUser($datos);

}
else{
	echo "FAIL";
}
}
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
