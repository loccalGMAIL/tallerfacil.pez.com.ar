# Changelog — TallerFácil

Basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/).
Versionado semántico: `MAJOR.MINOR.PATCH` — en `0.x.y`, el MINOR es un sprint/semana de funcionalidades.

---

## [Sin publicar] — v0.2.0

### Agregado

**Dashboard**
- Tablero Kanban con columnas por estado (recepcion / cotizacion / reparacion / listo)
- Drag-and-drop entre columnas via SortableJS → `PATCH /ordenes/{id}/mover`
- KPIs del mes: ingresos estimados, órdenes cotizadas, vehículos entregados
- Vista Calendario mensual con navegación prev/next

**Recepción rápida**
- Modal mobile-first en el dashboard ("Ingresar vehículo / Cotización rápida")
- Crea cliente + vehículo + orden en un solo paso (`RecepcionController`)
- Busca clientes existentes por nombre/teléfono antes de crear uno nuevo

**Órdenes — nuevas funcionalidades**
- Checklist de tareas por orden (`OrdenTarea`): agregar, completar (toggle), eliminar
- Galería de fotos por orden (`OrdenFoto`): captura desde cámara o galería, almacenado en disco `public`
- PDF de cotización generado con `barryvdh/laravel-dompdf` (`GET /ordenes/{id}/cotizacion.pdf`)
- Asignación de mecánico desde la vista de detalle
- Carga rápida de ítems desde catálogo de Servicios

**Configuración (solo administrador)**
- Sección `/configuracion` con nav lateral y tres módulos:
  - **Negocio**: nombre, dirección, teléfono, CUIT, logo — datos usados en PDF y mensajes WA (`NegocioConfig` singleton)
  - **Servicios**: CRUD del catálogo de servicios con descripción y precio referencial
  - **Personal**: CRUD de usuarios del sistema; el admin define/resetea contraseñas

**WhatsApp**
- Biblioteca de mensajes guardados (personalizados por el taller) — CRUD completo
- Plantillas de eventos: recepcion, listo, entregado — configurables por estado
- Toggle de auto-envío de recordatorio por km por vehículo

### Cambiado

- **Estados de orden remapeados** (migración `remap_estados_ordenes`):
  - Antes: `presupuesto`, `en_reparacion`, `terminado`, etc.
  - Ahora: `recepcion → cotizacion → reparacion → listo` + cerrados `entregado` / `cancelado`
  - Config centralizada en `Orden::TRANSICIONES`, `ESTADOS_TABLERO`, `ESTADO_LABELS`, `ESTADO_BADGES`
- `WaRecordatorioConfig`: se elimina el campo `umbral_km_recordatorio` (migración `drop_umbral_km_recordatorio`)
- `WaPlantilla`: soporte para plantillas de tipo `evento` (migración `add_plantillas_evento`)
- `OrdenService`: refactorizado para soportar los nuevos estados y la carga de ítems desde catálogo
- `WhatsAppService`: agrega soporte para mensajes de evento y mensajes guardados
- Layout (`layouts/app.blade.php`): navbar actualizado con acceso a Configuración (engranaje) solo para admin
- Todas las vistas CRUD actualizadas al nuevo diseño Tailwind v4 / Alpine.js

### Base de datos — nuevas migraciones

| Migración | Descripción |
|-----------|-------------|
| `2026_06_06_000001_remap_estados_ordenes` | Renombra valores de estado en órdenes e historial |
| `2026_06_06_000002_create_orden_tareas_table` | Checklist de tareas por orden |
| `2026_06_06_000003_create_orden_fotos_table` | Galería de fotos por orden |
| `2026_06_06_000004_create_negocio_config_table` | Datos del taller (singleton) |
| `2026_06_06_000005_create_servicios_table` | Catálogo de servicios |
| `2026_06_06_000006_drop_umbral_km_recordatorio` | Limpieza de columna obsoleta |
| `2026_06_06_000007_add_plantillas_evento` | Tipo `evento` en plantillas WA |
| `2026_06_06_000008_create_wa_mensajes_guardados_table` | Biblioteca de mensajes personalizados |
| `2026_06_06_000009_add_auto_envio_recordatorio` | Toggle de auto-envío de recordatorio |

---

## [0.1.2] — 2026-05-30

### Cambiado

- `composer.json`: elimina `php artisan pail` del script `dev` (incompatible con Windows — sin extensión `pcntl`)
- `composer.lock`: actualizado para compatibilidad con PHP 8.3 (Symfony v8 → v7)
- `.gitignore`: excluye `.claude/settings.local.json`
- Agrega `CLAUDE.md` con guía base y comandos clave para Claude Code

---

## [0.1.1] — 2026-05-28

### Corregido

- **Nombres de tablas Eloquent**: agrega `$table` explícito en `Orden`, `OrdenItem`, `OrdenEstadoHistorial` para evitar pluralización incorrecta en español (`ordens`, `orden_items_model`)
- **Normalización de teléfono argentino**: reescritura de `normalizarTelefono()` para manejar formato `011-15-XXXX-XXXX` (13 dígitos crudos, área 2–4 dígitos, prefijo `15` opcional)
- **Webhook ACK de Evolution API**: maneja valores string (`DELIVERY_ACK`, `READ`, `PLAYED`, `ERROR`) además de los códigos numéricos Baileys (2, 3, -1)

---

## [0.1.0] — 2026-05-27

### Agregado

Base completa de Laravel 12 con toda la infraestructura del sistema.

**Infraestructura**
- Laravel 12, PHP 8.3, Tailwind CSS v4, Vite, Alpine.js
- 11 migraciones + vista SQL `v_vehiculos_para_recordatorio`
- Seeders: usuario admin, plantillas WA, `wa_config`, `wa_recordatorio_config`
- Job `EnviarMensajeWhatsAppJob` con 3 reintentos y backoff exponencial
- Middlewares: `CheckRole`, `ValidateEvolutionWebhook`, `ValidateRecordatorioToken`

**Modelos**
`Usuario`, `Cliente`, `Vehiculo`, `Orden`, `OrdenItem`, `OrdenEstadoHistorial`, `WaConfig`, `WaRecordatorioConfig`, `WaPlantilla`, `WaMensaje`

**Servicios**
- `ClienteService`: normalización de teléfono argentino
- `VehiculoService`: normalización de patente
- `OrdenService`: generación de número, cambio de estado con historial
- `EvolutionApiService`: integración con Evolution API (WhatsApp)
- `WhatsAppService`: orquestación de envío de mensajes y plantillas

**Controllers y vistas**
- Auth (Login/Logout)
- CRUD completo: Clientes, Vehículos, Órdenes (lista, detalle, ítems)
- WhatsApp: config, mensajes enviados, plantillas, recordatorio automático
- Webhook (`POST /api/webhook/evolution`) + API de recordatorio (`POST /api/recordatorio`)

**Rutas**
- `routes/web.php`: panel autenticado con roles
- `routes/api.php`: webhook Evolution API + endpoint n8n de recordatorio
