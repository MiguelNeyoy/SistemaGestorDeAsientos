# Ticket PDF con QR

**Fecha:** 2026-06-24  
**Proyecto:** SistemaGestorDeAsientos  
**Archivo a modificar:** `FrontEnd/view_qr.php`

## 1. Objetivo

Reemplazar la descarga actual de PNG por una descarga en PDF con diseño tipo ticket, que incluya logos institucionales, código QR y datos del alumno.

## 2. Comportamiento actual

- `view_qr.php` genera QR con `qrcodejs` en un `<canvas>`
- Botón "Descargar mi pase" exporta el canvas como `mi_pase_qr.png`
- No hay diseño, solo la imagen cruda del QR

## 3. Cambios propuestos

### 3.1 Librería añadida
- `html2pdf.js` v0.10.2 (CDN) — misma librería ya usada en el panel admin

### 3.2 Template del ticket (HTML oculto)
Contenedor `<div id="ticket-content" style="display:none">` con el diseño:

```
┌──────────────────────────────────┐
│  [logoUAS]        [logoFIMAZ]    │
│                                  │
│   CEREMONIA DE GRADUACIÓN        │
│   15 de Julio de 2026            │
│                                  │
│       ┌──────────────┐           │
│       │   QR Code     │           │
│       └──────────────┘           │
│                                  │
│   {nombre_alumno}                │
│   Asiento: {asiento}             │
│   {carrera}                      │
│                                  │
│   ─────────────────────────      │
│   Universidad Autónoma de        │
│   Sinaloa                        │
└──────────────────────────────────┘
```

### 3.3 Flujo de descarga
1. Usuario hace clic en "Descargar mi pase"
2. JavaScript captura el `<canvas>` del QR → `toDataURL()`
3. Inyecta la imagen en el template del ticket
4. `html2pdf()` convierte el template a PDF A5 portrait
5. Se descarga como `mi_pase_qr.pdf`

### 3.4 Datos mostrados
| Campo | Variable PHP | Ejemplo |
|-------|-------------|---------|
| Nombre | `$alumno['nombre']` | Juan Pérez López |
| Asiento | `$alumno['asiento']` | A-12 |
| Carrera | `$alumno['carrera']` | LI4-1 |
| QR | `$qrToken` (canvas) | — |
| Fecha | Hardcode | 15 de Julio de 2026 |
| Horario | Según carrera | 11:30 AM / 10:00 AM |
| Logos | `img/logouas.png`, `img/logofimaz.png` | — |

### 3.5 Estilos del ticket (inline)
- Fondo: blanco `#ffffff`
- Borde: `4px solid #D4AF37` (dorado institucional)
- Texto principal: `#003B71` (navy), Poppins/Segoe UI
- Encabezado: fondo `#003B71` con texto dorado `#FDC800`
- QR centrado, padding 16px, fondo blanco
- Layout: A5 portrait (148×210 mm)

## 4. Eliminaciones
- Código de descarga PNG (`canvas.toDataURL` + `link.download = "mi_pase_qr.png"`)

## 5. No cambia
- Lógica PHP de obtención de datos (token QR, estado alumno, asiento)
- Generación del QR con `qrcodejs`
- Estilos visuales de la página existente
- Rutas de API ni controladores
