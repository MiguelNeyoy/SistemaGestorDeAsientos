# Filtro de Asientos por Evento - Documentación Técnica

## Resumen Ejecutivo

Este documento detalha la implementación del sistema de filtrado y ordenamiento de asientos por evento (LI y LISI) en el panel de administrador del Sistema Gestor de Asientos.

---

## 1. Contexto y Problema Inicial

### 1.1 Estructura de la Base de Datos

El sistema cuenta con dos eventos separados:
- **LI** (Licenciatura en Informática)
- **LISI** (Licenciatura en Ingeniería en Sistemas)

Cada evento tiene su propia tabla de asientos:
- `asiento_evento_li`
- `asiento_evento_lisi`

### 1.2 Problema Identificado

Cuando el administrador queria ver los asientos de un evento específico:
1. No existia forma de filtrar la tabla de alumnos
2. Los asientos no se mostraban correlacionados con los Alumnos
3. El orden era alfabético por nombre, no por número de asiento

---

## 2. Análisis del Backend

### 2.1 Endpoints del API

**Ruta:** `GET /asientos/mapa/{evento}`

| Parámetro | Valor | Descripción |
|----------|-------|-------------|
| evento | `li` o `lisi` | Identificador del evento |

**Respuesta del Backend (anterior):**
```json
{
  "success": true,
  "data": {
    "asientos": [
      {
        "id_asiento": "A1",
        "fila": "A",
        "numero": 1,
        "estado": "ocupado",
        "asignado": false
      }
    ]
  }
}
```

**Problema:** No incluía `numCuenta` → No se podia mapear asiento a alumno.

### 2.2 ServicioAsientos.php (Modificado)

**Ubicación:** `API/servicios/ServicioAsientos.php`

**Cambio realizado:** Agregar `numCuenta` cuando el solicitante es admin.

```php
// Solo incluir numCuenta si hay admin pidiendo (para mapear en frontend)
if ($jwtAdminId !== null && $tieneNumCuenta) {
    $dataAsiento['numCuenta'] = $asiento['numCuenta'];
}
```

**Respuesta actualizada:**
```json
{
  "success": true,
  "data": {
    "asientos": [
      {
        "id_asiento": "A1",
        "fila": "A",
        "numero": 1,
        "estado": "ocupado",
        "asignado": false,
        "numCuenta": "0154847"  // <-- Nuevo campo
      }
    ]
  }
}
```

---

## 3. Implementación del Frontend

### 3.1 API Integration (api.js)

**Ubicación:** `FrontEnd/js/admin/modules/api.js`

**Cambio:** Llamar a ambos eventos y combinar resultados.

```javascript
// Llamada a evento LI
fetch(`${window.BASE_API_URL}/asientos/mapa/li`, {...})

// Llamada a evento LISI  
fetch(`${window.BASE_API_URL}/asientos/mapa/lisi`, {...})
```

**Propósito:** Obtener todos los asientos de ambos eventos para el mapeo.

### 3.2 Mapeo de Asientos (app.js)

**Ubicación:** `FrontEnd/js/admin/app.js`

**Flujo:**
1. Receive response from both API endpoints
2. Create Map: `numCuenta → id_asiento`
3. Asignar a cada alumno su asiento

```javascript
const seatMap = new Map();

// Procesar asientos LI
asientosLiData.data.asientos.forEach(s => {
    if (s.numCuenta) {
        seatMap.set(s.numCuenta.toString(), s.id_asiento);
    }
});

// Asignar al alumno
al.asiento = seatMap.get(String(al.numCuenta)) || "-";
```

### 3.3 Filtrado por Evento (table.js)

**Ubicación:** `FrontEnd/js/admin/modules/table.js`

**Filtros implementados:**

```javascript
// Filtros por evento (LI o LISI)
if (state.currentFilterType === 'LI' || state.currentFilterType === 'LISI') {
    const carLower = (al.carrera || '').toLowerCase();
    
    if (state.currentFilterType === 'LI') {
        // LI = Informática
        if (!carLower.includes('informática') && !carLower.includes('informatica')) {
            return false;
        }
    }
    
    if (state.currentFilterType === 'LISI') {
        // LISI = Ingeniería en Sistemas
        if (!carLower.includes('ingeniería') && !carLower.includes('sistemas')) {
            return false;
        }
    }
}
```

**Detección de evento por carrera:**
| Carrera | Evento |
|---------|--------|
| contains "informática" o "informatica" | LI |
| contains "ingeniería" o "sistemas" | LISI |

### 3.4 Ordenamiento por Asiento (table.js + app.js)

**Lógica de ordenamiento:**

```javascript
// 1. Si no tiene asiento, ir al final
if (seatA === "-" && seatB === "-") return 0;
if (seatA === "-") return 1;
if (seatB === "-") return -1;

// 2. Extraer letra y número
const letraA = seatA.charAt(0);
const letraB = seatB.charAt(0);
const numA = parseInt(seatA.substring(1)) || 0;
const numB = parseInt(seatB.substring(1)) || 0;

// 3. Comparar letras primero, luego números
if (letraA !== letraB) return letraA.localeCompare(letraB);
return numA - numB;
```

**Resultado:** A1, A2, A3 ... B1, B2, B3 ...

### 3.5 Interfaz de Usuario (sidebar.php)

**Ubicación:** `FrontEnd/admin/partials/_sidebar.php`

**Filtros de evento agregados:**

```html
<section class="admin-sidebar__section--collapsible">
    <div class="admin-sidebar__section-header">
        <p class="admin-sidebar__section-title">Evento</p>
    </div>
    <ul class="admin-sidebar__list">
        <li class="admin-sidebar__item">
            <a href="javascript:void(0)" 
               onclick="window.setFilterType('LI')">
                LI (Informática)
            </a>
        </li>
        <li class="admin-sidebar__item">
            <a href="javascript:void(0)" 
               onclick="window.setFilterType('LISI')">
                LISI (Sistemas)
            </a>
        </li>
    </ul>
</section>
```

**Nota:** Se usó `href="javascript:void(0)"` para evitar scroll unwanted al hacer click.

---

## 4. Archivos Modificados

| Archivo | Cambio |
|---------|--------|
| `API/servicios/ServicioAsientos.php` | Agregar numCuenta para admin |
| `FrontEnd/admin/partials/_sidebar.php` | Agregar filtros LI/LISI |
| `FrontEnd/js/admin/app.js` | Mapeo + ordenamiento por asiento |
| `FrontEnd/js/admin/modules/table.js` | Filtrado + ordenamiento por evento |

---

## 5. Comportamiento Esperado

### 5.1 Sin filtro (Dashboard General)
- Muestra todos los alumnos de ambos eventos
- Ordenado alfabéticamente por apellido

### 5.2 Click en "LI"
- Muestra solo alumnos de LI (Informática)
- Ordenado por número de asiento: A1, A2, B1, B2, ...

### 5.3 Click en "LISI"
- Muestra solo alumnos de LISI (Sistemas)
- Ordenado por número de asiento: A1, A2, B1, B2, ...

---

## 6. Pruebas Realizadas

1. ✅ Click en "LI" filtra correctamente
2. ✅ Click en "LISI" filtra correctamente
3. ✅ Los asientos se muestran en la columna "Asiento"
4. ✅ El ordenamiento es por asiento (no por nombre)
5. ✅ No hay scroll extraño al hacer click

---

## 7. Pendientes / Futuras Mejoras

- [ ] Agregar selector de evento en vista de mapa de asientos (asientos.php)
- [ ] Optimizar llamadas API (evitar llamar a ambos eventos siempre)
- [ ] Cache de asientos para evitar llamadas repetitivas
- [ ] Agregar "Todos" como filtro de evento

---

## 8. Referencias

- Commit: `8fcdfb9` - feat: filtrar y ordenar asientos por evento en admin
- Documentación API: `/API/publico/index.php`
- Modelos: `API/modelos/ModeloAsiento.php`, `API/modelos/AlumnoModelo.php`