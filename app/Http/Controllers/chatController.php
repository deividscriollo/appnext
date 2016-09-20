<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//------------------- Modelos -----------------
use App\chat;
use App\chat_mensajes;
use App\Empresas;
use App\img_perfiles;
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
        $this->img_perfiles = new img_perfiles();
        //-------------------------- Autenticacion-------
        $this->user = JWTAuth::parseToken()->authenticate();
        //--------------  Funciones ------
        $this->funciones=new Funciones();
    }

    public function send_mensaje(Request $request){

    	$result_sala1=$this->chat_sala->where('user1_id',$request->input('id_empresa'))
    							->where('user2_id',$this->user['id_user'])->first();

    	$result_sala2=$this->chat_sala->where('user1_id',$this->user['id_user'])
    							->where('user2_id',$request->input('id_empresa'))->first();

    	if (count($result_sala1)==0&&count($result_sala2)==0) {
    		//------------------------ Crear Sala ------------
    		$id_chat=$this->funciones->generarID();
    		$this->chat_sala->chat_id=$id_chat;
    		$this->chat_sala->user1_id=$this->user['id_user'];
    		$this->chat_sala->user2_id=$request->input('id_empresa');
    		$this->chat_sala->estado=TRUE;
    		$this->chat_sala->save();
    	}else{
    		if (count($result_sala1)!=0) {
    			$id_chat=$result_sala1['chat_id'];
    		}
    		if (count($result_sala2)!=0) {
    			$id_chat=$result_sala2['chat_id'];
    		}
    		
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
    			if ($id_user==$this->user['id_user']) {
    				$user_para=$this->chat_sala->select('user2_id')->where('chat_id',$value->chat_id)->first();
    				$id_user=$user_para['user2_id'];
    			}
    			$datos=$this->Empresas->select('nombre_comercial','razon_social')->where('id_empresa',$id_user)->get();
    			$img_perfil=$this->img_perfiles->select('img')->where('id_empresa',$id_user)->where('estado',1)->first();
    			if ($datos[0]['nombre_comercial']=='no disponible') {
    				$chats[$key]['para']=$datos[0]['razon_social'];
    			}else{
    				$chats[$key]['para']=$datos[0]['nombre_comercial'];
    			}
    			$chats[$key]['img']=$img_perfil['img'];
			}
    		 return response()->json(["respuesta"=>true,"datos"=>$chats],200);
    	}

    }
    public function get_mensajes(Request $request){
    	$mensajes=$this->chat_mensajes->select(['mensaje','user_id'])->where('chat_id',$request->input('chat_id'))->orderBy('created_at','DESC')->get();
    	$enviados=0;
    	$recibidos=0;
    	$mensaje_enviados=array();
    	$mensaje_recibidos=array();

    	foreach ($mensajes as $key => $value) {
    		$datos=$this->Empresas->where('id_empresa',$value['user_id'])->first();
			if ($datos['nombre_comercial']=='no disponible') {
    				$nombre_user=$datos['razon_social'];
    			}else{
    				$nombre_user=$datos['nombre_comercial'];
    			}
    		$mensajes[$key]['usuario']=$nombre_user;

    		if ($value['user_id']==$this->user['id_user']) {
    			$mensaje_enviados[$enviados]=$value;
    			$enviados++;
    		}else{
    			$mensaje_recibidos[$recibidos]=$value;
    			$recibidos++;
    		}
    	}

    	return response()->json(["respuesta"=>true,"enviados"=>$mensaje_enviados,"recibidos"=>$mensaje_recibidos],200);
    }


}
