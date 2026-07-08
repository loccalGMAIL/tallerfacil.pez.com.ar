# Arquitectura del Proyecto — TallerFácil

## Descripción
Sistema de gestión para talleres mecánicos. Manejo de clientes, vehículos, órdenes de trabajo y comunicación por WhatsApp.

## Stack
- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Vite (assets), Blade (templates)
- **Base de datos:** MySQL (configurar en .env)
- **Cola:** Laravel Queue (driver a configurar)
- **Dev:** `composer run dev` — levanta servidor, queue y Vite en paralelo

## Modelos principales
- `Usuario` — usuarios del sistema (staff del taller)
- `Cliente` — clientes del taller
- `Vehiculo` — vehículos de los clientes
- `Orden` — órdenes de trabajo
- `OrdenItem` — ítems/trabajos dentro de una orden
- `OrdenEstadoHistorial` — historial de estados de una orden
- `WaConfig` — configuración de WhatsApp
- `WaRecordatorioConfig` — config de recordatorios automáticos
- `WaPlantilla` — plantillas de mensajes WA
- `WaMensaje` — mensajes enviados por WA
- `OrdenTarea` — checklist de tareas de la orden
- `OrdenFoto` — fotos de la orden (cámara), disco `public`
- `Servicio` — catálogo de servicios (carga rápida en cotización)
- `NegocioConfig` — datos del taller (singleton, usado en PDF/mensajes)

## Funcionalidades clave (inspiradas en Appli-Car)
- **Estados de orden (4 + cerrados)**: recepcion → cotizacion → reparacion → listo; cerrados: entregado, cancelado. Config centralizada en `Orden` (TRANSICIONES, ESTADOS_TABLERO, ESTADO_LABELS, ESTADO_BADGES).
- **Panel** (`/`, DashboardController): KPIs del mes + tablero Kanban con **drag-and-drop** (SortableJS → `ordenes.mover` → `OrdenService::moverATablero`) + vista **Calendario**.
- **Ingresar vehículo / Cotización rápida**: modal mobile-first en el Panel (`RecepcionController`) que crea cliente+vehículo+orden.
- **Cotización**: ítems agrupados Servicios/Repuestos, **PDF** (barryvdh/laravel-dompdf), carga rápida desde catálogo de Servicios.
- **Configuración** (`/configuracion`, solo admin, engranaje en navbar): Negocio, Servicios (CRUD), Personal (CRUD usuarios, admin define contraseña), WhatsApp (módulo Evolution API existente).

## Rutas
- `routes/web.php` — rutas web
- `routes/api.php` — API
- `routes/console.php` — comandos

## Portal admin (proyecto hermano)
- `D:\DESARROLLO\CoDiGo\admin.tallerfacil.pez.com.ar` — portal administrativo SaaS (Laravel 12, Blade+Alpine+Tailwind 4) que reemplaza gradualmente al superadmin embebido.
- **Misma BD MySQL**: el portal solo crea tablas `portal_*` y usa `portal_migrations`/`portal_sessions`/`portal_cache`. Regla: tablas de dominio → migraciones SOLO acá; `portal_*` → SOLO en el portal.
- **Sin colas** (Hostinger compartido): ambas apps con `QUEUE_CONNECTION=sync`; automatización por cron (`schedule:run`).
- Integración: TallerFácil expone `GET /impersonar/{token}` (tokens en `portal_impersonation_tokens`) y su webhook Evolution escribe en `portal_wa_eventos` y `portal_wa_contadores_diarios`.

## Convenciones
- Nombres de modelos y tablas en español
- **Todo modelo declara `protected $table` explícito** — la pluralización inglesa de Eloquent rompe con nombres en español (`Taller`→`tallers`); bug latente corregido el 2026-07-08
- **Versión del sistema**: `config/app.php` → `'version'` (leer con `config('app.version')`); mantener en sincronía con el encabezado de CHANGELOG.md al publicar
- **Ramas**: prefijo con la versión en curso — `v{version}/feature/{nombre}` (ej. `v0.2.0/feature/mobile-cards-pwa`)
- php artisan pail **no disponible** en Windows (sin extensión pcntl) — removido del script dev
- **Tablas → cards en mobile**: toda tabla de datos lleva clase `table-cards` (+ `dark-cards` en superadmin), `data-label` en cada td, `td-acciones` en la celda de acciones y `td-vacio` en la fila vacía con colspan. CSS en `resources/css/app.css` (bloque sin @layer, <768px). El PDF de cotización queda fuera.

## PWA
- Instalable: `public/manifest.webmanifest`, íconos en `public/icons/` (regenerables con `php scripts/generar-iconos.php`), SW mínimo `public/sw.js` (registrado solo en PROD desde `resources/js/app.js`)
- Banner "Agregar a pantalla de inicio": componente Alpine `pwaInstall` (app.js) + partial `layouts/partials/pwa-banner.blade.php` (solo layout app); iOS muestra instrucciones, Android usa `beforeinstallprompt`; descarte persistido en localStorage

## Setup inicial
```bash
composer run dev       # servidor + queue + vite
php artisan migrate    # crear tablas
```
