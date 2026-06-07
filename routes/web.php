<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\OrdenFotoController;
use App\Http\Controllers\OrdenTareaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\LogsController;
use App\Http\Controllers\SuperAdmin\SuscripcionesController;
use App\Http\Controllers\SuperAdmin\TalleresController;
use App\Http\Controllers\SuperAdmin\UsuariosController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

// ============================================================
// Panel superadmin — admin.tallerfacil.com.ar
// ============================================================
Route::domain('admin.' . config('app.base_domain'))->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('superadmin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('superadmin.login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('superadmin.logout')->middleware('auth');

    Route::middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/', [SuperAdminDashboard::class, 'index'])->name('superadmin.dashboard');

        // Talleres
        Route::get('/talleres', [TalleresController::class, 'index'])->name('superadmin.talleres.index');
        Route::get('/talleres/crear', [TalleresController::class, 'create'])->name('superadmin.talleres.create');
        Route::post('/talleres', [TalleresController::class, 'store'])->name('superadmin.talleres.store');
        Route::get('/talleres/{taller}', [TalleresController::class, 'show'])->name('superadmin.talleres.show');
        Route::get('/talleres/{taller}/editar', [TalleresController::class, 'edit'])->name('superadmin.talleres.edit');
        Route::put('/talleres/{taller}', [TalleresController::class, 'update'])->name('superadmin.talleres.update');
        Route::post('/talleres/{taller}/activar', [TalleresController::class, 'activar'])->name('superadmin.talleres.activar');
        Route::post('/talleres/{taller}/desactivar', [TalleresController::class, 'desactivar'])->name('superadmin.talleres.desactivar');

        // Usuarios
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('superadmin.usuarios.index');
        Route::get('/usuarios/{usuario}/editar', [UsuariosController::class, 'edit'])->name('superadmin.usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuariosController::class, 'update'])->name('superadmin.usuarios.update');
        Route::post('/usuarios/{usuario}/reset-password', [UsuariosController::class, 'resetPassword'])->name('superadmin.usuarios.reset-password');

        // Logs de actividad
        Route::get('/logs', [LogsController::class, 'index'])->name('superadmin.logs.index');

        // Suscripciones
        Route::get('/suscripciones', [SuscripcionesController::class, 'index'])->name('superadmin.suscripciones.index');
        Route::get('/suscripciones/taller/{taller}', [SuscripcionesController::class, 'show'])->name('superadmin.suscripciones.show');
        Route::post('/suscripciones/{suscripcion}/activar', [SuscripcionesController::class, 'activar'])->name('superadmin.suscripciones.activar');
        Route::post('/suscripciones/{suscripcion}/cancelar', [SuscripcionesController::class, 'cancelar'])->name('superadmin.suscripciones.cancelar');
    });
});

// ============================================================
// Auth — compartido (cuando no hay subdominio configurado o en localhost)
// ============================================================
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// Panel de taller — aplica el middleware de tenant
// ============================================================
Route::middleware(['taller'])->group(function () {

    // Panel principal — requiere autenticación
    Route::middleware(['auth', 'suscripcion.activa'])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Perfil
        Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

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
        Route::post('/ordenes/{orden}/mover', [OrdenController::class, 'moverColumna'])->name('ordenes.mover');

        // Recepción rápida
        Route::post('/recepcion', [RecepcionController::class, 'store'])->name('recepcion.store');
        Route::post('/ordenes/{orden}/mecanico', [OrdenController::class, 'asignarMecanico'])->name('ordenes.mecanico');
        Route::post('/ordenes/{orden}/items', [OrdenController::class, 'storeItem'])->name('ordenes.items.store');
        Route::delete('/ordenes/{orden}/items/{item}', [OrdenController::class, 'destroyItem'])->name('ordenes.items.destroy');
        Route::get('/ordenes/{orden}/cotizacion.pdf', [OrdenController::class, 'cotizacionPdf'])->name('ordenes.cotizacion.pdf');

        // Tareas de la orden
        Route::post('/ordenes/{orden}/tareas', [OrdenTareaController::class, 'store'])->name('ordenes.tareas.store');
        Route::patch('/ordenes/{orden}/tareas/{tarea}', [OrdenTareaController::class, 'toggle'])->name('ordenes.tareas.toggle');
        Route::delete('/ordenes/{orden}/tareas/{tarea}', [OrdenTareaController::class, 'destroy'])->name('ordenes.tareas.destroy');

        // Fotos de la orden
        Route::post('/ordenes/{orden}/fotos', [OrdenFotoController::class, 'store'])->name('ordenes.fotos.store');
        Route::delete('/ordenes/{orden}/fotos/{foto}', [OrdenFotoController::class, 'destroy'])->name('ordenes.fotos.destroy');

        // WhatsApp y Configuración — solo administrador
        Route::middleware('role:administrador')->group(function () {
            Route::post('/whatsapp/presupuesto/{orden}', [WhatsAppController::class, 'enviarPresupuesto'])->name('whatsapp.presupuesto');
            Route::post('/whatsapp/recepcion/{orden}', [WhatsAppController::class, 'enviarRecepcion'])->name('whatsapp.recepcion');
            Route::post('/whatsapp/evento/{orden}/{tipo}', [WhatsAppController::class, 'enviarEvento'])->name('whatsapp.evento');
            Route::post('/whatsapp/manual/{orden}', [WhatsAppController::class, 'enviarManual'])->name('whatsapp.manual');
            Route::get('/whatsapp/mensajes', [WhatsAppController::class, 'mensajes'])->name('whatsapp.mensajes');
            Route::post('/whatsapp/guardados', [WhatsAppController::class, 'storeGuardado'])->name('whatsapp.guardados.store');
            Route::put('/whatsapp/guardados/{guardado}', [WhatsAppController::class, 'updateGuardado'])->name('whatsapp.guardados.update');
            Route::delete('/whatsapp/guardados/{guardado}', [WhatsAppController::class, 'destroyGuardado'])->name('whatsapp.guardados.destroy');
            Route::get('/whatsapp/config', [WhatsAppController::class, 'config'])->name('whatsapp.config');
            Route::post('/whatsapp/config', [WhatsAppController::class, 'saveConfig'])->name('whatsapp.config.save');
            Route::get('/whatsapp/qr', [WhatsAppController::class, 'qr'])->name('whatsapp.qr');
            Route::post('/whatsapp/desconectar', [WhatsAppController::class, 'desconectar'])->name('whatsapp.desconectar');
            Route::get('/whatsapp/recordatorio', [WhatsAppController::class, 'recordatorioConfig'])->name('whatsapp.recordatorio');
            Route::post('/whatsapp/recordatorio', [WhatsAppController::class, 'saveRecordatorioConfig'])->name('whatsapp.recordatorio.save');
            Route::get('/whatsapp/plantillas', [WhatsAppController::class, 'plantillas'])->name('whatsapp.plantillas');
            Route::put('/whatsapp/plantillas/{plantilla}', [WhatsAppController::class, 'updatePlantilla'])->name('whatsapp.plantillas.update');

            Route::prefix('configuracion')->name('configuracion.')->group(function () {
                Route::get('/', fn () => redirect()->route('configuracion.negocio'))->name('index');
                Route::get('/negocio', [NegocioController::class, 'edit'])->name('negocio');
                Route::put('/negocio', [NegocioController::class, 'update'])->name('negocio.update');
                Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios');
                Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
                Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
                Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
                Route::get('/personal', [PersonalController::class, 'index'])->name('personal');
                Route::post('/personal', [PersonalController::class, 'store'])->name('personal.store');
                Route::put('/personal/{usuario}', [PersonalController::class, 'update'])->name('personal.update');
                Route::delete('/personal/{usuario}', [PersonalController::class, 'destroy'])->name('personal.destroy');
            });
        });
    });
});
