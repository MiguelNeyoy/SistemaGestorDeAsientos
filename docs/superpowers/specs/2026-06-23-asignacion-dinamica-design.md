# Asignación Dinámica de Asientos

## Resumen
Sistema de dos fases: confirmación de asistencia → asignación dinámica post-cierre.
Los asientos se asignan por orden de turno (Matutino → Vespertino) + orden alfabético,
ejecutado manualmente por el admin mediante batches de 50.

## Arquitectura

### Fase 1 — Confirmación (existe, sin cambios)
- Alumno confirma asistencia → `asistencia.estado = 1`
- No se muestra asiento. Mensaje: "Se asignará al cerrar registro"

### Fase 2 — Asignación (nuevo)
1. Admin cierra confirmaciones (existe: botón Resetear Confirmaciones)
2. Admin → **"Limpiar asignaciones"**: `UPDATE asiento_evento_{li,lisi} SET numCuenta = NULL`
3. Admin → **"Vista previa"** (dry-run): muestra conteos sin escribir
4. Admin → **"Asignar asientos"**: ejecuta algoritmo, modal con spinner+progreso
5. Admin → **"Publicar resultados"** (switch): alumnos ven su asiento

## Base de Datos

### Tablas afectadas
- `asiento_evento_li`: se limpia/reescribe `numCuenta`
- `asiento_evento_lisi`: se limpia/reescribe `numCuenta`
- Nueva tabla `config_asignacion`: guarda `publicado BOOLEAN`, `fecha_asignacion TIMESTAMP`

### Algoritmo de asignación
```
1. Obtener alumnos con asistencia.estado=1 para evento li
   ORDER BY turno (M→V), apellido ASC, nombre ASC
2. Obtener asientos en asiento_evento_li
   ORDER BY letra ASC, numero ASC
3. Mapear alumno[i] → asiento[i]
4. Ejecutar batches de 50:
   BEGIN TRANSACTION
   UPDATE asiento_evento_li SET numCuenta = ? WHERE idAsiento = ?
   (×50 por batch)
   COMMIT
5. Repetir para evento lisi
6. Alumnos sin asiento (si sobran) → alerta
```

## API — Nuevos Endpoints

| Método | Ruta | Auth | Body | Respuesta |
|--------|------|------|------|-----------|
| POST | `/admin/asignacion/limpiar` | Admin | — | `{success, message}` |
| POST | `/admin/asignacion/ejecutar` | Admin | `{dry_run: bool}` | `{success, dry_run, li: N, lisi: N, sin_asiento: N}` |
| GET | `/admin/asignacion/estado` | Admin | — | `{asignado: bool, fecha, publicado: bool, confirmados: N, capacidad: N}` |
| POST | `/admin/asignacion/publicar` | Admin | `{publicado: bool}` | `{success, message}` |

## Panel Admin — Sidebar

Sección "Evento", después de "Resetear Confirmaciones":
- `○ Limpiar asignaciones` (confirmación "¿Seguro?")
- `▶ Asignar asientos` (botón destacado)
  - Primero muestra modal de **vista previa** con conteos
  - Si confirma, modal de **progreso** con spinner
- `⚡ Publicar resultados` (switch ON/OFF)

## Vista Alumno

- **Antes de publicar**: mapa de asientos sin resaltado, QR sin número de asiento
- **Después de publicar**: asiento resaltado en azul en mapa, QR con asiento
- **Colores alumno**: gris oscuro = ocupado por otro, azul = mi asiento
- **Colores admin**: se mantienen igual (azul = QR escaneado)

## Testing

- `POST /admin/asignacion/ejecutar {dry_run: true}` verifica conteos antes de escribir
- Script `test_api.sh` actualizado con pruebas de los nuevos endpoints
- Rollback manual con botón "Limpiar asignaciones" si algo sale mal
