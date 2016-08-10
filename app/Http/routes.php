
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
    Route::post('registroColaboradores', 'registroController@registroColaborador');
    Route::get('activar_cuenta', 'registroController@activar_cuenta');
    Route::post('login', 'loginController@login');
     // /////////////////////////////////////////////////////////////////////// EMPRESAS ///////////////////
        // --------------------------------------- get Empresas -----------
        Route::get('buscarEmpresas', 'BusquedaController@get_empresas');

            // /////////////////////////////////////////////////////////////////////// CATEGORIAS ///////////////////
        // --------------------------------------- add Categoria -----------
        Route::post('addCategoria', 'categoriasController@addCategoria');
         // --------------------------------------- get Categorias -----------
        Route::get('getCategorias', 'categoriasController@getCategorias');
    // /////////////////////////////////////////////////////////////////////// PROVINCIAS ///////////////////
        // --------------------------------------- add Provincia -----------
        Route::post('addProvincia', 'provinciasController@add_provincia');
        // --------------------------------------- get Provincias -----------
        Route::get('getProvincias', 'provinciasController@get_provincias');

        // /////////////////////////////////////////////////////////////////////// USUARIOS NEXTBOOK ///////////////////
        // --------------------------------------- Usuario Existe -----------
        Route::get('buscarUsernext', 'existenciaController@usernext_exist');
        // ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    Route::group(['middleware' => ['jwt.auth']], function ()
        {
        Route::get('getDatosE', 'datosController@getDatosE');
        Route::get('getDatosP', 'datosController@getDatosP');
        // -----------------------------------SUCURSALES --------------------
        //------------------------------------- GET -------------------------
        Route::get('getsucursales', 'sucursalesController@getsucursales');
        //----------------------------------- CAMBIAR CATEGORIA--------------
        Route::post('setCategoriaSucursal', 'sucursalesController@set_categoria_sucursal');
        //------------------------------------------ LOGOUT --------------------
        Route::post('logoutE', 'loginController@logoutE');
        // ************************************ AÑADIR EXTRA ***********************;
        Route::post('addExtra', 'perzonalizacionController@addExtra');
        // /////////////////////////////////////////////////////////////////////////////////// PASSWORD  PERZONALIZACION///////////////////////////////
        // ************************************ CAMBIAR PASSWORD ***********************;
        Route::post('changePass', 'perzonalizacionController@change_pass');
        // ************************************ ESTADO PASSWORD ***********************;
        Route::post('PassState', 'perzonalizacionController@pass_state');
        // ************************************ VERIFICAR PASSWORD ***********************;
        Route::post('VerficarPass', 'perzonalizacionController@verify_pass');
        // ************************************ Actualizar informacion ***********************;
        Route::post('updateInfo', 'perzonalizacionController@update_info');
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
        // ************************************ LEER FACTURAS RECHAZADAS ***********************;
        Route::get('getFacturasRechazadas', 'facturaController@get_facturas_rechazadas');
        // ////////////////////////////////////////////////// IMAGENES DE PERFIL //////////////
        // --------------------------------------- AÑADIR IMAGEN DE PERFIL -----------
        Route::post('addImgPerfil', 'PerfilesController@add_img_perfil');
        // --------------------------------------- SELECCIONAR IMAGEN DE PERFIL -----------
        Route::post('setImgPerfil', 'PerfilesController@set_img_perfil');
        // --------------------------------------- CARGAR IMAGENES PERFIL -----------
        Route::get('loadImgsPerfil', 'PerfilesController@load_imgs_perfil');
        // --------------------------------------- GET IMAGENES PERFIL -----------
        Route::get('getImgPerfil', 'PerfilesController@get_img_perfil');
        // ////////////////////////////////////////////////// IMAGENES DE PORTADA //////////////
        // --------------------------------------- AÑADIR IMAGEN DE PORTADA -----------
        Route::post('addImgPortada', 'PortadasController@add_img_portada');
        // --------------------------------------- SELECCIONAR IMAGEN DE PORTADA -----------
        Route::post('setImgPortada', 'PortadasController@set_img_portada');
        // --------------------------------------- CARGAR IMAGENES PORTADA -----------
        Route::get('loadImgsPortada', 'PortadasController@load_imgs_portada');
        // --------------------------------------- GET IMAGENES PORTADA -----------
        Route::get('getImgPortada', 'PortadasController@get_img_portada');
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
           // --------------------------------------- Borrar Nomina -----------
        Route::post('deleteNomina', 'NominaController@delete_nomina');
           // --------------------------------------- Get Nomina -----------
        Route::get('getNomina', 'NominaController@get_nomina');

          // /////////////////////////////////////////////////////////////////////// PROVEEDORES ///////////////////
        // --------------------------------------- add Proveedores -----------
        Route::post('addProveedor', 'proveedoresController@add_proveedor');
        // --------------------------------------- Actualizar Proveedores -----------
        Route::post('updateProveedor', 'proveedoresController@update_proveedor');
           // --------------------------------------- Borrar Proveedores -----------
        Route::post('deleteProveedor', 'proveedoresController@delete_proveedor');
           // --------------------------------------- Get Proveedores -----------
        Route::get('getProveedores', 'proveedoresController@get_proveedores');

        // /////////////////////////////////////////////////////////////////////// DEPARTAMENTOS ///////////////////
        // --------------------------------------- add Departamento -----------
        Route::post('addDepartamento', 'NominaController@add_departamento');
        // --------------------------------------- Actualizar Departamento -----------
        Route::post('updateDepartamento', 'NominaController@update_departamento');
           // --------------------------------------- Borrar Departamento -----------
        Route::post('deleteDepartamento', 'NominaController@delete_departamento');
           // --------------------------------------- Get Departamentos -----------
        Route::get('getDepartamentos', 'NominaController@get_departamentos');

        // /////////////////////////////////////////////////////////////////////// CARGOS ///////////////////
        // --------------------------------------- add Departamento -----------
        Route::post('addCargo', 'NominaController@add_cargo');
        // --------------------------------------- Actualizar Cargo -----------
        Route::post('updateCargo', 'NominaController@update_cargo');
           // --------------------------------------- Borrar Cargo -----------
        Route::post('deleteCargo', 'NominaController@delete_cargo');
           // --------------------------------------- Get Cargos -----------
        Route::get('getCargos', 'NominaController@get_cargos');


        });
    });
