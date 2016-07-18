
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
Route::get('/', function ()
    {
    return view('email_registro');
    });
// Route::auth();
// Route::get('/', 'HomeController@index');
// Route::get('/home', 'HomeController@index');
Route::group(['middleware' => 'cors'], function ()
    {
    Route::post('registroEmpresas', 'registroController@registrarEmpresa');
    Route::post('registroPersonas', 'registroController@registrarPersona');
    Route::post('registroColaboradores', 'registroController@registroColaborador');
    Route::get('activar_cuenta', 'registroController@activar_cuenta');
    Route::post('login', 'loginController@login');
        // /////////////////////////////////////////////////////////////////////// USUARIOS NEXTBOOK ///////////////////
        // --------------------------------------- Usuario Existe -----------
        Route::get('buscarUsernext', 'existenciaController@usernext_exist');
        // ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    Route::group(['middleware' => ['jwt.auth']], function ()
        {
        Route::get('getDatosE', 'datosController@getDatosE');
        Route::get('getDatosP', 'datosController@getDatosP');
        Route::get('getsucursales', 'datosController@getsucursales');
        Route::post('logoutE', 'loginController@logoutE');
        // ************************************ AÑADIR EXTRA ***********************;
        Route::post('addExtra', 'perzonalizacionController@addExtra');
        // /////////////////////////////////////////////////////////////////////////////////// PASSWORD ///////////////////////////////
        // ************************************ CAMBIAR PASSWORD ***********************;
        Route::post('changePass', 'perzonalizacionController@change_pass');
        // ************************************ ESTADO PASSWORD ***********************;
        Route::post('PassState', 'perzonalizacionController@pass_state');
        // ************************************ VERIFICAR PASSWORD ***********************;
        Route::get('VerficarPass', 'perzonalizacionController@verify_pass');
        // /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // ************************************ ADD FACTURAS ***********************;
        Route::get('readFacturas', 'facturaController@add_fac_bdd');
        // ************************************ LEER FACTURAS ***********************;
        Route::get('getFacturas', 'facturaController@get_facturas');
        // ************************************ SUBIR ARCHIVOS XML ***********************;
        Route::post('uploadFactura', 'facturaController@upload_xmlfile');
        // ************************************ DESCARGAR ARCHIVOS XML ***********************;
        Route::post('Downloadlink', 'facturaController@gen_download_link');
        Route::get('Downloadfac', 'facturaController@Download_fac');
        // --------------------------------------- AÑADIR IMAGEN DE PERFIL -----------
        Route::post('addImgPerfil', 'PerfilesController@add_img_perfil');
        // --------------------------------------- SELECCIONAR IMAGEN DE PERFIL -----------
        Route::post('setImgPerfil', 'PerfilesController@set_img_perfil');
        // --------------------------------------- CARGAR IMAGENES PERFIL -----------
        Route::get('loadImgsPerfil', 'PerfilesController@load_imgs_perfil');
        // --------------------------------------- GET IMAGENES PERFIL -----------
        Route::get('getImgPerfil', 'PerfilesController@get_img_perfil');
        // /////////////////////////////////////////////////////////////////////// CLIENTES ///////////////////
        // --------------------------------------- AÑADIR CLIENTE -----------
        Route::post('addCliente', 'ClientesController@save');
        // --------------------------------------- EDITAR CLIENTE -----------
        Route::post('editCliente', 'ClientesController@edit');
        // --------------------------------------- ELIMINAR CLIENTE -----------
        Route::delete('deleteCliente', 'ClientesController@delete');
        // --------------------------------------- GET CLIENTES -----------
        Route::get('getClientes', 'ClientesController@get');
        // --------------------------------------- Cliente Existe -----------
        Route::get('buscarCliente', 'existenciaController@cliente_exist');
         // /////////////////////////////////////////////////////////////////////// PERSONA QUE REGISTRA ///////////////////
        // --------------------------------------- Persona Existe -----------
        Route::get('getDatosPropietario', 'persona_q_registraController@get_datos');
        // --------------------------------------- Guardar datos personales y cambio de contraseña -----------
        Route::post('setDatosPropietario', 'persona_q_registraController@set_datos');
        // /////////////////////////////////////////////////////////////////////// NOMINA ///////////////////
        // --------------------------------------- add Nomina -----------
        Route::post('addNomina', 'NominaController@add_nomina');
        // --------------------------------------- Actualizar Nomina -----------
        Route::post('updateNomina', 'NominaController@update_nomina');
        });
    });
