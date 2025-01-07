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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();
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