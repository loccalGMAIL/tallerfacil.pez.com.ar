<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Panel principal — requiere autenticación
Route::middleware('auth')->group(function () {

    Route::get('/', fn () => redirect()->route('ordenes.index'));

    // Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Vehículos
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('/vehiculos/crear', [VehiculoController::class, 'create'])->name('vehiculos.create');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.show');
    Route::get('/vehiculos/{vehiculo}/editar', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // Órdenes de trabajo
    Route::get('/ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/crear', [OrdenController::class, 'create'])->name('ordenes.create');
    Route::post('/ordenes', [OrdenController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{orden}', [OrdenController::class, 'show'])->name('ordenes.show');
    Route::post('/ordenes/{orden}/estado', [OrdenController::class, 'cambiarEstado'])->name('ordenes.estado');
    Route::post('/ordenes/{orden}/mecanico', [OrdenController::class, 'asignarMecanico'])->name('ordenes.mecanico');
    Route::post('/ordenes/{orden}/items', [OrdenController::class, 'storeItem'])->name('ordenes.items.store');
    Route::delete('/ordenes/{orden}/items/{item}', [OrdenController::class, 'destroyItem'])->name('ordenes.items.destroy');

    // WhatsApp — solo administrador
    Route::middleware('role:administrador')->group(function () {
        Route::post('/whatsapp/presupuesto/{orden}', [WhatsAppController::class, 'enviarPresupuesto'])->name('whatsapp.presupuesto');
        Route::post('/whatsapp/recepcion/{orden}', [WhatsAppController::class, 'enviarRecepcion'])->name('whatsapp.recepcion');
        Route::get('/whatsapp/mensajes', [WhatsAppController::class, 'mensajes'])->name('whatsapp.mensajes');
        Route::get('/whatsapp/config', [WhatsAppController::class, 'config'])->name('whatsapp.config');
        Route::post('/whatsapp/config', [WhatsAppController::class, 'saveConfig'])->name('whatsapp.config.save');
        Route::get('/whatsapp/qr', [WhatsAppController::class, 'qr'])->name('whatsapp.qr');
        Route::post('/whatsapp/desconectar', [WhatsAppController::class, 'desconectar'])->name('whatsapp.desconectar');
        Route::get('/whatsapp/recordatorio', [WhatsAppController::class, 'recordatorioConfig'])->name('whatsapp.recordatorio');
        Route::post('/whatsapp/recordatorio', [WhatsAppController::class, 'saveRecordatorioConfig'])->name('whatsapp.recordatorio.save');
        Route::get('/whatsapp/plantillas', [WhatsAppController::class, 'plantillas'])->name('whatsapp.plantillas');
        Route::put('/whatsapp/plantillas/{plantilla}', [WhatsAppController::class, 'updatePlantilla'])->name('whatsapp.plantillas.update');
    });
});
