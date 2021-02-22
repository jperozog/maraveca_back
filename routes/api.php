<?php

use Illuminate\Http\Request;
Use App\Celdas;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\PassportController@login');
Route::post('register', 'API\PassportController@register');
Route::post('testme', function (Request $request) { dd($request->header()); });

Route::group(['middleware' => 'auth:api'], function(){
	Route::post('get-details', 'API\PassportController@getDetails');
});

Route::middleware('auth:api')->group(function() {
   // insert a new book
   Route::get('user', 'UsersController@index');
   Route::post('user', 'UsersController@store');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
//Route::match(['put', 'post', 'options'], 'api/...', 'Api\XController@method')->middleware('cors');

Route::post('tnotify', 'NotifyController@snotify');
Route::get('mensajes_morosos', 'NotifyController@mensajes_morosos');
Route::post('env_sms_morosos', 'NotifyController@env_msj_morosos');
Route::get('traerLista', 'NotifyController@traerHistoricoMensaje');


// rutas de los balances

Route::get('balance', 'BalanceClienteController@index');
Route::put('balance', 'BalanceClienteController@update');

Route::delete('balance', 'BalanceClienteController@delete');
Route::put('edit_balance/{id}', 'BalanceClienteController@edit_balance');
Route::put('edit_balance_config/{id}', 'BalanceClienteController@edit_balance_config');
Route::put('anular_balance_config/{id}', 'BalanceClienteController@anular_balance_config');
/*=====================================================================================================*/
Route::get('balance_in', 'BalanceClienteInController@index');
Route::put('balance_in', 'BalanceClienteInController@update');
Route::delete('balance_in', 'BalanceClienteInController@delete');
Route::put('edit_balance_in/{id}', 'BalanceClienteInController@edit_balance_in');
Route::put('edit_balance_in_config/{id}', 'BalanceClienteInController@edit_balance_in_config');
Route::put('anular_balance_in_config/{id}', 'BalanceClienteInController@anular_balance_in_config');
/*======================================================================================================*/

// rutas para las celdas

Route::get('celdas', 'CeldasController@index');
Route::get('celdas/{id}', 'CeldasController@show');
Route::post('celdas', 'CeldasController@store');
Route::put('celdas', 'CeldasController@update');
Route::delete('celdas/{id}', 'CeldasController@delete');

//rutas para los usuarios

Route::get('users', 'UsersController@index');
Route::get('dash', 'UsersController@dash');
Route::get('installer', 'UsersController@indexi');
Route::get('users/{id}/permission', 'UsersController@g_permissions');
Route::get('users/{id}', 'UsersController@show');
Route::delete('users/{id}/permission', 'UsersController@d_permissions');
Route::post('users/permission', 'UsersController@update_permissions');
Route::get('users/{id}/zona', 'UsersController@g_zona');
Route::post('users/{id}/zona', 'UsersController@p_zona');
Route::post('users/zona', 'UsersController@p_zona');
Route::post('users/login/{username}/{password}', 'UsersController@login');
Route::post('users', 'UsersController@store');
Route::put('users/{id}', 'UsersController@update');
Route::delete('users/{id}', 'UsersController@delete');


//rutas para los MK

Route::get('servidor', 'ServidoresController@index');
Route::get('servidor/{id}', 'ServidoresController@show');
Route::post('servidor', 'ServidoresController@store');
Route::put('servidor/{id}', 'ServidoresController@update');
Route::delete('servidor/{id}', 'ServidoresController@delete');
Route::get('test', 'PotencialesController@index');
//rutas para los AP

Route::get('aps', 'aps@index');
Route::get('aps/{id}', 'aps@show');
Route::post('aps', 'aps@store');
Route::put('aps', 'aps@update');
Route::delete('aps/{id}', 'aps@delete');

//rutas para los soportes e instalaciones

Route::get('soporte', 'SoportesController@indext');
Route::get('soportesz', 'SoportesController@indexz');
Route::get('soportesm', 'SoportesController@indexi');
Route::get('instalaciones', 'SoportesController@indexinstalls');
Route::get('soporte/{id}', 'SoportesController@show');
Route::post('soporte', 'SoportesController@store');
Route::post('soporte2', 'SoportesController@store1');
Route::post('ticketa', 'SoportesController@store3');
Route::put('ticketu/{id}', 'SoportesController@updateinstall');
Route::put('soporte/{id}', 'SoportesController@update');
Route::put('install/{id}', 'SoportesController@closeinstall');
Route::post('oinstall/{id}/', 'SoportesController@closeoinstall');
Route::delete('soporte/{id}', 'SoportesController@delete');
Route::get('verificar', 'SoportesController@verificar');
Route::get('show_ip', 'SoportesController@showip');
Route::get('show_tickets', 'SoportesController@showtickets');
Route::get('show_tickets_user/{id}', 'SoportesController@show_tickets_user');
Route::put('retirar_ip_mk/{id}', 'SoportesController@retirar_ip_mk');
Route::put('editar_install/{id}', 'SoportesController@editar_install');
Route::put('anular_install/{id}', 'SoportesController@anular_install');
//Route::put('tipo_soporte/{id}', 'SoportesController@anular_install');
//pagos desde perfil de cliente (digamosle monedero)

Route::get('pagoclient/{id}', 'BalanceClienteController@show');
Route::post('pagoclient', 'BalanceClienteController@store');
/*==================================================================================================================*/

Route::get('pagoclient_in/{id}', 'BalanceClienteInController@show');
Route::post('pagoclient_in', 'BalanceClienteInController@store');

/*==================================================================================================================*/
//Zonas
Route::get('zonas', 'ZonasController@index');
Route::post('zonas', 'ZonasController@store');
Route::put('zonas', 'ZonasController@update');


//inventarios
Route::get('inventarios', 'InventariosController@index');
Route::get('inventarios/{id}', 'InventariosController@show');
Route::post('inventarios', 'InventariosController@store');
Route::put('inventarios', 'InventariosController@update');
Route::get('PreloadInventarios', 'InventariosController@PreloadInventarios');
Route::get('PreloadTransferencias', 'InventariosController@PreloadTransferencias');
Route::get('equipo/{id}', 'InventariosController@equipo');
Route::post('transferdevice', 'TransferenciasEquiposController@store');
Route::put('transferdevice/{id}', 'TransferenciasEquiposController@update');
Route::get('equiposasignados', 'InventariosController@equiasig');


//tabla de historial de cerrados
Route::get('ticketh/{id}', 'TicketHistoryController@index');
Route::post('ticketh', 'TicketHistoryController@store');
Route::get('ticketp/{id}', 'TicketProblemsController@index');
Route::post('ticketp', 'TicketProblemsController@store');
Route::delete('ticketp/{id}', 'TicketProblemsController@delete');

//ruta para los equipos

Route::get('equipos', 'EquiposController@index');
Route::get('equipos/{id}', 'EquiposController@show');
Route::get('equit/{id}', 'EquiposController@equit');
Route::post('equipos', 'EquiposController@store');
Route::put('equipos/{id}', 'EquiposController@update');
Route::delete('equipos/{id}', 'EquiposController@delete');

//rutas para cuentas por cobrar y por pagar
Route::get('stat', 'StatController@index');
Route::get('statdl', 'StatController@indexdl');
Route::get('incidencia', 'StatController@show');
Route::get('installers', 'StatController@instaladores');
Route::get('installer/{id}', 'StatController@instalador');
Route::post('instpagos', 'InstpagController@store');
Route::post('oinstallpgo', 'OinstallpgoController@store');
Route::get('oinstallers', 'StatController@oinstaladores');
Route::get('oinstaller/{id}', 'StatController@oinstalador');
Route::get('user_comision', 'StatController@user_comision');
Route::get('fac_comision/{id}', 'StatController@fac_comision');
Route::get('fac_comision_montos/{id}', 'StatController@fac_comision_montos');
Route::post('report_pago', 'StatController@report_pago');
Route::get('history_pago/{id}', 'StatController@history_pago');

//rutas para los planes

Route::get('planes', 'PlanesController@index');
Route::get('planes/{id}', 'PlanesController@show');
Route::get('traerPlanes/{id}', 'PlanesController@traerPlanes');
Route::post('planes', 'PlanesController@store');
Route::put('planes/{id}', 'PlanesController@update');
Route::post('planes/update', 'PlanesController@updatePrice');
Route::delete('planes/{id}', 'PlanesController@delete');

//rutas para los servicios

Route::get('servicios', 'ServiciosController@index');
Route::get('servicios/{id}', 'ServiciosController@show');
Route::get('servicioscli/{id}', 'ServiciosController@serv_cliente');
Route::post('servicios', 'ServiciosController@store');
Route::put('servicios/{id}', 'ServiciosController@update');
Route::delete('servicios/{id}', 'ServiciosController@delete');
Route::get('add_preload', 'ServiciosController@add_preload');
Route::get('servicios_v/{ip_srv}', 'ServiciosController@solicitar_ip'); //datos para validar ip
Route::get('servicios_s/{serial}', 'ServiciosController@solicitar_serial'); // datos para validar serial del equipo
Route::post('Prog_corte', 'ServiciosController@prog_corte');
Route::put('cortes_prog/anular/{id}', 'ServiciosController@anular_corte_prog');
//rutas para los clientes

Route::get('clientes', 'ClientesController@index');
Route::get('clientes1/{id}', 'ClientesController@index1');
Route::get('clientes/{id}', 'ClientesController@show');
Route::get('clientover/{id}', 'ClientesController@overview');
Route::post('clientes', 'ClientesController@store');
Route::post('clientesn', 'ClientesController@notificar');
Route::put('clientes/{id}', 'ClientesController@update');
Route::delete('clientes/{id}', 'ClientesController@delete');
Route::put('tplancl/{id}', 'ClientesController@act_tipoplan');
//rutas para los clientes Potenciales

Route::get('pclientes', 'PclientesController@index');
Route::get('pclientes/{id}', 'PclientesController@show');
Route::post('pclientes', 'PclientesController@store');
Route::post('pclienttc', 'PclientesController@pclientstc');
Route::put('pclientes/{id}', 'PclientesController@update');
Route::delete('pclientes/{id}', 'PclientesController@delete');

//presupuestos
Route::post('presupuesto', 'PresupuestoController@sendPresupuesto');
Route::get('presupuesto', 'PresupuestoController@show');

//facibilidades

Route::get('factibi', 'FactibilidadesController@index');
Route::get('factibi/{id}', 'FactibilidadesController@show');
Route::get('factible/{id}', 'FactibilidadesController@showr');
Route::get('factibil/{id}', 'FactibilidadesController@show2');
Route::post('factibi', 'FactibilidadesController@store');
Route::put('factibi/{id}', 'FactibilidadesController@update');
Route::put('factibi_act/{id}', 'FactibilidadesController@update1');
Route::delete('factibi/{id}', 'FactibilidadesController@delete');

//rutas para las notificaciones via SMS

Route::put('sms', 'smsController@update');
Route::post('sms', 'smsController@update');
Route::get('sms', 'smsController@update');

//rutas para facturacion

//facturas
Route::get('facturas1/{id}', 'FacControlController@index');
Route::get('facturas/{id}', 'FacControlController@show');
Route::get('facturacli/{id}', 'FacControlController@fac_cliente');
Route::post('facturas', 'FacControlController@store');
Route::post('factura', 'FacControlController@generate');
Route::post('notificar', 'FacControlController@notificar');
Route::put('facturas/{id}', 'FacControlController@update');
Route::put('facturas/anular/{id}', 'FacControlController@anular');
Route::put('facturas_prog/anular/{id}', 'FacControlController@anular_prog');
Route::post('facturas/{id}', 'FacControlController@facmail');
Route::post('addadic', 'FacAdicController@store');
Route::delete('facturas/{id}', 'FacControlController@delete');
Route::delete('fac_pago/{id}', 'FacPagoController@delete');
Route::delete('fac_product/{id}', 'FacProductController@delete');
Route::post('price_fac/update', 'FacControlController@update_pricefac');
Route::post('factura_blanco', 'FacControlController@generate_fac_blanco');


//fac_details

Route::get('facdet/{id}', 'FacDetController@show');
Route::post('facdet', 'FacDetController@store');
Route::delete('facdet/{id}', 'FacDetController@delete');

//fac_prod

Route::get('facprod/{id}', 'FacProductController@index');
Route::post('facprod', 'FacProductController@store');
Route::delete('facprod/{id}', 'FacProductController@delete');
Route::put('facprod/{id}', 'FacProductController@update');
Route::put('actualizar_precio/{id}', 'FacProductController@actualizar_precio');

//fac_pagos

Route::get('facpag/{id}', 'FacPagoController@index');
Route::post('facpag', 'FacPagoController@store');
Route::delete('facpag/{id}', 'FacPagoController@delete');
Route::put('facpag/{id}', 'FacPagoController@update');


//lista de clientes activos

Route::get('activos', 'ClientesController@activos');

Route::get('pruebasnmp', 'SNMPController@get');

//pagos_comisiones
Route::get('pago_comisiones', 'pagos_comisionesController@index');
Route::get('pago_comisiones_user/{id}', 'pagos_comisionesController@comision_user');

//configuracion
Route::get('Configuraciones', 'ConfiguracionController@index');
Route::put('Configuraciones', 'ConfiguracionController@update');
Route::get('balances', 'ConfiguracionController@balances');

//historico configuraciones
Route::get('history_config', 'historico_config_adminController@index');



//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//
//JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE////JOSE//


//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE
//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE

Route::get('cliente/{id}','ClienteController@traerCliente');
Route::post('editarDatosClientes','ClienteController@editarDatosClientes');
Route::get('estados','ClienteController@traerEstados');
Route::post('municipios','ClienteController@traerMunicipios');
Route::post('traerCMunicipio','ClienteController@traerCMunicipios');
Route::post('parroquias','ClienteController@traerParroquias');
Route::post('ciudades','ClienteController@traerCiudades');
Route::post('guardarCliente','ClienteController@store');
Route::get('datosEstado/{id}','ClienteController@datosEstado');

//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE
//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE//CLIENTE

//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL
//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL

Route::get('traerPClientes/{id}','ClientePotencialController@index');
Route::get('traerFactibilidadPCliente/{id}','ClientePotencialController@traerFactibilidadPCliente');
Route::post('guardarPcliente','ClientePotencialController@store');
Route::post('editarPcliente','ClientePotencialController@editarPcliente');

//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL
//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL//CLIENTEPOTENCIAL


//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//
//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//

//Articulos

Route::get('articulos','ArticulosController@index');
Route::get('articulo/{id}','ArticulosController@traerDatoEquipo');
Route::post('articulosCategorias','ArticulosController@articulosCategorias');
Route::post('articulosCategorias2','ArticulosController@articulosCategorias2');
Route::post('articulosCategorias3','ArticulosController@articulosCategorias3');
Route::get('articulos/disponibles/{id}','ArticulosController@traerDisponibles');
Route::get('articulos/enProceso/{id}','ArticulosController@traerEnProceso');
Route::get('articulos/noDisponibles/{id}','ArticulosController@traerNoDisponibles');
Route::get('busquedaEquipo/{zona}/{modelo}/{id}',"ArticulosController@busquedaEquipo");
Route::post('articulos','ArticulosController@store');
Route::post('articulosCola','ArticulosController@storeCola');
Route::put('transferirArchivos/{id}','ArticulosController@Transferir');
Route::put('articulos/{id}','ArticulosController@update');
Route::delete('articulos/{id}','ArticulosController@destroy');
Route::get('consumibles/{id}','ArticulosController@traerConsumibles');
Route::get('equiposConsumibles','ArticulosController@traerEquiposConsumibles');
Route::post('agregarConsumible','ArticulosController@agregarEquiposConsumibles');
Route::put('modificarConsumible','ArticulosController@modificarEquiposConsumibles');
Route::post('eliminarConsumible','ArticulosController@eliminarEquiposConsumibles');
Route::get('traerGrupal','ArticulosController@traerGrupal');
Route::post('agregarGrupal','ArticulosController@agregarGrupal');


//ruta para los equipos

Route::get('equipos2', 'Equipos2Controller@index');
Route::get('equiposInstalacion/{id}', 'Equipos2Controller@traerEquiposInstalacion');
Route::get('equipos2/{id}', 'Equipos2Controller@traerDatosEquipo');
Route::get('tiposEquipos', 'Equipos2Controller@traerTiposEquipos');
Route::post('agregarEquipo2', 'Equipos2Controller@agregarEquipo2');
Route::put('editarEquipo', 'Equipos2Controller@editarEquipo');
Route::post('agregarCategoria', 'Equipos2Controller@agregarCategoria');
Route::get('equipos/{id}', 'EquiposController@show');
Route::get('equit/{id}', 'EquiposController@equit');
Route::post('equipos', 'EquiposController@store');
Route::put('equipos/{id}', 'EquiposController@update');
Route::delete('equipos/{id}', 'EquiposController@delete');


//zonas2

Route::get('zonas2Permisos','Zonas2Controller@index');
Route::get('zonas2','Zonas2Controller@index2');
Route::get('traerHistorialInventario','Zonas2Controller@traerHistorial');
Route::get('tiposCategorias','Zonas2Controller@traerCategorias');
Route::get('tiposConsumibles','Zonas2Controller@traerConsumibles');
Route::post('zonas2','Zonas2Controller@store');
Route::get('zonas2/{id}','Zonas2Controller@traerEquipos1');
Route::get('zonas2/{id}/{id2}','Zonas2Controller@traerEquipos');
Route::get('zonas2Asignados/{id}/{id2}','Zonas2Controller@traerEquiposAsignados');
Route::get('zonas2Vendidos/{id}/{id2}','Zonas2Controller@traerEquiposVendidos');
Route::delete('zonas2/{id}','Zonas2Controller@destroy');
Route::post('chequearConsumible','Zonas2Controller@chequearConsumible');


//transferencias de equipos
Route::get("transferencia/{id}", 'TransferenciaEquiposController@index');
Route::get("autorizar/{id}/{filtro}", 'TransferenciaEquiposController@autorizar');
Route::get("transferencias/{id}/{filtro}", 'TransferenciaEquiposController@obtenerData');
Route::get("transferenciasE/{id}", 'TransferenciaEquiposController@traerEmisor');
Route::get("transferenciasR/{id}", 'TransferenciaEquiposController@traerReceptor');
Route::get("transferenciasRes/{id}", 'TransferenciaEquiposController@traerResponsable');
Route::get("transferenciasCon/{id}", 'TransferenciaEquiposController@traerConfirmante');
Route::post("aceptarTransferir", 'TransferenciaEquiposController@aceptarTransferir');
Route::post("aceptarTransferirDetalles", 'TransferenciaEquiposController@aceptarTransferirDetalles');
Route::post("aceptarTransferirDetalles2", 'TransferenciaEquiposController@aceptarTransferirDetalles2');
Route::get("traerComentario/{id}", 'TransferenciaEquiposController@traerComentario');
Route::post("negarTransferir", 'TransferenciaEquiposController@negarTransferir');
Route::get("instaladores", 'TransferenciaEquiposController@traerInstalador');
Route::get("ultimaTransferencia", 'TransferenciaEquiposController@traerUltimaTransferencia');
Route::post("traspasoEquipo", 'TransferenciaEquiposController@traspasoEquipo');
Route::post("modificarSedesTranferencia", 'TransferenciaEquiposController@modificarSedesTranferencia');
Route::post("aggEquiposTranferencia", 'TransferenciaEquiposController@aggEquiposTranferencia');
Route::post("delEquiposTranferencia", 'TransferenciaEquiposController@delEquiposTranferencia');
Route::post("modificarOrdenTranferencia", 'TransferenciaEquiposController@modificarOrdenTranferencia');


Route::get("traerDatosCajas/{id}", 'TransferenciaEquiposController@datosCajas');
Route::get("traerEquiposCajas/{id}", 'TransferenciaEquiposController@equiposCajas');

//Ordenes de traslado
Route::post("aggOrdenTraslado", 'OrdenTrasladoController@store');
Route::get("ordenTraslado/{id}/{filtro}", 'OrdenTrasladoController@traerDatos');
Route::get("PdfTraslado/{id}", 'OrdenTrasladoController@traerDatosTraslado');
Route::get("ordenTraslado", 'OrdenTrasladoController@traerOrdenTraslado');
Route::get("datosChofer/{id}", 'OrdenTrasladoController@traerDatosChofer');
Route::get("traerTraslados", 'OrdenTrasladoController@traerTraslados');
Route::get("datosConductores", 'OrdenTrasladoController@traerDatosConductores');
Route::get("datosVehiculos", 'OrdenTrasladoController@traerDatosVehiculos');
Route::post("agregarVehiculo", 'OrdenTrasladoController@agregarVehiculo');
Route::post("realizarTraslado", 'OrdenTrasladoController@realizarTraslado');

//enviar
Route::get("correo",'CorreosController@index');


//Venta Equipos
Route::get('traerData','VentaEquiposController@index');
Route::get('traerData/{id}','VentaEquiposController@traerDataDetalles');
Route::get('buscarClienteVenta/{data}','VentaEquiposController@traerClientes');
Route::get('practica2/{data}','VentaEquiposController@traerClientesP');
Route::get('traerSede','VentaEquiposController@traerSede');
Route::get('traerEquipo','VentaEquiposController@traerEquipo');
Route::post('traerArticulos','VentaEquiposController@traerArticulos');
Route::post('guardarData','VentaEquiposController@store');


//infraestructura
Route::get('traerInfraestructura','InsfraestructuraController@index');
Route::get('traerZonaPermisos','InsfraestructuraController@traerZonasPermisos');
Route::get('traerZona','InsfraestructuraController@traerZonas');
Route::post('traerEquipos','InsfraestructuraController@traerEquipos');
Route::post('traerDisponibles','InsfraestructuraController@traerDisponibles');
Route::post('guardarInfra','InsfraestructuraController@guardarInfra');
Route::post('agregarIncidencia','InsfraestructuraController@agregarIncidencia');

//buscador
Route::post("buscarSerial","BuscardorController@buscador");
Route::post("masDetalles","BuscardorController@masDetalles");

//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//
//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//INVENTARIO//

//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//
//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//

//Zonas Administrativas
Route::get('zonasAministrativas','ZonasAdministrativasController@index');
Route::get('datosZona/{id}','ZonasAdministrativasController@traerDatosZona');
Route::get('busqueda/{id}/{zona}','ZonasAdministrativasController@busqueda');
Route::get('datosUser/{id}','ZonasAdministrativasController@traerDatosUser');
Route::get('cantidadEfectivo/{id}','ZonasAdministrativasController@traerContadorEfectivo');
Route::get('cantidadTransferencia/{id}','ZonasAdministrativasController@traerContadorTransferencia');
Route::get('cantidadZelle/{id}','ZonasAdministrativasController@traerContadorZelle');
Route::get('traerUsers/{id}','ZonasAdministrativasController@traerUsersAdministrativos');
Route::get('cantidadEfectivoUser/{id}','ZonasAdministrativasController@traerContadorEfectivoUser');
Route::get('cantidadTransferenciaUser/{id}','ZonasAdministrativasController@traerContadorTransferenciaUser');
Route::get('cantidadZelleUser/{id}','ZonasAdministrativasController@traerContadorZelleUser');
Route::post('cierreCaja/','ZonasAdministrativasController@hacerCierreCaja');
Route::get('ultimoCierre','ZonasAdministrativasController@traerUltimoCierre');


//Pagos & Cierre Caja
Route::get('traerMetodos','RegistroPagosController@traerMetodos');
Route::get('traerTaza','RegistroPagosController@traerTaza');
Route::get('conversion','RegistroPagosController@conversion');
Route::get('cierresPendientes','RegistroPagosController@traerCierresPendientes');
Route::get('datosCierrePendiente/{id}','RegistroPagosController@traerDatosCierrePendiente');
Route::get('efectivoCierre/{id}','RegistroPagosController@traerEfectivoCierre');
Route::get('nacionalesCierre/{id}','RegistroPagosController@traerNacionalesCierre');
Route::get('zelleCierre/{id}','RegistroPagosController@traerZelleCierre');
Route::post('registrarPago','RegistroPagosController@store');
Route::post('editarPago','RegistroPagosController@editarPago');
Route::post('confimarCierre','RegistroPagosController@confirmarCierre');
Route::post('cancelarCierre','RegistroPagosController@cancelarCierre');



//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//
//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//ADMINISTRATIVO//


//PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES//
//PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES//

Route::get('instalaciones/{id}/{mes}/{anio}','PagoComisionesController@traerInstalaciones');
Route::get('instalacionesPendientes/{id}/{mes}/{anio}','PagoComisionesController@traerInstalacionesPendientes');
Route::get('busqueda/{id}/{mes}/{anio}/{dato}','PagoComisionesController@busqueda');
Route::post('guardarComision/','PagoComisionesController@guardarComision');
Route::get('pagosRealizadosComisionesDl/{id}/{mes}/{anio}','PagoComisionesController@pagosRealizadosComisionesDl');
Route::get('pagosRealizadosComisionesBs/{id}/{mes}/{anio}','PagoComisionesController@pagosRealizadosComisionesBs');
Route::get('pagosRealizadosRecientesComisionesBs/{id}/{mes}/{anio}','PagoComisionesController@pagosRealizadosRecientesComisionesBs');
Route::get('pagosRealizadosRecientesComisionesDl/{id}/{mes}/{anio}','PagoComisionesController@pagosRealizadosRecientesComisionesDl');
Route::get('listaPagosComisiones/{id}/{mes}/{anio}','PagoComisionesController@traerListaPagosComisiones');


//PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES//
//PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES////PAGO-COMISIONES//



//SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE//
//SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE//

//tickets && Averias && reposision de equipo
Route::get('tickets','TicketsController@index');
Route::get('ticketsActivos','TicketsController@ticketsActivos');
Route::get('tickets/{id}','TicketsController@busqueda');
Route::post('guardarTicket','TicketsController@store');
Route::post('detallesTicket','TicketsController@detallesTicket');
Route::post('aggComentarioTicket','TicketsController@aggComentarioTicket');
Route::post('guardarAveria','TicketsController@guardarAveria');
Route::post('detallesAveria','TicketsController@detallesAveria');
Route::post('aggComentarioAveria','TicketsController@aggComentarioAveria');
Route::post('guardarReposicion','TicketsController@guardarReposicion');
Route::post('detallesReposicion','TicketsController@detallesReposicion');
Route::post('cerrarReposicion','TicketsController@cerrarReposicion');

//Instalaciones && Migraciones && Mudanzas
Route::post('traerInstalaciones','InstalacionesController@index');
Route::get('ips','InstalacionesController@ips');
Route::get('instalacionesActivas', 'InstalacionesController@InstalacionesActivas');
Route::post('instalaciones', 'InstalacionesController@store');
Route::post('editarInstalacion', 'InstalacionesController@editarInstalacion');
Route::post('anularInstalacion', 'InstalacionesController@anularInstalacion');
Route::get('datosInstalacion/{id}', 'InstalacionesController@datosInstalacion');
Route::get('practica/{id}','InstalacionesController@traerClientes');
Route::get('listaip/{id}','InstalacionesController@listaIp');
Route::post('BusquedaIpMk','InstalacionesController@listaIp2');
Route::get('instalaciones/{id}','TicketsController@busqueda');
Route::post('cerrarInstalacion/{id}','InstalacionesController@cerrarInstalacion');
Route::post('traerMigraciones','InstalacionesController@traerMigraciones');
Route::post('guardarMigracion', 'InstalacionesController@guardarMigracion');
Route::get('datosMigracion/{id}', 'InstalacionesController@datosMigracion');
Route::post('cerrarMigracion/{id}','InstalacionesController@cerrarMigracion');
Route::post('traerMudanzas','InstalacionesController@traerMudanzas');
Route::get('datosMudanza/{id}', 'InstalacionesController@datosMudanza');
Route::post('guardarMudanza', 'InstalacionesController@guardarMudanza');
Route::post('cerrarMudanza/{id}','InstalacionesController@cerrarMudanza');


//SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE//
//SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE////SOPORTE//


//PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS//
//PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS//


Route::get('procesoFacturacion','ProcesosController@index');
Route::get('datosFacturacion','ProcesosController@traerDatosFacturacion');
Route::get('procesoCorreos','ProcesosController@EnviarCorreosFacturacion');



//PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS//
//PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS////PROCESOS//


//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO
//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO

Route::get('traerPromociones','FacPromoController@index');
Route::get('traerDatosCliente/{id}','FacPromoController@traerDatosCliente');
Route::get('traerPlanesPromo/{id}','FacPromoController@traerPlanesPromo');
Route::post('guardarDataPromo','FacPromoController@store');
Route::get('verificarPromo/{id}','FacPromoController@verificarPromo');



//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO
//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO//FAC_PROMO


//FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL
//FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL

Route::post("ComprobarAnulacion","FacControlController@ComprobarAnulacion");

//FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL
//FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL////FAC_CONTROL


//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS
//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS

Route::get('traerAlarmas','AlarmasController@index');
Route::get('traerDatosMk','AlarmasController@traerDatosMk');
Route::get('traerDatosCeldas','AlarmasController@traerDatosCeldas');
Route::get('traerDatosAps','AlarmasController@traerDatosAps');
Route::post('guardarAlarma','AlarmasController@guardarAlarma');
Route::get('notificacionAlarma','AlarmasController@notificacion');
Route::post('cambiarStatusP','AlarmasController@cambiarStatusP');
Route::post('cambiarStatusN','AlarmasController@cambiarStatusN');


//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS
//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS//ALARMAS

//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES
//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES
Route::get('factibilidad/{id}', 'FactibilidadController@index');
Route::get('factibilidad1/{id}', 'FactibilidadController@index2');
Route::get('factibilidades', 'FactibilidadController@traerFactibilidades');
Route::get('verificarFac/{id}','FactibilidadController@verificarFac');
Route::get('equiposFact','FactibilidadController@traerEquipos');
Route::get('celdasFact','FactibilidadController@traerCeldas');
Route::post('guardarNuevaFac','FactibilidadController@guardarNuevaFac');
Route::post('guardarFac','FactibilidadController@guardarFac');
Route::post('editarFac','FactibilidadController@editarFac');

//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES
//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES//FACTIBILIDADES

//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD
//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD

Route::get('datosGraficaClientesActivos/','DashboardController@traerDatosGraficaClientesActivos');
Route::get('datosGraficaClientesInactivos/','DashboardController@traerDatosGraficaClientesInactivos');
Route::get('datosGraficaClientesZonas/','DashboardController@traerDatosGraficaClientesZonas');
Route::get('datosGraficaCuentasDl/','DashboardController@traerDatosGraficaCuentasDl');
Route::get('datosGraficaCuentasBs/','DashboardController@traerDatosGraficaCuentasBs');
Route::get('traerDatosGraficaServicio/','DashboardController@traerDatosGraficaServicio');
Route::get('datosGraficaTickets','DashboardController@index');
Route::get('datosGraficaFactibilidades','DashboardController@traerDatosGraficaFactibilidades');
Route::get('datosGraficaInstalaciones','DashboardController@traerDatosGraficaInstalaciones');
Route::get('datosGraficaOperaciones','DashboardController@traerDatosGraficaOperaciones');


//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD
//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD//DASHBOARD

//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS
//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS
Route::get("Servicios/{id}","ServicioController@index");
Route::post("editarServicio","ServicioController@editarServicio");
Route::post("guardarServicio","ServicioController@guardarServicio");
Route::post("activarServicio","ServicioController@activarServicio");
Route::get("servicioCliente/{id}","ServicioController@servicioCliente");
Route::post("servicioIndividual/","ServicioController@servicioIndividual");
Route::get("usuariosComision","ServicioController@usuariosComision");
Route::post("generarCompromisoServicio","ServicioController@generarCompromisoServicio");
Route::post("verificarCompromisoServicio","ServicioController@verificarCompromisoServicio");
Route::post("EliminarCompromisoServicio","ServicioController@EliminarCompromisoServicio");
Route::post("EditarCompromisoServicio","ServicioController@EditarCompromisoServicio");
Route::post("GenerarFacturaPro","ServicioController@GenerarFacturaPro");

//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS
//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS//SERVICIOS

//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO
//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO

Route::get("notasCredito/{id}","NotasCreditoController@traerNotasDeCredito");
Route::get("productosNota/{id}","NotasCreditoController@traerProductosNota");
Route::post("notaCredito","NotasCreditoController@guardarNota");
Route::post("guardarPagoNota","NotasCreditoController@guardarPagoNota");

//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO
//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO//NOTASCREDITO

//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES
//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES
Route::get("promociones","PromocionesController@index");
Route::post("guardarPromocion","PromocionesController@guardarPromocion");
//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES
//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES//PROMOCIONES

//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES
//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES
Route::get("traerVendedores","ComisionesVendedoresController@traerVendedores");
Route::post("traerComisionesVendedor","ComisionesVendedoresController@traerComisionesVendedor");
Route::post("realizarPagoComisionVendedor","ComisionesVendedoresController@realizarPagoComisionVendedor");
Route::post("traerPagosComisionesVendedor","ComisionesVendedoresController@traerPagosComisionesVendedor");

//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES
//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES//COMISIONES-VENDEDORES

//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES
//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES
Route::get("traerInstaladores","ComisionesInstaladoresController@traerInstaladores");
Route::post("traerComisionesInstalador","ComisionesInstaladoresController@traerComisionesInstalador");
Route::post("guardarCuotaInstalador","ComisionesInstaladoresController@guardarCuotaInstalador");

//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES
//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES//COMISIONES-INSTALADORES


//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS
//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS
Route::get("traerUsuarios","UsuarioController@index");
Route::get("traerHistorialUsuario/{id}","UsuarioController@traerHistorialUsuario");
Route::post("actualizarPermisosMk","UsuarioController@actualizarPermisosMk");
Route::get("traerPermisoMK/{id}","UsuarioController@traerPermisoMK");

//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS
//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS//USUARIOS

//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
Route::get("traerOlts","OltController@index");
Route::post('Olt', 'OltController@store');
Route::post('editarOlt', 'OltController@editarOlt');
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT

//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME
//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME
Route::get("traerMangasEmpalme","MangaEmpalmeController@index");
Route::get("puertosOlt/{id}","MangaEmpalmeController@traerPuertosDisponibles");
Route::post('mangaEmpalme', 'MangaEmpalmeController@store');
Route::post('editarManga', 'MangaEmpalmeController@editarManga');
//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME
//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME//MNAGAEMPLAME


//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
Route::get("traerCajas","CajaDistribucionController@index");
Route::post('cajaDistribucion', 'CajaDistribucionController@store');
Route::post('editarCaja', 'CajaDistribucionController@editarCaja');
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT
//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT//OLT

//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES
//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES
Route::post("traerCuentas","CuentasIncobrablesController@index");
Route::get("traerClientesExonerados","CuentasIncobrablesController@traerClientesExonerados");
Route::get("datosGraficaCuentasIncobrables/","CuentasIncobrablesController@graficaCuentasIncobrables");
Route::get("datosGraficaCuentasExonerados/","CuentasIncobrablesController@graficaCuentasExonerados");

//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES
//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES//CUENTASINCOBRABLES

//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS
//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS
Route::get("descuentos","DescuentosController@index");
Route::post("guardarDescuento","DescuentosController@guardarDescuento");
Route::post("aprobarDescuento","DescuentosController@aprobarDescuento");
Route::post("cancelarDescuento","DescuentosController@cancelarDescuento");

//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS
//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS//DESCUENTOS

//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS
//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS
Route::post("traerVentas","VentasController@index");
Route::post("guardarVenta","VentasController@store");
Route::post("guardarPromoVenta","VentasController@guardarPromoVenta");
Route::post("guardarTipoVenta","VentasController@guardarTipoVenta");
Route::post("guardaVentaInstalacion","VentasController@guardaVentaInstalacion");
//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS
//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS//VENTAS
