<?php
use Illuminate\Support\Facades\Route;

// MIDDLEWARE PARA RESTRICCION DE ROLES

// INVITADO
Route::group( ['middleware'=> ['guest']], function() {
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
} );

// AUTENTICADOS
Route::group( ['middleware'=> ['auth']], function(){
    // NOTIFICACION
        Route::post( '/notification/get', 'NotificationController@get' );
    // DASHBOARD
        Route::get('/main', function () {
            return view('contenido.contenido');
        })->name('main');
        Route::get( '/dashboard', 'DashboardController' );
    // ALMACENERO
    Route::group( ['middleware'=> ['almacenero']], function(){
        // CATEGORIA
            Route::resource( '/categoria', 'CategoriaController' );
            Route::put( '/categoria/baja/id', 'CategoriaController@darBajaEstado' );
            Route::put( '/categoria/alta/id', 'CategoriaController@darAltaEstado' );
            Route::get( '/categorias', 'CategoriaController@listaCategorias' );
        // ARTICULO
            Route::resource( '/articulo', 'ArticuloController' );
            Route::put( '/articulo/baja/id', 'ArticuloController@darBajaEstado' );
            Route::put( '/articulo/alta/id', 'ArticuloController@darAltaEstado' );
            Route::get( '/articulo/buscar/filtro', 'ArticuloController@buscarArticulo' );
            Route::get( '/articulos', 'ArticuloController@getArticulos' );
            Route::get( '/articulos/stock', 'ArticuloController@buscarArticuloConStock' );
            Route::get( '/articulos/stock/venta', 'ArticuloController@buscarArticuloVenta' );
            Route::get( '/articulos/listar/pdf', 'ArticuloController@getArticulosPDF' );
        // PROVEEDOR - PERSONA
            Route::resource( '/proveedor', 'ProveedorController' );
            Route::get( '/proveedores', 'ProveedorController@getProveedores' );
        // INGRESO
            Route::resource('/ingreso', 'IngresoController');
            Route::put('/ingreso/baja/id', 'IngresoController@darBajaEstado');
            Route::get( '/ingreso/get/cabecera', 'IngresoController@getCabecera' );
            Route::get( '/ingreso/get/detalles',  'IngresoController@getDetalles' );
    } );
    // VENDEDOR
    Route::group( ['middleware'=> ['vendedor']], function(){
        // CLIENTE - PERSONA
            Route::resource( '/cliente', 'ClienteController' );
            Route::get( '/clientes', 'ClienteController@getClientes' );
        // VENTAS
            Route::resource('/venta', 'VentaController');
            Route::put('/venta/baja/id', 'VentaController@darBajaEstado');
            Route::get( '/venta/get/cabecera', 'VentaController@getCabecera' );
            Route::get( '/venta/get/detalles',  'VentaController@getDetalles' );
            Route::get('/venta/pdf/{id}', 'VentaController@getVentaPDF');

        // TURNO
        Route::resource( '/turno', 'TurnoController' );
        Route::put( '/turno/alta/id', 'TurnoController@darAltaEstado' );
        Route::get( '/turnos', 'TurnoController@listaTurnos' );     

        // DEPOSITO
        Route::resource( '/deposito', 'DepositoController' );
        Route::get( '/depositos', 'DepositoController@listaDepositos' );  
        Route::get( '/deposito/listar/pdf', 'DepositoController@getDepositosPDF' );            
    } );
    // ADMIN
    Route::group( ['middleware'=> ['administrador']], function(){
        // CATEGORIA
            Route::resource( '/categoria', 'CategoriaController' );
            Route::put( '/categoria/baja/id', 'CategoriaController@darBajaEstado' );
            Route::put( '/categoria/alta/id', 'CategoriaController@darAltaEstado' );
            Route::get( '/categorias', 'CategoriaController@listaCategorias' );
        // CLIENTE - PERSONA
            Route::resource( '/cliente', 'ClienteController' );
            Route::get( '/clientes', 'ClienteController@getClientes' );
        // ARTICULO
            Route::resource( '/articulo', 'ArticuloController' );
            Route::put( '/articulo/baja/id', 'ArticuloController@darBajaEstado' );
            Route::put( '/articulo/alta/id', 'ArticuloController@darAltaEstado' );
            Route::get( '/articulo/buscar/filtro', 'ArticuloController@buscarArticulo' );
            Route::get( '/articulos', 'ArticuloController@getArticulos' );
            Route::get( '/articulos/stock', 'ArticuloController@buscarArticuloConStock' );
            Route::get( '/articulos/stock/venta', 'ArticuloController@buscarArticuloVenta' );
            Route::get( '/articulos/listar/pdf', 'ArticuloController@getArticulosPDF' );
        // PROVEEDOR - PERSONA
            Route::resource( '/proveedor', 'ProveedorController' );
            Route::get( '/proveedores', 'ProveedorController@getProveedores' );
        // ROL
            Route::resource( '/rol', 'RolController' );
            Route::put( '/rol/baja/id', 'RolController@darBajaEstado' );
            Route::put( '/rol/alta/id', 'RolController@darAltaEstado' );        
        // USUARIOS
            Route::resource( '/user', 'UserController' );
            Route::put( '/user/baja/id', 'UserController@darBajaEstado' );
            Route::put( '/user/alta/id', 'UserController@darAltaEstado' );
            Route::get( '/roles', 'RolController@listaRoles' );
            Route::get( '/users', 'UserController@getUsuarios' );
        // INGRESO
            Route::resource('/ingreso', 'IngresoController');
            Route::put('/ingreso/baja/id', 'IngresoController@darBajaEstado');
            Route::get( '/ingreso/get/cabecera', 'IngresoController@getCabecera' );
            Route::get( '/ingreso/get/detalles',  'IngresoController@getDetalles' );
            Route::get( '/ingreso/listar/pdf', 'IngresoController@getIngresosPDF' );
        // VENTAS
            Route::resource('/venta', 'VentaController');
            Route::put('/venta/baja/id', 'VentaController@darBajaEstado');
            Route::get( '/venta/get/cabecera', 'VentaController@getCabecera' );
            Route::get( '/venta/get/detalles',  'VentaController@getDetalles' );
            Route::get('/venta/recibo/pdf/{id}', 'VentaController@getVentaReciboPDF');
            Route::get('/venta/factura/pdf/{id}', 'VentaController@getVentaFacturaPDF');
            Route::get( '/venta/listar/pdf', 'VentaController@getVentaListPDF' );
        // CREDITOS
            Route::resource('/credito', 'CreditoController');
            Route::put('/abono/baja/id', 'CreditoController@darBajaEstado');
            Route::get( '/credito/get/cabecera', 'CreditoController@getCabecera' );
            Route::get( '/credito/get/detalles',  'CreditoController@getDetalles' );
            Route::get('/credito/recibo/pdf/{id}', 'CreditoController@getVentaReciboPDF');
            Route::get('/credito/factura/pdf/{id}', 'CreditoController@getVentaFacturaPDF');
            Route::get( '/credito/listar/pdf', 'CreditoController@getVentaListPDF' );
            Route::get('/credito/get/abonos', 'CreditoController@getAbonos');

        // TALONARIO
        Route::resource( '/talonario', 'TalonarioController' );
        Route::put( '/talonario/baja/id', 'TalonarioController@darBajaEstado' );
        Route::put( '/talonario/alta/id', 'TalonarioController@darAltaEstado' );
        Route::get( '/talonarios', 'TalonarioController@listaCategorias' );

        // TURNO
        Route::resource( '/turno', 'TurnoController' );
        Route::put( '/turno/alta/id', 'TurnoController@darAltaEstado' );
        Route::get( '/turnos', 'TurnoController@listaTurnos' );     

        // DEPOSITO
        Route::resource( '/deposito', 'DepositoController' );
        Route::get( '/depositos', 'DepositoController@listaDepositos' );   
        Route::get( '/deposito/listar/pdf', 'DepositoController@getDepositosPDF' );           
    } );
    // LOGOUT
    Route::post('/logout', 'Auth\LoginController@logout');
} );
