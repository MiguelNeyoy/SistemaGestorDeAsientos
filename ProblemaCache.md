# Documentación: Problema de Caché en Módulos JavaScript

## 1. El Problema (¿Qué sucedió?)
Después de realizar una actualización mayor en la lógica de las métricas (cambio de Carreras/Turnos a Grupos LI4/LISI4), el sistema funcionaba perfectamente en el entorno de desarrollo, pero en dispositivos externos (móviles, otras laptops), las métricas se mostraban en "0" o desordenadas.

## 2. Causa Raíz
El problema fue el **Cacheo Agresivo de Módulos ES6**.

*   **HTML Actualizado**: Los dispositivos descargaban el nuevo archivo `view_admin.php`, por lo que veían los nuevos nombres de las tarjetas.
*   **JS Obsoleto**: El navegador, para ahorrar datos, no volvía a descargar los archivos `.js` importados dentro de los módulos si el nombre era el mismo. Seguía ejecutando la lógica vieja que buscaba IDs de métricas que ya no existían en el nuevo HTML.
*   **Encadenamiento**: Aunque el archivo principal `app.js` tuviera una versión (`?v=4`), las importaciones internas (como `import ... from './metrics.js'`) no tenían versión, por lo que el navegador usaba la copia guardada en su memoria local.

## 3. Solución Aplicada: Cache Busting Manual
La solución inmediata fue implementar **Cache Busting** añadiendo un parámetro de consulta (`query string`) a cada importación:

```javascript
// Antes
import { state } from './modules/state.js';

// Después (v5 fuerza al navegador a descargar de nuevo)
import { state } from './modules/state.js?v=5';
```

Al cambiar el nombre de la URL (añadiendo `?v=5`), el navegador lo interpreta como un archivo totalmente nuevo y descarga la versión actualizada.

## 4. Soluciones a Futuro y Herramientas

Para evitar tener que cambiar manualmente las versiones en cada archivo, existen métodos profesionales y automatizados:

### A. Bundlers (Herramientas de Compilación)
Herramientas como **Vite**, **Webpack** o **Parcel** son el estándar hoy en día.
*   **Cómo funcionan**: Al "construir" el proyecto para producción (`build`), estas herramientas leen el contenido de tus archivos y generan un nombre único basado en un hash (ej: `app.a1b2c3d4.js`).
*   **Ventaja**: Si el archivo cambia aunque sea una coma, el nombre cambia. Si no cambia, el nombre se mantiene y se aprovecha la caché. Es 100% automático.

### B. Configuración del Servidor (Headers)
Configurar el servidor web (Apache o Nginx) para que envíe cabeceras de `Cache-Control` más estrictas para archivos `.js`.
*   Ejemplo en `.htaccess` (Apache):
    ```apache
    <FilesMatch "\.(js|css)$">
        Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
    </FilesMatch>
    ```
*   *Nota: Esto puede hacer la carga un poco más lenta ya que siempre descargará los archivos.*

### C. Versionamiento por PHP (Semi-automático)
Si no deseas usar un compilador como Vite, puedes usar PHP para generar la versión basada en la fecha de modificación del archivo:

```php
<script type="module" src="app.js?v=<?php echo filemtime('app.js'); ?>"></script>
```
Esto hará que `v=` cambie automáticamente cada vez que guardes cambios en el archivo.

## 5. Mejores Prácticas Recomendadas
1. **Desarrollo**: Mantener las herramientas de desarrollador abiertas con la opción "Disable Cache" activada.
2. **Producción**: Siempre que se haga un cambio en la estructura del HTML que dependa de IDs en JS, se **debe** subir la versión del script.
3. **Migración**: Considerar migrar el FrontEnd a **Vite** en el futuro para manejar estas dependencias de forma profesional.
