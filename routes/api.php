<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(App\Http\Controllers\API\AuthController::class)->group(function () {
    Route::prefix('auth')->group(function () {
    Route::post('/', 'login');
    Route::get('/logout', 'logout');
    Route::post('/forgetpassword', 'forgetpassword');
    Route::get('/checkforget/{data}', 'checkforget');
    Route::post('/updateDataInfo', 'updateDataInfo');
    Route::post('/changePassword', 'changePassword');
    
    });
});
Route::controller(App\Http\Controllers\API\in_ventasAPIController::class)->group(function () {
    Route::prefix('in_ventas')->group(function () {
    Route::get('/{year}/{month}/{sucursal}', 'showDataGroup');
    Route::get('/full/{year}/{month}/{sucursal}', 'showDataFull');
    Route::get('/{year}/{month}/{yearan}/{monthan}/{sucursal}', 'showDataAnalisisdeVentas');
    Route::get('/desdehasta/{desdeanalisis}/{hastaanalisis}/{desdecomparativo}/{hastacomparativo}/{sucursal}', 'showDataAnalisisdeVentasDesdeHasta');

    });
});
Route::controller(App\Http\Controllers\API\in_balanceAPIController::class)->group(function () {
    Route::prefix('in_balances')->group(function () {
    Route::get('EstadoSitacion/{year}/{month}/{yearCierre}/{monthCierre}/{sucursal}', 'showEstadoSitacionPatrim');
    Route::get('showDataEvolutivo/{year}/{month}/{sucursal}', 'showDataEvolutivo');
    Route::get('showEstadoDash/{year}/{month}/{sucursal}', 'showEstadoDash');
    
    });
});
Route::controller(App\Http\Controllers\API\in_resultadoAPIController::class)->group(function () {
    Route::prefix('in_resultados')->group(function () {
        
    Route::post('/datoPostModal', 'post_modal');
    Route::get('/showinformedesdeyhasta/{yeardesde}/{monthdesde}/{yearhasta}/{monthhasta}/{sucursal}', 'showinformedesdeyhasta');
    Route::get('/fullYear/{year}/{sucursal}', 'showDataFullYear');
    Route::get('/{year}/{month}/{sucursal}', 'showDataGroup');
    Route::get('/full/{year}/{month}/{sucursal}', 'showDataFull');
    Route::get('/showInformER/{year}/{month}/{sucursal}', 'showInformER');
    Route::get('/showInformERFiscal/{year}/{month}/{yearfiscal}/{monthfiscal}/{sucursal}', 'showInformERFiscal');
    Route::get('/showDataEvolutivo/{year}/{month}/{sucursal}', 'showDataErEvolutivo');
    Route::get('/showDataLegal/{year}/{month}/{sucursal}', 'showDataLegal');
    Route::get('/showDataPuntoEquilibrioEvolutivo/{year}/{month}/{sucursal}', 'showDataPuntoEquilibrioEvolutivo');
    Route::get('/ebitda/{year}/{month}/{sucursal}', 'showDataGroupEbitda');
    Route::get('/er/{year}/{month}/{sucursal}', 'showDataGroupEstadosResult');
    Route::get('/ebitda7Monthbefore/{sucursal}', 'showDataGroupEbitda7month');
    Route::get('/showEstadisticas/{sucursal}', 'showEstadisticas');
    
    });
});
Route::controller(App\Http\Controllers\API\in_presupuestosAPIController::class)->group(function () {
    Route::prefix('in_presupuestos')->group(function () {
        Route::post('/datoPostModal', 'post_modal');
    Route::get('/showDataLegal/{year}/{month}/{sucursal}', 'showDataLegal');
    Route::get('/showinformedesdeyhasta/{yeardesde}/{monthdesde}/{yearhasta}/{monthhasta}/{sucursal}', 'showinformedesdeyhasta');
    Route::get('/{year}/{month}/{sucursal}', 'showDataGroup');
    Route::get('/full/{year}/{month}/{sucursal}', 'showDataFull');
    Route::get('/showInformER/{year}/{month}/{sucursal}', 'showInformER');
    Route::get('/showInformERFiscal/{year}/{month}/{yearfiscal}/{monthfiscal}/{sucursal}', 'showInformERFiscal');
    Route::get('/showERevolutivo/{year}/{month}/{sucursal}', 'showDataErEvolutivo');
    Route::get('/showDataEvolutivo/{year}/{month}/{sucursal}', 'showDataErEvolutivo');
    Route::get('/showEstadisticas/{sucursal}', 'showEstadisticas');
    });
});
Route::controller(App\Http\Controllers\API\tipo_cambioAPIController::class)->group(function () {
    Route::prefix('tipo_cambios')->group(function () {
    Route::get('/getTipoCambio/{year}/{month}', 'getTipoCambio');
    });
});
Route::controller(App\Http\Controllers\API\grupo_economicos_empresasAPIController::class)->group(function () {
    Route::prefix('grupo_economicos_empresas')->group(function () {
    Route::get('/', 'getAllCompanies');
    });
});
Route::controller(App\Http\Controllers\API\tipo_documentoAPIController::class)->group(function () {
    Route::prefix('tipo_documentos')->group(function () {
        Route::get('/', 'getAllTipoDocumentos');
    });
});
Route::controller(App\Http\Controllers\API\documento_companiaAPIController::class)->group(function () {
    Route::prefix('documento_companias')->group(function () {
    Route::get('/', 'getAllDocumentosCompanias');
    Route::get('/download/{id}', [App\Http\Controllers\API\documento_companiaAPIController::class, 'download']);
    });
});
Route::post('/fileupload', [App\Http\Controllers\API\UserAPIController::class, 'uploadfile']);
Route::resource('users', App\Http\Controllers\API\UserAPIController::class)
    ->except(['create', 'edit']);
Route::resource('companies', App\Http\Controllers\API\CompanyAPIController::class)
    ->except(['create', 'edit']);
Route::resource('excelscompanies', App\Http\Controllers\API\excelscompanyAPIController::class)
    ->except(['create', 'edit']);
Route::resource('categorizacion_cts_balances', App\Http\Controllers\API\categorizacion_cts_balanceAPIController::class)
    ->except(['create', 'edit']);
Route::resource('clasificacion_cuenta_resuls', App\Http\Controllers\API\clasificacion_cuenta_resulAPIController::class)
    ->except(['create', 'edit']);
Route::resource('client_users', App\Http\Controllers\API\client_userAPIController::class)
    ->except(['create', 'edit']);
Route::resource('setup_analises', App\Http\Controllers\API\setup_analisisAPIController::class)
    ->except(['create', 'edit']);
Route::resource('setup_ers', App\Http\Controllers\API\setup_erAPIController::class)
    ->except(['create', 'edit']);
Route::resource('sucursales', App\Http\Controllers\API\sucursalesAPIController::class)
    ->except(['create', 'edit']);
Route::resource('super_clients', App\Http\Controllers\API\super_clientAPIController::class)
    ->except(['create', 'edit']);
Route::resource('estudios', App\Http\Controllers\API\estudiosAPIController::class)
    ->except(['create', 'edit']);
Route::resource('estudios_usuarios', App\Http\Controllers\API\estudios_usuariosAPIController::class)
    ->except(['create', 'edit']);
Route::resource('companies', App\Http\Controllers\API\companyAPIController::class)
    ->except(['create', 'edit']);
Route::resource('grupo_economicos', App\Http\Controllers\API\grupo_economicosAPIController::class)
    ->except(['create', 'edit']);
Route::resource('usuario_grupoeconomicos', App\Http\Controllers\API\usuario_grupoeconomicoAPIController::class)
    ->except(['create', 'edit']);
Route::resource('bitacora_hitos', App\Http\Controllers\API\bitacora_hitosAPIController::class)
    ->except(['create', 'edit']);
Route::resource('bitacora_enviosemails', App\Http\Controllers\API\bitacora_enviosemailAPIController::class)
    ->except(['create', 'edit']);
Route::resource('tipo_cambios_globals', App\Http\Controllers\API\tipo_cambios_globalAPIController::class)
    ->except(['create', 'edit']);
Route::resource('compromiso_entregas', App\Http\Controllers\API\compromiso_entregaAPIController::class)
    ->except(['create', 'edit']);
/*Route::resource('tipo_documentos', App\Http\Controllers\API\tipo_documentoAPIController::class)
    ->except(['create', 'edit']);
Route::resource('documento_companias', App\Http\Controllers\API\documento_companiaAPIController::class)
    ->except(['create', 'edit']);*/
Route::resource('bitacoras_envios_documentos', App\Http\Controllers\API\bitacoras_envios_documentoAPIController::class)
    ->except(['create', 'edit']);