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

## Rutas
- `routes/web.php` — rutas web
- `routes/api.php` — API
- `routes/console.php` — comandos

## Convenciones
- Nombres de modelos y tablas en español
- php artisan pail **no disponible** en Windows (sin extensión pcntl) — removido del script dev

## Setup inicial
```bash
composer run dev       # servidor + queue + vite
php artisan migrate    # crear tablas
```
