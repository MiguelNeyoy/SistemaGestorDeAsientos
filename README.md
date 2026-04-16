# SistemaGestorDeAsientos

## ¿Qué es?

Es una aplicación web para gestionar la ceremonia de graduación de la Facultad de Informática. Ayuda a organizar quién asiste, cuántos invitados trae, y dónde se sienta cada persona.

---

## ¿Quién lo usa?

### Estudiantes
- **Confirmar asistencia**: Indican si irán a la graduación.
- **Registrar invitados**: Pueden llevar hasta 6 personas.
- **Actualizar correo**: Para recibir información del evento.
- **Ver su asiento**: Pueden ver en un mapa dónde les tocó sentarse.

### Administradores
- **Ver estadísticas**: Cuántos confirmó, cuántos invitados, distribución por carrera y turno.
- **Buscar alumnos**: Encontrar y editar información de estudiantes.
- **Escanear códigos QR**: El día del evento, registrar quién llegó escaneando su código.
- **Enviar códigos QR**: Enviar los códigos por correo a los alumnos.
- **Ver mapa de asientos**: Ver qué lugares están ocupados.

---

## Requisitos

- Servidor web (XAMPP, LAMP o similar)
- PHP 8.x
- Base de datos MySQL

---

## Instalación

1. Abrir terminal en la carpeta del proyecto y ejecutar:
   ```bash
   composer install
   cd API && composer install
   ```

2. Crear la base de datos MySQL e importar el archivo SQL.

3. Configurar la conexión a la base de datos (DB_HOST, DB_USER, etc.).

4. Iniciar Apache y MySQL desde XAMPP o LAMP.

---

## Páginas principales

| Página | ¿Quién la usa? | Descripción |
|--------|----------------|-------------|
| `index.php` | Estudiantes | Ingresar número de cuenta |
| `view_confirmacion.php` | Estudiantes | Confirmar asistencia e invitados |
| `asientos.php` | Estudiantes | Ver el mapa del teatro con tu asiento |
| `loginAdmin.php` | Administradores | Iniciar sesión |
| `view_admin.php` | Administradores | Panel de control con métricas y herramientas |

---

## ¿Cómo funciona?

### Flujo del estudiante

1. Entra a la página principal
2. Ingresa su número de cuenta (sin el último dígito)
3. Si ya confirmó → ve su asiento asignado
4. Si no ha confirmado → llena el formulario de asistencia
5. Después de confirmar → puede ver su lugar en el teatro

### Flujo del administrador

1. Entra a la página de login
2. Ingresa sus credenciales
3. Ve las estadísticas del evento
4. Puede buscar, editar alumnos, escanear QR o ver el mapa de asientos

---

## Notas

- Los códigos QR contienen un token de seguridad para validar la identidad.
- El panel de administración se actualiza automáticamente cada 5 segundos.
- Los tokens de acceso expiran después de un tiempo por seguridad.

---

## Acceso

- **Aplicación**: `Ruta establecida en el servidor`
- **API**: `Ruta establecida en el servidor`
