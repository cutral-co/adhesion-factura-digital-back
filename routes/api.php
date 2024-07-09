<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarrioController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\SolicitudController;
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

Route::post('login', [AuthController::class, 'login']);

Route::get('barrios', [BarrioController::class, 'index']);
Route::get('provincias', [ProvinciaController::class, 'index']);

Route::post('solicitudes', [SolicitudController::class, 'store']);

Route::get('get_by_token', [SolicitudController::class, 'get_by_token']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::group(['middleware' => ['jwt.verify', 'permission:admin']], function () {
    Route::get('solicitudes', [SolicitudController::class, 'index']);

    Route::get('solicitudes/pendientes', [SolicitudController::class, 'pendientes']);
    Route::get('solicitudes/aprobadas', [SolicitudController::class, 'aprobadas']);
    Route::get('solicitudes/rechazadas', [SolicitudController::class, 'rechazadas']);
    Route::get('solicitudes/no_verificadas', [SolicitudController::class, 'no_verificadas']);

    Route::post('solicitudes/borrar', [SolicitudController::class, 'destroy']);

    Route::post('solicitudes/cambiar-estado', [SolicitudController::class, 'cambiarEstado']);
    Route::get('monitor', [MonitorController::class, 'index']);
    Route::get('monitor/resumen', [MonitorController::class, 'resumen']);
    Route::get('monitor/total_mensual', [MonitorController::class, 'cantidadSolicitudesMes']);
});

Route::get('correo', [SolicitudController::class, 'envio_correo_verificar']);
