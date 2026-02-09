<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('/login');
});

Auth::routes();
Route::group(['middleware' => 'web'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('users', App\Http\Controllers\UserController::class);
Route::resource('companies', App\Http\Controllers\CompanyController::class);
Route::resource('excelscompanies', App\Http\Controllers\excelscompanyController::class);
Route::resource('categorizacion_cts_balances', App\Http\Controllers\categorizacion_cts_balanceController::class);
Route::resource('clasificacion_cuenta_resuls', App\Http\Controllers\clasificacion_cuenta_resulController::class);
Route::resource('in_balances', App\Http\Controllers\in_balanceController::class);
Route::resource('in_presupuestos', App\Http\Controllers\in_presupuestosController::class);
Route::resource('in_resultados', App\Http\Controllers\in_resultadoController::class);
Route::resource('in_ventas', App\Http\Controllers\in_ventasController::class);
Route::resource('setup_analises', App\Http\Controllers\setup_analisisController::class);
Route::resource('sucursales', App\Http\Controllers\sucursalesController::class);
Route::resource('tipo_cambios', App\Http\Controllers\tipo_cambioController::class);
Route::resource('estudios', App\Http\Controllers\estudiosController::class);
Route::resource('estudios_usuarios', App\Http\Controllers\estudios_usuariosController::class);
Route::resource('grupo_economicos', App\Http\Controllers\grupo_economicosController::class);
Route::resource('grupo_economicos_empresas', App\Http\Controllers\grupo_economicos_empresasController::class);
Route::resource('usuario_grupoeconomicos', App\Http\Controllers\usuario_grupoeconomicoController::class);
Route::resource('bitacoras', App\Http\Controllers\bitacoraController::class);
Route::get('/bitacoras/{id}/grupo', [ App\Http\Controllers\bitacoraController::class, 'index2'])->name('bitacoras.index2');
Route::get('/bitacoras/{id}/EnviosEmail/{id2}', [ App\Http\Controllers\bitacoraController::class, 'EnviosEmail'])->name('bitacoras.EnviosEmail');
Route::get('/userCompany/{idCompany}', [ App\Http\Controllers\UserController::class, 'getUsersCompanies'])->name('users.getUsersCompanies');
Route::get('/bitacoras/{id}/grupo/create', [ App\Http\Controllers\bitacoraController::class, 'create2'])->name('bitacoras.create2');
Route::post('/bitacoras/{id}/grupo/store', [ App\Http\Controllers\bitacoraController::class, 'store'])->name('bitacoras.store');
Route::get('/bitacoras/{id}/grupo/{id2}/edit', [ App\Http\Controllers\bitacoraController::class, 'edit'])->name('bitacoras.edit');
Route::patch('/bitacoras/{id}/grupo/{id2}/update', [ App\Http\Controllers\bitacoraController::class, 'update'])->name('bitacoras.update');
Route::get('/bitacoras/{id}/grupo/{id2}/show', [ App\Http\Controllers\bitacoraController::class, 'show'])->name('bitacoras.show');
Route::delete('/bitacoras/{id}/grupo/{id2}/delete', [ App\Http\Controllers\bitacoraController::class, 'destroy'])->name('bitacoras.destroy');

Route::resource('controlcuentas', App\Http\Controllers\controlcuentasController::class);
Route::resource('bitacora_hitos', App\Http\Controllers\bitacora_hitosController::class);
Route::get('/bitacoras/{id}/grupo/{id2}/crearhito', [ App\Http\Controllers\bitacora_hitosController::class, 'create2'])->name('bitacora_hitos.create2');
Route::get('/bitacoras/{id}/grupo/{id2}/edithito/{id3}', [ App\Http\Controllers\bitacora_hitosController::class, 'edit'])->name('bitacora_hitos.edit');
Route::patch('/bitacoras/{id}/grupo/{id2}/savehito/{id3}', [ App\Http\Controllers\bitacora_hitosController::class, 'update'])->name('bitacora_hitos.update');
Route::post('/bitacoras/{id}/grupo/{id2}/storehito', [ App\Http\Controllers\bitacora_hitosController::class, 'store'])->name('bitacora_hitos.store');
Route::get('/bitacoras/{id}/grupo/{id2}/showhito/{id3}', [ App\Http\Controllers\bitacora_hitosController::class, 'show'])->name('bitacora_hitos.show');
Route::delete('/bitacoras/{id}/grupo/{id2}/delete/{id3}', [ App\Http\Controllers\bitacora_hitosController::class, 'destroy'])->name('bitacora_hitos.destroy');


Route::resource('bitacora_enviosemails', App\Http\Controllers\bitacora_enviosemailController::class);
Route::get('/bitacoras/{id}/grupo/{id2}/usuarioshito', [ App\Http\Controllers\bitacora_enviosemailController::class, 'create'])->name('bitacora_enviosemails.create');
Route::post('/bitacoras/{id}/grupo/{id2}/usuarioshitosave', [ App\Http\Controllers\bitacora_enviosemailController::class, 'store'])->name('bitacora_enviosemails.store');

Route::resource('sessiones', App\Http\Controllers\sessionesController::class);
Route::post('/tipo_cambios_globals/uploadfile', [ App\Http\Controllers\tipo_cambios_globalController::class, 'uploadfile'])->name('tipo_cambios_global.uploadfile');

Route::resource('tipo_cambios_globals', App\Http\Controllers\tipo_cambios_globalController::class);
Route::resource('compromiso_entregas', App\Http\Controllers\compromiso_entregaController::class);
Route::post('/compromiso_entregas/procesar-datos', [App\Http\Controllers\compromiso_entregaController::class, 'procesar'])->name('compromiso.registro');

});
Route::get('/getAllCompromisoEntregar', [App\Http\Controllers\compromiso_entregaController::class, 'getAllCompromisoEntregar']);
Route::get('documentos/descargar/{id}', [\App\Http\Controllers\documento_companiaController::class, 'download'])->name('documento_companias.download');
Route::resource('tipo_documentos', App\Http\Controllers\tipo_documentoController::class);
Route::resource('documento_companias', App\Http\Controllers\documento_companiaController::class);
Route::resource('bitacoras_envios_documentos', App\Http\Controllers\bitacoras_envios_documentoController::class);
