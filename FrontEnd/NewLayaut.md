# Propuesta de Cambio de Layout (FrontEnd)

## Análisis de Problemas Actuales en `view_admin.php`

1. **Estructura HTML en Carta de Asientos (Error de anidación):**
   La etiqueta `<a>` que redirige a `asientos.php` está cerrándose fuera de lugar. Inicia dentro de `div.col-6` pero cierra después del mismo div, lo cual rompe el árbol DOM y puede causar problemas de clic y renderizado.

2. **Dificultad de Visualización de la Tabla en Móviles:**
   Aunque tiene una clase predeterminada de bootstrap `table-responsive`, debido a la cantidad de columnas (8 en total), en dispositivos pequeños la tabla se aplasta. Falta que la tabla aplique la clase `text-nowrap` y que la tipografía o márgenes no fuercen a textos muy aplastados. Además, se le puede dar un min-width o que las acciones queden estáticas al hacer scroll.

3. **Textos de Botones Largos en Navegación (Header):**
   En teléfonos, los botones como "Escanear QR", "Enviar QRs", "Cerrar Sesión" ocupan muchísimo espacio haciendo que el `navbar` colapse de manera no estética o se amontonen las cosas. Los textos en pantallas `< md` deberían ser más concisos, o usar iconos (`d-none d-md-inline` en los spans de texto) en dispositivos muy pequeños.

4. **Registro de Administrador:**
   Actualmente hay un botón visible que lleva a `view_registroAdmin.php` en la barra superior. Si la lógica cambia, esto debe removerse para no hacer accesible el formulario (como se solicitó).

5. **Acceso a Asientos (Gestión de Privilegios):**
   La página `asientos.php` verifica la existencia de `$_SESSION['jwt_token']` y asume tu rol dependiendo de `$_SESSION['tipo']`. En el dashboard de admins, la sesión se guarda en `$_SESSION['admin_token']`. Al navegar a `asientos.php`, el admin será redireccionado al Index si no se le asgina la sesión correcta, o lo denegará. Hay que preparar la sesión correctamente para el admin desde `view_admin.php` o manejar el pasaje del token temporalmente.

## Mejoras a Implementar a futuro
* **Sidebar o Menú Hamburguesa:** Para una vista admin con tantas métricas, un panel lateral ocultable es mejor que botones apretados en el Navbar.
* **Scroll Horizontal Bloqueado en Cabeceras:** Usar `position: sticky; right: 0;` para fijar la columna de acciones en la tabla al scrollear a los lados.
* **Loading Skeletons:** En vez del spinner genérico estancado, dar un look and feel más moderno.
* **Componentización:** Separar los Modals del HTML principal.
