# SistemaGestorDeAsientos

Sistema para gestionar la asistencia y asignación de asientos en ceremonias de graduación.

## Requisitos

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
   Copiar `API/.env.example` a `API/.env` y completar:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=gestion_asientos
   DB_PORT=3306
   JWT_KEY=tu_clave_secreta_jwt
   ```

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
│   ├── ConexionDB.php    # Conexión PDO a MySQL
│   └── variables.php     # Carga de variables de entorno
├── controladores/
│   ├── ControladorAlumno.php       # Lógica de estudiantes
│   ├── ControladorAdministrador.php # Lógica de admin
│   └── ControladorAsientos.php     # Gestión de asientos (reservado)
├── servicios/
│   ├── ServicioAlumno.php
│   └── ServicioAdministrador.php
├── modelos/
│   └── AlumnoModelo.php
├── publico/
│   └── index.php         # Entry point de la API
└── .env                  # Variables de entorno
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

### Archivos JS

```
FrontEnd/js/
├── script.js           # Funciones generales del frontend
└── admin/
    ├── app.js          # Lógica del panel admin (tabla, métricas, modales)
    └── modules/
        ├── api.js      # Consumos a la API
        ├── metrics.js  # Manejo de métricas
        ├── modal.js    # Lógica de modales
        ├── qrscanner.js # Escáner QR
        ├── state.js    # Estado de la aplicación
        └── table.js    # Renderizado de tabla de alumnos
```

### Flujo típico (Estudiante)

1. Estudiante accede a `index.php`
2. Ingresa número de cuenta (sin último dígito)
3. API valida y retorna JWT
4. Si ya confirmó asistencia → `asientos.php` (muestra su asiento)
5. Si no ha confirmado → `view_confirmacion.php` (formulario de confirmación)
6. Después de confirmar → redirigido a `asientos.php`

### Flujo (Administrador)

1. Accede a `view_admin.php`
2. Inicia sesión con credenciales admin
3. Puede ver métricas, buscar/editAR alumnos, escanear QR
4. Acceso al mapa de asientos en `asientos.php` (modo admin)

---

## Notas de desarrollo

- **SSL**: En desarrollo local, las peticiones cURL usan `CURLOPT_SSL_VERIFYPEER => false`
- **JWT**: Los tokens expiran en 1 hora. El payload incluye `numero_cuenta` (estudiante) o `role: admin` + `admin_id` (admin)
- **Sesiones**: El token JWT se guarda en `$_SESSION['jwt_token']` para el estudiante y `$_SESSION['admin_token']` para el admin
- **Configuración**: La URL base de la API se define en `FrontEnd/config.php` y detecta automáticamente el entorno (local/producción)

---

## Puertos típicos

- Apache: 80
- MySQL: 3306
- Acceso frontend: `http://localhost/SistemaGestorDeAsientos/FrontEnd/`
- Acceso API: `http://localhost/SistemaGestorDeAsientos/API/publico/`