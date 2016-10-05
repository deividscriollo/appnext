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
    return view('welcome');
    });
// Route::auth();
// Route::get('/', 'HomeController@index');
// Route::get('/home', 'HomeController@index');
Route::group(['middleware' => 'cors'], function ()
    {

    Route::get('pdf', 'Pdf_XML_Controller@generar_pdf');
    Route::get('xml', 'Pdf_XML_Controller@generar_xml');
    // Route::get('pdf', function ()
    // {
    // return view('invoice');
    // });
        
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

    // ------------------------------------------ Tipo de comsumos y documentos----------------
        Route::post('addTipoDocumentos', 'tiposController@add_tipo_documentos');
        Route::get('getTipoDocumentos', 'tiposController@get_tipo_documentos');
    // /////////////////////////////////////////////////////////////////////// GASTOS ///////////////////
        // --------------------------------------- add Provincia -----------
        Route::post('addGasto', 'gastosController@add_gasto');
        // --------------------------------------- get Provincias -----------
        Route::get('getGastos', 'gastosController@get_gastos');

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
        // ************************************ LEER NUEVAS FACTURAS ***********************;
        Route::get('getNewFacturas', 'facturaController@get_new_facturas');
        // ************************************ SUBIR ARCHIVOS XML ***********************;
        Route::post('uploadFactura', 'facturaController@upload_xmlfile');
        // ************************************ ACTUALIZAR ESTADO FACTURA ***********************;
        Route::post('uploadViewFactura', 'facturaController@update_estado_view');
        // ************************************ ACTUALIZAR TIPO DE CONSUMO ***********************;
        Route::post('updateTipoConsumo', 'facturaController@update_tipo_consumo');
        // // ************************************ DESCARGAR ARCHIVOS XML ***********************;
        // Route::post('Downloadlink', 'facturaController@gen_download_link');
        // Route::get('Downloadfac', 'facturaController@Download_fac');
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
                                // ////////////////////////////////////////////////// IMAGENES DE LOGO //////////////
        // --------------------------------------- AÑADIR IMAGEN DE LOGO -----------
        Route::post('addImgLogo', 'logosController@add_img_logo');
        // --------------------------------------- SELECCIONAR IMAGEN DE LOGO -----------
        Route::post('setImgLogo', 'logosController@set_img_logo');
        // --------------------------------------- CARGAR IMAGENES LOGO -----------
        Route::get('loadImgsLogo', 'logosController@load_imgs_logo');
        // --------------------------------------- GET IMAGENES LOGO -----------
        Route::get('getImgLogo', 'logosController@get_img_logo');
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
        // Route::get('buscarCliente', 'existenciaController@cliente_exist');
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
        // // --------------------------------------- Actualizar Proveedores -----------
        // Route::post('updateProveedor', 'proveedoresController@update_proveedor');
        //    // --------------------------------------- Borrar Proveedores -----------
        // Route::post('deleteProveedor', 'proveedoresController@delete_proveedor');
           // --------------------------------------- Get Proveedores -----------
        Route::get('getProveedores', 'proveedoresController@get_proveedores');
        // --------------------------------------- Get Datos Proveedores by Ruc -----------
        Route::get('getProveedorbyRuc', 'proveedoresController@get_datos_proveedor_by_Ruc');
        // --------------------------------------- Get Datos Proveedores buscador -----------
        Route::get('getBuscarProveedor', 'proveedoresController@get_datos_proveedor_buscador');

        // ------------------------------------------- GRUPOS ---------------------------------------------
        //------------------------------------- Añadir grupo ------------------------
        Route::post('addGrupo', 'inventario\grupoController@add_grupo');
        //------------------------------------- modificar grupo ------------------------
        Route::post('updateGrupo', 'inventario\grupoController@update_grupo');
        //------------------------------------- eliminar grupo ------------------------
        Route::post('deleteGrupo', 'inventario\grupoController@delete_grupo');
        //------------------------------------- modificar grupo ------------------------
        Route::get('getGrupos', 'inventario\grupoController@get_grupos');
        //------------------------------------- ultimo codigo grupo ------------------------
        Route::get('ultimoCodeGrupo', 'inventario\grupoController@ultimo_code_grupo');

        // ------------------------------------------- MODO ADQUISICION ---------------------------------------------
        //------------------------------------- Añadir modo_adquisicion ------------------------
        Route::post('addmodo_adquisicion', 'inventario\modo_adquisicionController@add_modo_adquisicion');
        //------------------------------------- modificar modo_adquisicion ------------------------
        Route::post('updatemodo_adquisicion', 'inventario\modo_adquisicionController@update_modo_adquisicion');
        //------------------------------------- eliminar modo_adquisicion ------------------------
        Route::post('deletemodo_adquisicion', 'inventario\modo_adquisicionController@delete_modo_adquisicion');
        //------------------------------------- modificar modo_adquisicion ------------------------
        Route::get('getmodo_adquisiciones', 'inventario\modo_adquisicionController@get_modo_adquisiciones');
        //------------------------------------- ultimo codigo modo_adquisicion ------------------------
        Route::get('ultimoCodemodo_adquisicion', 'inventario\modo_adquisicionController@ultimo_code_modo_adquisicion');

        // ------------------------------------------- ESTADO DEL BIEN ---------------------------------------------
        //------------------------------------- Añadir estado del bien ------------------------
        Route::post('addestadobn', 'inventario\estadobnController@add_estadobn');
        //------------------------------------- modificar estado del bien ------------------------
        Route::post('updateestadobn', 'inventario\estadobnController@update_estadobn');
        //------------------------------------- eliminar estado del bien ------------------------
        Route::post('deleteestadobn', 'inventario\estadobnController@delete_estadobn');
        //------------------------------------- modificar estado del bien ------------------------
        Route::get('getestadobnes', 'inventario\estadobnController@get_estadobnes');
        //------------------------------------- ultimo codigo estado del bien ------------------------
        Route::get('ultimoCodeestadobn', 'inventario\estadobnController@ultimo_code_estadobn');

           // ------------------------------------------- UBICACIONES ---------------------------------------------
        //------------------------------------- Añadir ubicaciones ------------------------
        Route::post('addUbicacion', 'inventario\ubicacionesController@add_ubicacion');
        //------------------------------------- modificar ubicaciones ------------------------
        Route::post('updateUbicacion', 'inventario\ubicacionesController@update_ubicacion');
        //------------------------------------- eliminar ubicaciones ------------------------
        Route::post('deleteUbicacion', 'inventario\ubicacionesController@delete_ubicacion');
        //------------------------------------- modificar ubicaciones ------------------------
        Route::get('getUbicaciones', 'inventario\ubicacionesController@get_ubicaciones');
        //------------------------------------- ultimo codigo ubicaciones ------------------------
        Route::get('ultimoCodeUbicacion', 'inventario\ubicacionesController@ultimo_code_ubicacion');

        // ------------------------------------------- MOTIVOS BAJAS ---------------------------------------------
        //------------------------------------- Añadir motivos bajas ------------------------
        Route::post('addMotivosBajas', 'inventario\motivosbajasController@add_motivosbajas');
        //------------------------------------- modificar motivos bajas ------------------------
        Route::post('updateMotivosBajas', 'inventario\motivosbajasController@update_motivosbajas');
        //------------------------------------- eliminar motivos bajas ------------------------
        Route::post('deleteMotivosBajas', 'inventario\motivosbajasController@delete_motivosbajas');
        //------------------------------------- get motivos bajas ------------------------
        Route::get('getMotivosBajas', 'inventario\motivosbajasController@get_motivosbajas');
        //------------------------------------- ultimo codigo motivos bajas ------------------------
        Route::get('ultimoCodeMotivosBajas', 'inventario\motivosbajasController@ultimo_code_motivosbajas');

        // ------------------------------------------- BAJAS ---------------------------------------------
        //------------------------------------- Añadir bajas ------------------------
        Route::post('addBajas', 'inventario\bajasController@add_bajas');
        //------------------------------------- modificar bajas ------------------------
        Route::post('updateBajas', 'inventario\bajasController@update_bajas');
        //------------------------------------- eliminar bajas ------------------------
        Route::post('deleteBajas', 'inventario\bajasController@delete_bajas');
        //------------------------------------- modificar bajas ------------------------
        Route::get('getBajas', 'inventario\bajasController@get_bajas');
        //------------------------------------- ultimo codigo bajas ------------------------
        Route::get('ultimoCodeBajas', 'inventario\bajasController@ultimo_code_bajas');

        // ------------------------------------------- MAESTRO ARTICULO ---------------------------------------------
        //------------------------------------- Añadir  ------------------------
        Route::post('addMaestroArticulo', 'inventario\maestro_articuloController@add_maestro_articulo');
        //------------------------------------- modificar  ------------------------
        Route::post('updateMaestroArticulo', 'inventario\maestro_articuloController@update_maestro_articulo');
        //------------------------------------- eliminar  ------------------------
        Route::post('deleteMaestroArticulo', 'inventario\maestro_articuloController@delete_maestro_articulo');
        //------------------------------------- get  ------------------------
        Route::get('getMaestroArticulo', 'inventario\maestro_articuloController@get_maestro_articulos');
        //------------------------------------- ultimo codigo  ------------------------
        Route::get('ultimoCodeMaestroArticulo', 'inventario\maestro_articuloController@ultimo_code_maestro_articulo');

        //---------------------------------------------- CHAT ------------------------------------------------
        Route::post('sendMensaje', 'chatController@send_mensaje');
        Route::post('sendMensajeFromChat', 'chatController@send_mensaje_from_chatbox');
        Route::get('getChats', 'chatController@get_chats');
        Route::get('getMensajes', 'chatController@get_mensajes');
        //---------------------------------------------- Catalogo ------------------------------------------------
        Route::post('addProducto', 'catalogoController@add_producto');
        Route::post('addPortada', 'catalogoController@add_portada');
        Route::post('addContraPortada', 'catalogoController@add_contraportada');

        });
    });
