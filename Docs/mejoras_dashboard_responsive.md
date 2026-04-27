# Documentación Técnica: Optimización y Responsive del Dashboard

Esta documentación detalla los cambios realizados en el sistema gestor de asientos para mejorar la experiencia de usuario, la adaptabilidad móvil y la robustez del panel administrativo.

## 1. Mejoras de Interfaz y Experiencia (UI/UX)

### Optimización del Reloj (Eliminación de Parpadeo)
- **Archivo:** `FrontEnd/js/admin/app.js`
- **Qué soluciona:** Anteriormente, la actualización del reloj cada segundo disparaba indicadores de carga visuales que hacían que el dashboard "parpadeara".
- **Cambio:** Se aisló la lógica de actualización del `DOM` del reloj para que sea independiente del flujo de carga de datos, eliminando cualquier efecto visual molesto.

### Limpieza de Componentes Obsoletos
- **Cambio:** Se retiró la tarjeta "Ver Mapa" que no tenía funcionalidad útil en ese contexto, simplificando la vista principal de métricas.
- **Ajuste de Fuentes:** Se estandarizó el tamaño de fuente de los correos electrónicos en la tabla para que coincida con el resto de los datos, mejorando la jerarquía visual.

---

## 2. Integración de Asientos Reales

### Obtención de Datos
- **Archivo:** `FrontEnd/js/admin/modules/api.js`
- **Función:** `fetchDashboardData()`
- **Por qué:** El listado de alumnos no incluye el asiento por defecto. Se añadió una llamada paralela al endpoint `/asientos/mapa` para obtener la asignación actual.

### Mapeo de Información
- **Archivo:** `FrontEnd/js/admin/app.js`
- **Lógica:** Se creó un `Map` (estructura de datos de alto rendimiento) llamado `seatMap` que indexa los asientos por número de cuenta.
- **Qué soluciona:** Permite inyectar el número de asiento (ej. "A12") en el objeto de cada alumno en milisegundos, permitiendo que la tabla lo muestre sin realizar múltiples peticiones.

---

## 3. Dashboard Responsive (Estilo MVAdmin1.2.5)

### Transformación de Tabla a Tarjetas (Cards)
- **Archivo:** `FrontEnd/css/admin/responsive.css` (Nuevo)
- **Por qué:** En pantallas pequeñas (móviles), las tablas con muchas columnas son ilegibles.
- **Solución:** Se implementó una técnica de CSS donde cada fila (`<tr>`) se convierte en una tarjeta visual. Las celdas (`<td>`) se apilan verticalmente y utilizan el atributo `data-label` para mostrar el nombre de la columna a la izquierda y el valor a la derecha.

### Atributos de Datos Dinámicos
- **Archivo:** `FrontEnd/js/admin/modules/table.js`
- **Cambio:** Se añadió el atributo `data-label` (ej. `data-label="Nombre"`) a cada celda generada por JavaScript.
- **Qué soluciona:** Permite que el CSS identifique qué dato está mostrando cada celda para poder etiquetarlo correctamente en la vista de tarjeta móvil.

---

## 4. Resiliencia y Manejo de Errores

### Peticiones No Bloqueantes
- **Archivo:** `FrontEnd/js/admin/modules/api.js`
- **Cambio:** Se separó la petición de asientos de las métricas principales. Se añadió un `AbortController` con un **timeout de 5 segundos**.
- **Qué soluciona:** Si el servidor de asientos tarda en responder o falla, el Dashboard **no se queda colgado**. Simplemente muestra un guion ("-") en el asiento y permite al administrador seguir trabajando con el resto de los datos.

### Robustez en el Renderizado
- **Archivo:** `FrontEnd/js/admin/app.js`
- **Cambio:** Se movió la función `renderTable()` fuera de los bloques condicionales de éxito.
- **Qué soluciona:** Evita que el dashboard se quede eternamente en "Cargando..." si la API devuelve una lista vacía o un error controlado. Ahora la UI siempre se refresca para mostrar el estado actual.
