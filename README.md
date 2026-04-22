# SistemaGestorDeAsientos

## ¿Qué es este sistema?

Es una aplicación web diseñada para gestionar la ceremonia de graduación de la Facultad de Informática. Su objetivo es facilitar el control de asistencia y la organización de los asientos para el evento de clausura.

### ¿Para quién sirve?

- **Para los estudiantes**: Les permite confirmar si asistirán a la graduación, indicar cuántos invitados traerán y proporcionar un correo de contacto. También pueden ver el asiento que les fue asignado.
- **Para los administradores**: Permite llevar un registro de todos los alumnos confirmados, ver estadísticas en tiempo real (cuántos confirmaron, cuántos invitados, distribución por carrera y turno), editar información de los estudiantes y escanear códigos QR el día del evento para registrar quién llegó.

### ¿Qué puede hacer un estudiante?

1. **Ingresar con su número de cuenta**: El sistema valida que sea estudiante activo.
2. **Confirmar asistencia**: Puede decir si asistirá o no a la ceremonia.
3. **Indicar invitados**: Puede llevar hasta 5 invitados.
4. **Proporcionar correo**: Para recibir información sobre el evento.
5. **Ver su asiento**: Una vez confirmada la asistencia, puede ver en un mapa visual cuál es su asiento asignado en el teatro.

### ¿Qué puede hacer un administrador?

1. **Iniciar sesión**: Con credenciales exclusivas para personal autorizado.
2. **Ver dashboard con métricas**: Estadísticas generales del evento (confirmados, rechazados, total de invitados, distribución por turno y carrera).
3. **Buscar y editar alumnos**: Modificar datos de estudiantes si es necesario.
4. **Escanear QR**: El día del evento, escanear el código QR del estudiante para confirmar su llegada.
5. **Ver mapa de asientos**: Visualizar qué asientos están ocupados.

---

## Requisitos técnicos

- PHP 8.x
- MySQL
- Composer
- XAMPP/LAMP (servidor web)

## Instalación

1. **Instalar dependencias:**
   ```bash
   composer install
   cd API && composer install
   ```

2. **Configurar base de datos:**
   - Crear la base de datos MySQL
   - Importar el esquema (si existe)

3. **Configurar variables de entorno:**
   Copiar `API/.env.example` a `API/.env` y completar los datos de conexión.

4. **Iniciar servicios:**
   - Apache y MySQL (XAMPP/LAMP)

---

## API (Backend)

### Rutas disponibles

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|----------------|
| POST | `/admin/login` | Inicio de sesión de administrador | No |
| GET | `/admin/alumnos` | Lista todos los alumnos con sus datos | JWT (Admin) |
| GET | `/admin/metricas` | Obtiene estadísticas del evento | JWT (Admin) |
| PUT | `/admin/alumnos/editar` | Edita datos de un alumno | JWT (Admin) |
| POST | `/alumnos/validar` | Valida número de cuenta y retorna JWT | No |
| POST | `/alumnos/asistencia` | Confirma asistencia del alumno | JWT (Alumno) |
| POST | `/alumnos/correo` | Actualiza correo del alumno | JWT (Alumno) |
| GET | `/alumnos/estado` | Obtiene el estado actual del alumno | JWT (Alumno) |

### Estructura de archivos

```
API/
├── configuracion/
│   ├── ConexionDB.php    # Conexión a la base de datos
│   └── variables.php     # Carga de configuración
├── controladores/
│   ├── ControladorAlumno.php       # Lógica de estudiantes
│   ├── ControladorAdministrador.php # Lógica de admin
│   └── ControladorAsientos.php     # Gestión de asientos
├── servicios/
│   ├── ServicioAlumno.php
│   └── ServicioAdministrador.php
├── modelos/
│   └── AlumnoModelo.php
└── publico/
    └── index.php         # Punto de entrada de la API
```

---

## Frontend

### Páginas

| Archivo | Descripción |
|---------|-------------|
| `index.php` | Página inicial: estudiante ingresa número de cuenta |
| `view_confirmacion.php` | Formulario para confirmar asistencia (Sí/No), elegir invitados y proporcionar correo |
| `asientos.php` | Mapa visual del teatro - muestra el asiento asignado |
| `view_admin.php` | Panel de administración: métricas, tabla de alumnos, edición, escáner QR |
| `view_registroAdmin.php` | Crear nuevos usuarios administradores |
| `bienvenida.php` | Página de bienvenida general |
| `view_teatro.php` | Vista alternativa del mapa de asientos |

### Flujo típico (Estudiante)

1. Estudiante accede a la página principal
2. Ingresa su número de cuenta (sin el último dígito)
3. El sistema valida sus datos y le da acceso
4. Si ya confirmó asistencia → ve su asiento asignado
5. Si no ha confirmado → completa el formulario de confirmación
6. Después de confirmar → puede ver su asiento

### Flujo (Administrador)

1. Accede a la página de administración
2. Inicia sesión con sus credenciales
3. Puede ver métricas, buscar y editar alumnos, escanear QR
4. Acceso al mapa de asientos general

---

## Notas de desarrollo

- Los tokens de acceso expiran en 1 hora
- El sistema detecta automáticamente si está en modo local o producción
- El día del evento, los administradores pueden escanear códigos QR para registrar la llegada de los estudiantes

---

## Acceso a la aplicación

- **Frontend**: `http://localhost/SistemaGestorDeAsientos/FrontEnd/`
- **API**: `http://localhost/SistemaGestorDeAsientos/API/publico/`
