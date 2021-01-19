<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('index.php/login');
});

//Auth::routes();
//Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Route::get('/home', 'HomeController@index')->name('home');


    Route::get('login', 'ClientController@login')->name('login');
    Route::post('loginprocess', 'ClientController@loginprocess')->name('loginprocess');
    Route::get('clientesover', 'ClientController@overview')->middleware('checkcliente');
    Route::post('logout', 'ClientController@logout')->name('logout');
    Route::get('register', 'ClientController@register')->name('register');
    Route::post('register1', 'ClientController@register1')->name('register1');
    Route::post('emailclient', 'ClientController@emailclient')->name('emailclient');
    Route::get('registro/{id}{password}', 'ClientController@changepassword')->name('registro');
    Route::post('changepassword', 'ClientController@registerf')->name('changepassword');
    Route::post('clientesoverfac', 'ClientController@facturacionc')->name('clientesoverfac');
    Route::post('reportarpago', 'ClientController@reportarpago')->name('reportarpago');
    Route::post('downloadPDF', 'ClientController@downloadPDF')->name('downloadPDF');

    Route::get('chpassword', 'ClientController@chpassword')->name('chpassword');
    Route::post('password1', 'ClientController@password1')->name('password1');
    Route::post('restablecer', 'ClientController@restablecer')->name('restablecer');
    Route::get('restablecerpass/{id}{password}', 'ClientController@restablecerpass')->name('restablecerpass');
    Route::post('restpassword', 'ClientController@restpassword')->name('restpassword');
    Route::get('chpassword', 'ClientController@chpassword')->name('chpassword');
    Route::get('monedalocal', 'ClientController@formreportbs')->name('monedalocal');// ruta para el formulario de reporte de pago, para planes en bs 6/8/19
    Route::get('monedain', 'ClientController@formreportdl')->name('monedain'); //ruta para el formulario de reporte de pago, para planes en moneda internacional 6/8/19

    // Route::get('chpassword', function(){
    //   return view('password.password');
    // });
