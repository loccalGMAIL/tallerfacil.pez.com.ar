# Arquitectura del Proyecto — TallerFácil

## Descripción
SaaS de gestión para talleres mecánicos. Multi-tenant por subdominio (`taller1.tallerfacil.com.ar`). Panel de superadmin en `admin.tallerfacil.com.ar`.

## Stack
- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Vite (assets), Blade (templates)
- **Base de datos:** MySQL compartida con `taller_id` en tablas tenant
- **Cola:** Laravel Queue (driver a configurar)
- **Dev:** `composer run dev` — levanta servidor, queue y Vite en paralelo
- **Paquetes clave:** `spatie/laravel-activitylog ^5`, `mercadopago/dx-php ^3`

## Multi-tenancy
- Resolución por subdominio → `SetTallerActual` middleware (alias `taller`)
- IoC binding: `app('taller.actual')` contiene el `Taller` activo
- Global Scope: `TallerScope` (en `app/Scopes/`) se aplica automáticamente a todos los modelos con el trait `BelongsToTaller` cuando `taller.actual` está bound
- Trait `BelongsToTaller` (en `app/Models/Concerns/`) aplica el scope y auto-asigna `taller_id` al crear
- El superadmin (`rol=superadmin`, `taller_id=NULL`) no tiene el scope activo → ve todos los datos

## Modelos principales
- `Taller` — tenant (subdominio, activo, suscripcion)
- `Suscripcion` — plan/estado por taller (activo/prueba/vencido/cancelado)
- `Pago` — pagos de MercadoPago por suscripción
- `Usuario` — staff del taller; `rol` ∈ [superadmin, administrador, mecanico]
- `Cliente`, `Vehiculo`, `Orden`, `OrdenItem`, `OrdenEstadoHistorial`
- `WaConfig`, `WaRecordatorioConfig`, `WaPlantilla`, `WaMensaje`, `WaMensajeGuardado`
- `OrdenTarea`, `OrdenFoto`, `Servicio`
- `NegocioConfig` — **deprecado**, reemplazado por `app('taller.actual')` (Taller)

## Funcionalidades clave (inspiradas en Appli-Car)
- **Estados de orden (4 + cerrados)**: recepcion → cotizacion → reparacion → listo; cerrados: entregado, cancelado. Config centralizada en `Orden` (TRANSICIONES, ESTADOS_TABLERO, ESTADO_LABELS, ESTADO_BADGES).
- **Panel** (`/`, DashboardController): KPIs del mes + tablero Kanban con **drag-and-drop** (SortableJS → `ordenes.mover` → `OrdenService::moverATablero`) + vista **Calendario**.
- **Ingresar vehículo / Cotización rápida**: modal mobile-first en el Panel (`RecepcionController`) que crea cliente+vehículo+orden.
- **Cotización**: ítems agrupados Servicios/Repuestos, **PDF** (barryvdh/laravel-dompdf), carga rápida desde catálogo de Servicios.
- **Configuración** (`/configuracion`, solo admin, engranaje en navbar): Negocio, Servicios (CRUD), Personal (CRUD usuarios, admin define contraseña), WhatsApp (módulo Evolution API existente).

## Middlewares clave
- `taller` (SetTallerActual) — extrae subdominio y bindea `taller.actual`
- `superadmin` (EsSuperAdmin) — aborta 403 si no es superadmin
- `suscripcion.activa` (VerificarSuscripcionActiva) — redirige a `suscripcion.index` si suscripción vencida

## Rutas
- `routes/web.php` — web (panel taller + panel superadmin + auth + password reset + suscripción)
- `routes/api.php` — API (webhooks: evolution, mercadopago; recordatorios n8n)
- Panel superadmin: `Route::domain('admin.' . config('app.base_domain'))` → controladores en `App\Http\Controllers\SuperAdmin\`
- Panel taller: `Route::middleware(['taller'])` → rutas existentes + suscripcion.*
- Password reset: `password.request`, `password.email`, `password.reset`, `password.update`

## Servicios
- `MercadoPagoService` — preapproval, cancelar, procesarWebhook
- `EvolutionApiService` — lazy-loads `WaConfig` (compatible con multi-tenant)

## Activity Log (spatie)
- `LogsActivity` trait en: `Orden`, `Usuario`, `Taller`
- Login/Logout logueado en `AppServiceProvider` con categoria `auth`
- Panel superadmin → Logs (`/logs`) muestra `activity_log` filtrable

## Variables de entorno necesarias
- `APP_BASE_DOMAIN=tallerfacil.com.ar`
- `TALLER_INICIAL_SUBDOMINIO=app`
- `MP_ACCESS_TOKEN`, `MP_PUBLIC_KEY`, `MP_WEBHOOK_SECRET`
- `SUPERADMIN_EMAIL`, `SUPERADMIN_PASSWORD`

## Convenciones
- Nombres de modelos y tablas en español
- Nunca usar `NegocioConfig::instancia()` — usar `app('taller.actual')`
- En el panel superadmin, usar `withoutGlobalScopes()` al querier modelos tenant

## Setup inicial
```bash
composer run dev           # servidor + queue + vite
php artisan migrate        # crear tablas + seed taller inicial
php artisan db:seed --class=SuperAdminSeeder  # crear usuario superadmin
```
