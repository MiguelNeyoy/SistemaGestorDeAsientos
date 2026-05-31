# Reglas de Arquitectura Frontend: Módulo Administrador

Este documento define la arquitectura y las reglas estrictas para interactuar con el código base del panel de administración. Eres un agente de IA experto en Vanilla JS (ES6 Modules) y diseño modular basado en estado.

**Alcance:** Estas reglas aplican ÚNICAMENTE a los archivos dentro de `js/admin/` y la vista `view_admin.php`.

## 1. Estructura de Directorios Obligatoria
El código del administrador debe respetar la siguiente jerarquía:
- `js/admin/app.js`: Punto de entrada. Orquesta la inicialización e importa los módulos.
- `js/admin/core/`: Lógica central y de datos.
- `js/admin/components/`: Manipulación exclusiva del DOM.
- `js/admin/utils/`: Funciones puras de apoyo.

## 2. Reglas por Capa (Separación de Responsabilidades)

### Capa de Red (`core/api.js`)
- **Responsabilidad:** Única vía de comunicación con la API backend (`/admin/*`).
- **Restricción estricta:** PROHIBIDO tocar el DOM. No puede contener `document.getElementById`, `innerHTML` ni dependencias de UI.
- **Salida:** Solo exporta funciones asíncronas (`async/await`) que retornan promesas resolviendo objetos JSON o estados HTTP.

### Capa de Estado (`core/state.js`)
- **Responsabilidad:** Memoria a corto plazo del administrador (Single Source of Truth).
- **Contenido permitido:** Variables globales del módulo, cachés (ej. `allStudentsCache`), filtros activos y tokens.
- **Flujo:** Los componentes deben leer de esta capa para renderizar, evitando peticiones redundantes a la API.

### Capa de Componentes UI (`components/*.js`)
- **Responsabilidad:** Modificar la vista (`view_admin.php`). 
- **Restricción estricta:** Son "Dumb Components" (Componentes tontos). Reciben datos y los pintan. No realizan cálculos complejos ni peticiones `fetch`.
- **Eventos:** Los `addEventListener` correspondientes a la UI de un componente específico deben encapsularse dentro de su respectivo archivo. (Ej: La lógica del modal va en `modal.js`).

### Capa de Utilidades (`utils/helpers.js`)
- **Responsabilidad:** Transformación de datos y cálculos aislados.
- **Restricción estricta:** Funciones puras. Entra un dato, sale un dato. PROHIBIDO hacer peticiones de red o tocar el DOM. (Ej: `getGrupo()`, `formatearFecha()`).

## 3. Prevención de Bugs Específicos

- **Data Diffing (Rendimiento):** Antes de inyectar datos masivos en el DOM (como reconstruir la tabla de alumnos), el código debe validar mediante un hash o comparación de estado (`JSON.stringify`) si los datos realmente cambiaron. Si son idénticos, se aborta el renderizado para evitar parpadeos (flickering).
- **Ordenamiento (Compatibilidad iOS/WebKit):** Cualquier ordenamiento de texto que involucre nombres o apellidos debe utilizar obligatoriamente `localeCompare('es', { sensitivity: 'base' })` para evitar inversiones de listas provocadas por el motor WebKit en dispositivos móviles.
- **Cache Busting de Módulos ES6:** Si el servidor no gestiona las cabeceras de caché, las importaciones estáticas en `app.js` deben forzar la recarga mediante un parámetro de versión (`import { x } from './modulo.js?v=N'`) al realizar despliegues a producción.