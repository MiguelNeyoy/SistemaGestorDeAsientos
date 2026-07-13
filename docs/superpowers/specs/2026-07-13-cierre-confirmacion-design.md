# Cierre de confirmación de asistencia

**Fecha:** 2026-07-13  
**Proyecto:** SistemaGestorDeAsientos  

## 1. Objetivo

Bloquear el registro de alumnos que no han confirmado su asistencia una vez que pase la fecha límite definida. Los alumnos que ya confirmaron deben poder acceder normalmente.

## 2. Comportamiento actual

- No existe ninguna lógica de fecha límite en backend
- Las fechas en `home_alumno.php` son texto HTML estático
- Cualquier alumno puede confirmar asistencia en cualquier momento

## 3. Cambios propuestos

### 3.1 Variable de entorno

`API/.env` → nueva variable:
```
CONFIRMACION_DEADLINE=2026-07-09 23:59:59
```

### 3.2 Backend — `ControladorAlumno::validarAlumno()`

Después de llamar a `buscarAlumno()`, si la respuesta es exitosa y el alumno está "Pendiente":
- Leer `$_ENV['CONFIRMACION_DEADLINE']`
- Si la fecha actual > fecha límite → cambiar respuesta a error 403 con flag `registro_cerrado: true`
- NO generar JWT
- El frontend recibe el flag y redirige

### 3.3 Frontend — `FrontEnd/index.php`

Después del login API call, si la respuesta contiene `registro_cerrado === true`:
- Redirigir a `registro_cerrado.php` en vez de continuar con el flujo normal

### 3.4 Frontend — `FrontEnd/registro_cerrado.php` (nuevo)

Página estática con el mismo header institucional (logo UAS, logo ConVision) y un mensaje informando que el período de confirmación finalizó. Sin formularios.

## 4. Archivos modificados

| Archivo | Tipo |
|---|---|
| `API/.env` | Modificar — añadir variable |
| `API/controladores/ControladorAlumno.php` | Modificar — validar fecha en login |
| `FrontEnd/index.php` | Modificar — capturar flag y redirigir |
| `FrontEnd/registro_cerrado.php` | Crear — página de cierre |

## 5. Flujo resultante

```
Alumno ingresa numCuenta → POST /alumnos/validar
  ├─ ¿Pasó la fecha límite?
  │   ├─ Sí + No ha confirmado → 403 registro_cerrado → redirect a registro_cerrado.php
  │   └─ Sí + Ya confirmó → Normal (JWT + acceso completo)
  └─ No pasó → Normal (JWT + flujo existente)
```
