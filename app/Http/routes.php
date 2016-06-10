<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('credenciales_ingreso');
});

Route::post('registroEmpresas','registroController@registrarEmpresa');
Route::post('registroPersonas','registroController@registrarPersona');
Route::post('registroColaboradores','registroController@registroColaborador');
Route::get('activar_cuenta','registroController@activar_cuenta');

Route::post('login','loginController@login');

Route::get('personas','loginController@getUsers');