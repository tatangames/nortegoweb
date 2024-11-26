<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Login\ApiLoginController;
use App\Http\Controllers\Api\Configuracion\Principal\ApiPrincipalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('app/verificacion/telefono', [ApiLoginController::class,'verificacionTelefono']);
Route::post('app/reintento/telefono', [ApiLoginController::class,'reintentoSMS']);
Route::post('app/verificarcodigo/telefono', [ApiLoginController::class,'verificarCodigo']);


// Numeros autorizados para iniciar sesion en aplicacion motorista
Route::post('app/numeros/motoristas', [ApiLoginController::class,'numerosMotoristas']);



// ******************* RUTAS CON AUTENTIFICACION **********************
Route::middleware('verificarToken')->group(function () {

    // --- PANTALLA PRINCIPAL
    Route::post('app/principal/listado', [ApiPrincipalController::class,'listadoPrincipal']);


    // --- GUARDAR DATOS SERVICIO BASICO ---
    Route::post('app/servicios/basicos/registrar', [ApiPrincipalController::class,'registrarServicioBasico']);

    // --- GUARDAR DATOS PARA SOLICITUD TALA DE ARBOL ---
    Route::post('app/servicios/talaarbol-solicitud/registrar', [ApiPrincipalController::class,'registrarTalaArbolSolicitud']);

    // --- GUARDAR DATOS PARA DENUNCIA TALA DE ARBOL ---
    Route::post('app/servicios/talaarbol-denuncia/registrar', [ApiPrincipalController::class,'registrarTalaArbolDenuncia']);

    // LISTADO DE SOLICITUDES MIXTAS
    Route::post('app/solicitudes/listado', [ApiPrincipalController::class,'listadoSolicitudes']);

    // OCULTAR SOLICITUD POR EL USUARIO
    Route::post('app/solicitudes/ocultar', [ApiPrincipalController::class,'ocultarSolicitudes']);

    // REGISTRAR SERVICIO DE CATASTRO
    Route::post('app/solicitud/catastro', [ApiPrincipalController::class,'registrarSolicitudCatastro']);
});

