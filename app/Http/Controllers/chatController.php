<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------- Modelos -----------------
use App\chat;
use App\chat_mensajes;
use App\Empresas;
//-------------------------  autenticacion -------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//----------------------- Funciones ------------
use App\libs\Funciones;

class chatController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        //----------------------------------- Modelos -----------
        $this->chat_sala  = new chat();
        $this->chat_mensajes = new chat_mensajes();
        $this->Empresas = new Empresas();
        //-------------------------- Autenticacion-------
        $this->user = JWTAuth::parseToken()->authenticate();
        //--------------  Funciones ------
        $this->funciones=new Funciones();
    }

    public function send_mensaje(Request $request){

    	$result=$this->chat_sala->where('user1_id',$request->input('id_empresa'))
    							->where('user2_id',$this->user['id_user'])
    							->orWhere('user1_id',$this->user['id_user'])
    							->orWhere('user2_id',$request->input('id_empresa'))->first();

    	if (count($result)==0) {
    		//------------------------ Crear Sala ------------
    		$id_chat=$this->funciones->generarID();
    		$this->chat_sala->chat_id=$id_chat;
    		$this->chat_sala->user1_id=$this->user['id_user'];
    		$this->chat_sala->user2_id=$request->input('id_empresa');
    		$this->chat_sala->estado=TRUE;
    		$this->chat_sala->save();
    	}else{
    		$id_chat=$result['chat_id'];
    	}
    	//---------------- Guardar Mensaje --------------------
    	$id_chat_mensajes=$this->funciones->generarID();
    		$this->chat_mensajes->chat_mensajes_id=$id_chat_mensajes;
    		$this->chat_mensajes->chat_id=$id_chat;
    		$this->chat_mensajes->user_id=$this->user['id_user'];
    		$this->chat_mensajes->mensaje=$request->input('mensaje');
    		$this->chat_mensajes->estado_view=FALSE;
    		$this->chat_mensajes->save();

    	return response()->json(["respuesta"=>true],200);
    }

      public function get_chats(Request $request){

    	$result_sala=$this->chat_sala->where('user1_id',$this->user['id_user'])
    							->orWhere('user2_id',$this->user['id_user'])->get();

    	if (count($result_sala)==0) {
    		 return response()->json(["respuesta"=>false],200);
    	}else{

    	$chats=[];
    	foreach ($result_sala as $key => $value) {
    		$result=$this->chat_mensajes->where('chat_id',$value->chat_id)->orderBy('created_at','DESC')->first();
    		$chats[$key]=$result;
    	}
    		foreach ($chats as $key => $value) {
    			$id_user=$value->user_id;
    			$datos=$this->Empresas->select('nombre_comercial','razon_social')->where('id_empresa',$id_user)->get();
    			$chats[$key]['from']=$datos;
			}
    		 return response()->json(["respuesta"=>true,"datos"=>$chats],200);
    	}

    }

}
