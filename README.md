# SistemaGestorDeAsientos
Este sistema es un proyecto para tener el control y agilizar el ordenamiento de las personas en la ceremonia de graduacion

# Proyecto de Graduación - Sistema de Asistencia

## Estructura del Proyecto
- `/NUEVA CARPETA/API/` - Backend del servicio de alumnos
- `/SistemaGestorDeAsientos/` - Módulo de gestión de asientos

## Servicio de Alumno (API)
Endpoint: `/NUEVA CARPETA/API/public/index.php`

### Acciones disponibles:
- `GET ?action=validar&numero_cuenta=XXX` - Validar alumno
- `POST ?action=confirmar` - Confirmar asistencia

## Ramas Git
- `main` - Rama principal
- `feature/servicio-alumno` - Desarrollo del servicio de alumnos
- `feature/base-datos` - Estructura de BD

## CREATE TABLE alumnos (
    id_alumno INT PRIMARY KEY AUTO_INCREMENT,
    numero_cuenta VARCHAR(20),
    nombre VARCHAR(100),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    carrera VARCHAR(100),
    semestre INT,
    asistira TINYINT(1) NULL,
    num_invitados INT NULL,
    correo VARCHAR(150) NULL,
    fecha_confirmacion DATETIME NULL
);
