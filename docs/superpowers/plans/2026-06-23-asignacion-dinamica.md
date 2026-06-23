# Asignación Dinámica de Asientos — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement dynamic seat assignment with dry-run preview, batch processing, and publish toggle

**Architecture:** Backend PHP with PDO transactions, batch processing in chunks of 50, frontend modal-driven flow with API polling for progress feedback

**Tech Stack:** PHP 8+, MySQL/PDO, vanilla JS, Bootstrap modals

## Global Constraints

- Table `asiento_evento_li` and `asiento_evento_lisi` are separate — never cross-assign
- Student event determined by carrera: "Informática" → li, else → lisi
- Order: turno Matutino (M/1) first, then Vespertino (V/2), then apellido ASC, nombre ASC
- Seat order: letra ASC, numero ASC
- Batch size: 50 updates per transaction
- Dry-run mode executes all logic but rolls back or skips writes
- All admin endpoints require JWT with role: admin
- Colors: student view — gray = occupied, blue = my seat; admin — unchanged (blue = scanned QR)

---

### Task 1: Modelo / DB — nuevos métodos en ModeloAsiento

**Files:**
- Modify: `API/modelos/ModeloAsiento.php` — add new methods
- Create: DB migration (SQL to create `config_asignacion` table)

**Interfaces:**
- Produces: `ModeloAsiento::limpiarTabla(string $tabla): bool`
- Produces: `ModeloAsiento::obtenerAsientosDisponibles(string $tabla): array`
- Produces: `ModeloAsiento::obtenerAlumnosConfirmadosPorEvento(): array`
- Produces: `ModeloAsiento::asignarBatch(string $tabla, array $asignaciones): bool`
- Produces: `ModeloAsiento::guardarFechaAsignacion(): void`
- Produces: `ModeloAsiento::obtenerEstadoConfig(): array`
- Produces: `ModeloAsiento::actualizarPublicado(bool $publicado): void`

- [ ] **Step 1: Write the config table SQL**

```sql
CREATE TABLE IF NOT EXISTS config_asignacion (
    id INT PRIMARY KEY DEFAULT 1,
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_asignacion DATETIME DEFAULT NULL,
    fecha_publicacion DATETIME DEFAULT NULL
);

-- Insert default row
INSERT INTO config_asignacion (id, publicado, fecha_asignacion, fecha_publicacion)
VALUES (1, 0, NULL, NULL)
ON DUPLICATE KEY UPDATE id=id;
```

Save to `API/configuracion/migracion_config_asignacion.sql`

- [ ] **Step 2: Read ModeloAsiento.php to understand existing patterns**

- [ ] **Step 3: Add `limpiarTabla` method**

```php
public function limpiarTabla($tabla)
{
    try {
        $tabla = $this->validarTabla($tabla);
        $sql = "UPDATE {$tabla} SET numCuenta = NULL, estado = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Error al limpiar tabla: " . $e->getMessage());
    }
}
```

- [ ] **Step 4: Add `obtenerAsientosDisponibles` method**

```php
public function obtenerAsientosDisponibles($tabla)
{
    try {
        $tabla = $this->validarTabla($tabla);
        $sql = "SELECT idAsiento FROM {$tabla} ORDER BY letra ASC, numero ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener asientos: " . $e->getMessage());
    }
}
```

- [ ] **Step 5: Add `obtenerAlumnosConfirmadosPorEvento` method**

```php
public function obtenerAlumnosConfirmadosPorEvento()
{
    try {
        $sql = "SELECT a.numCuenta, a.nombre, a.apellido, a.turno, a.carrera
                FROM alumno a
                JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                WHERE asi.estado = 1
                ORDER BY
                    CASE WHEN UPPER(a.turno) IN ('M', '1') THEN 0 ELSE 1 END ASC,
                    a.apellido ASC,
                    a.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener confirmados: " . $e->getMessage());
    }
}
```

- [ ] **Step 6: Add `asignarBatch` method**

```php
public function asignarBatch($tabla, $asignaciones)
{
    try {
        $tabla = $this->validarTabla($tabla);
        $this->db->beginTransaction();
        $sql = "UPDATE {$tabla} SET numCuenta = ? WHERE idAsiento = ?";
        $stmt = $this->db->prepare($sql);
        foreach ($asignaciones as $asig) {
            $stmt->execute([$asig['numCuenta'], $asig['idAsiento']]);
        }
        $this->db->commit();
        return true;
    } catch (PDOException $e) {
        $this->db->rollBack();
        throw new Exception("Error en batch de asignación: " . $e->getMessage());
    }
}
```

- [ ] **Step 7: Add config table methods**

```php
public function guardarFechaAsignacion()
{
    $sql = "INSERT INTO config_asignacion (id, publicado, fecha_asignacion)
            VALUES (1, 0, NOW())
            ON DUPLICATE KEY UPDATE fecha_asignacion = NOW()";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute();
}

public function obtenerEstadoConfig()
{
    $sql = "SELECT publicado, fecha_asignacion, fecha_publicacion FROM config_asignacion WHERE id = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: ['publicado' => 0, 'fecha_asignacion' => null, 'fecha_publicacion' => null];
}

public function actualizarPublicado($publicado)
{
    $sql = "INSERT INTO config_asignacion (id, publicado, fecha_publicacion)
            VALUES (1, ?, NOW())
            ON DUPLICATE KEY UPDATE publicado = ?, fecha_publicacion = IF(? = 1, NOW(), fecha_publicacion)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$publicado, $publicado, $publicado]);
}
```

- [ ] **Step 8: Run test to verify**

Run a quick PHP syntax check: `php -l API/modelos/ModeloAsiento.php`

- [ ] **Step 9: Commit**

```bash
git add API/modelos/ModeloAsiento.php API/configuracion/migracion_config_asignacion.sql
git commit -m "feat(model): add batch assignment and config table methods"
```

---

### Task 2: Servicio / Algoritmo — ServicioAsientos

**Files:**
- Modify: `API/servicios/ServicioAsientos.php` — add algorithm methods

**Interfaces:**
- Consumes: `ModeloAsiento` methods from Task 1
- Produces: `ServicioAsientos::ejecutarAsignacion(bool $dryRun): array`
- Produces: `ServicioAsientos::limpiarAsignaciones(): array`
- Produces: `ServicioAsientos::obtenerEstadoAsignacion(): array`
- Produces: `ServicioAsientos::publicarResultados(bool $publicado): array`

- [ ] **Step 1: Read existing ServicioAsientos.php to understand patterns**

- [ ] **Step 2: Add `ejecutarAsignacion` method**

```php
public function ejecutarAsignacion($dryRun = false)
{
    try {
        $alumnos = $this->modelo->obtenerAlumnosConfirmadosPorEvento();
        if (empty($alumnos)) {
            return $this->respuesta(false, "No hay alumnos confirmados para asignar", 400);
        }

        // Separar alumnos por evento
        $porEvento = ['li' => [], 'lisi' => []];
        foreach ($alumnos as $al) {
            $evento = $this->determinarEvento($al['carrera']);
            $porEvento[$evento][] = $al;
        }

        $resultado = ['li' => 0, 'lisi' => 0, 'sin_asiento' => 0];

        if (!$dryRun) {
            $this->modelo->limpiarTabla('asiento_evento_li');
            $this->modelo->limpiarTabla('asiento_evento_lisi');
        }

        foreach (['li', 'lisi'] as $evento) {
            $alumnosEvento = $porEvento[$evento];
            if (empty($alumnosEvento)) continue;

            $tabla = "asiento_evento_" . $evento;
            $asientos = $this->modelo->obtenerAsientosDisponibles($tabla);

            $totalAsignar = min(count($alumnosEvento), count($asientos));
            $resultado[$evento] = $totalAsignar;
            $resultado['sin_asiento'] += count($alumnosEvento) - count($asientos);

            if ($dryRun) continue;

            // Batch processing: chunks of 50
            $indice = 0;
            for ($i = 0; $i < $totalAsignar; $i += 50) {
                $batch = [];
                $limite = min($i + 50, $totalAsignar);
                for ($j = $i; $j < $limite; $j++) {
                    $batch[] = [
                        'numCuenta' => $alumnosEvento[$j]['numCuenta'],
                        'idAsiento' => $asientos[$j]['idAsiento']
                    ];
                }
                $this->modelo->asignarBatch($tabla, $batch);
            }
        }

        if (!$dryRun) {
            $this->modelo->guardarFechaAsignacion();
        }

        $resultado['dry_run'] = $dryRun;
        return $this->respuesta(true, $dryRun ? "Vista previa completada" : "Asignación completada", 200, $resultado);

    } catch (Exception $e) {
        return $this->respuesta(false, "Error en asignación: " . $e->getMessage(), 500);
    }
}

private function determinarEvento($carrera)
{
    $carLower = strtolower(trim($carrera));
    if (strpos($carLower, 'informática') !== false || strpos($carLower, 'informatica') !== false) {
        return 'li';
    }
    return 'lisi';
}
```

- [ ] **Step 3: Add `limpiarAsignaciones` method**

```php
public function limpiarAsignaciones()
{
    try {
        $this->modelo->limpiarTabla('asiento_evento_li');
        $this->modelo->limpiarTabla('asiento_evento_lisi');
        return $this->respuesta(true, "Asignaciones limpiadas correctamente", 200);
    } catch (Exception $e) {
        return $this->respuesta(false, "Error al limpiar: " . $e->getMessage(), 500);
    }
}
```

- [ ] **Step 4: Add `obtenerEstadoAsignacion` method**

```php
public function obtenerEstadoAsignacion()
{
    try {
        $config = $this->modelo->obtenerEstadoConfig();

        $alumnos = $this->modelo->obtenerAlumnosConfirmadosPorEvento();
        $confirmados = count($alumnos);

        // Contar asientos por evento
        $asientosLi = $this->modelo->obtenerAsientosDisponibles('asiento_evento_li');
        $asientosLisi = $this->modelo->obtenerAsientosDisponibles('asiento_evento_lisi');
        $capacidad = count($asientosLi) + count($asientosLisi);

        // Contar asignados
        $asignadosLi = $this->contarAsignados('asiento_evento_li');
        $asignadosLisi = $this->contarAsignados('asiento_evento_lisi');

        $tieneAsignacion = ($config['fecha_asignacion'] !== null);

        $data = [
            'asignado' => $tieneAsignacion,
            'fecha_asignacion' => $config['fecha_asignacion'],
            'publicado' => (bool)$config['publicado'],
            'confirmados' => $confirmados,
            'capacidad' => $capacidad,
            'asignados_li' => $asignadosLi,
            'asignados_lisi' => $asignadosLisi
        ];

        return $this->respuesta(true, "Estado obtenido", 200, $data);
    } catch (Exception $e) {
        return $this->respuesta(false, "Error al obtener estado: " . $e->getMessage(), 500);
    }
}

private function contarAsignados($tabla)
{
    $sql = "SELECT COUNT(*) as total FROM {$tabla} WHERE numCuenta IS NOT NULL";
    $stmt = $this->modelo->getDb()->prepare($sql);
    $stmt->execute();
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
}
```

Note: `contarAsignados` needs access to the PDO connection. Add a `getDb()` method to `ModeloAsiento` or make the query method there.

- [ ] **Step 5: Add `publicarResultados` method**

```php
public function publicarResultados($publicado)
{
    try {
        $this->modelo->actualizarPublicado($publicado ? 1 : 0);
        $msg = $publicado ? "Resultados publicados" : "Resultados ocultados";
        return $this->respuesta(true, $msg, 200);
    } catch (Exception $e) {
        return $this->respuesta(false, "Error al publicar: " . $e->getMessage(), 500);
    }
}
```

- [ ] **Step 6: Add `getDb()` to ModeloAsiento for counting queries**

Add to ModeloAsiento.php:
```php
public function getDb()
{
    return $this->db;
}
```

- [ ] **Step 7: PHP syntax check**

Run: `php -l API/modelos/ModeloAsiento.php && php -l API/servicios/ServicioAsientos.php`

- [ ] **Step 8: Commit**

```bash
git add API/servicios/ServicioAsientos.php API/modelos/ModeloAsiento.php
git commit -m "feat(service): add dynamic seat assignment algorithm with dry-run"
```

---

### Task 3: API Routes + Controller

**Files:**
- Modify: `API/publico/index.php` — add new routes
- Modify: `API/controladores/ControladorAdmin.php` — add handler methods

**Interfaces:**
- Consumes: `ServicioAsientos` methods from Task 2
- Produces: Route handlers for 4 new endpoints

- [ ] **Step 1: Read existing index.php and ControladorAdmin.php**

- [ ] **Step 2: Add routes to index.php**

After existing admin routes, add:
```php
$router->addRoute('POST', '/admin/asignacion/limpiar', ['ControladorAdmin', 'limpiarAsignaciones']);
$router->addRoute('POST', '/admin/asignacion/ejecutar', ['ControladorAdmin', 'ejecutarAsignacion']);
$router->addRoute('GET', '/admin/asignacion/estado', ['ControladorAdmin', 'estadoAsignacion']);
$router->addRoute('POST', '/admin/asignacion/publicar', ['ControladorAdmin', 'publicarResultados']);
```

- [ ] **Step 3: Add handler methods to ControladorAdmin.php**

```php
public function limpiarAsignaciones($request)
{
    $servicio = new ServicioAsientos();
    $resultado = $servicio->limpiarAsignaciones();
    echo json_encode($resultado);
}

public function ejecutarAsignacion($request)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $dryRun = isset($data['dry_run']) ? (bool)$data['dry_run'] : false;
    $servicio = new ServicioAsientos();
    $resultado = $servicio->ejecutarAsignacion($dryRun);
    echo json_encode($resultado);
}

public function estadoAsignacion($request)
{
    $servicio = new ServicioAsientos();
    $resultado = $servicio->obtenerEstadoAsignacion();
    echo json_encode($resultado);
}

public function publicarResultados($request)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $publicado = isset($data['publicado']) ? (bool)$data['publicado'] : false;
    $servicio = new ServicioAsientos();
    $resultado = $servicio->publicarResultados($publicado);
    echo json_encode($resultado);
}
```

- [ ] **Step 4: Ensure JWT middleware protects these routes**

Add the routes to the admin-protected section if not already. Check how existing `/admin/*` routes are grouped.

- [ ] **Step 5: PHP syntax check**

Run: `php -l API/publico/index.php && php -l API/controladores/ControladorAdmin.php`

- [ ] **Step 6: Commit**

```bash
git add API/publico/index.php API/controladores/ControladorAdmin.php
git commit -m "feat(api): add dynamic assignment endpoints"
```

---

### Task 4: Frontend Admin — Sidebar + Modales

**Files:**
- Modify: `FrontEnd/admin/partials/_sidebar.php` — add buttons
- Modify: `FrontEnd/admin/view_admin.php` — add modal HTML
- Modify: `FrontEnd/admin/admin.css` — add styles for modals and switch

**Interfaces:**
- Consumes: HTML structure from existing admin layout
- Produces: Button elements with IDs, modal containers, CSS classes

- [ ] **Step 1: Read _sidebar.php to find insertion point**

Insert new buttons after the "Resetear Confirmaciones" button (around line 59).

- [ ] **Step 2: Add sidebar buttons**

```html
<div class="sidebar-divider"></div>
<button id="btnLimpiarAsignaciones" class="btn-sidebar-action btn-warning">
    <i class="fas fa-eraser"></i> Limpiar asignaciones
</button>
<button id="btnAsignarAsientos" class="btn-sidebar-action btn-primary">
    <i class="fas fa-chair"></i> Asignar asientos
</button>
<div class="sidebar-switch-item">
    <span><i class="fas fa-eye"></i> Publicar resultados</span>
    <label class="switch">
        <input type="checkbox" id="switchPublicar">
        <span class="slider round"></span>
    </label>
</div>
```

- [ ] **Step 3: Add vista previa modal to view_admin.php**

```html
<!-- Modal Vista Previa -->
<div class="modal fade" id="modalVistaPrevia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista previa de asignación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="vistaPreviaBody">
        <p>Cargando...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmarAsignacion">Asignar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Progreso -->
<div class="modal fade" id="modalProgreso" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignando asientos...</h5>
      </div>
      <div class="modal-body text-center" id="progresoBody">
        <div class="spinner-border text-primary mb-3" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="progress mb-3">
          <div class="progress-bar progress-bar-striped progress-bar-animated" id="barraProgreso" style="width: 0%"></div>
        </div>
        <p id="progresoTexto">Procesando...</p>
      </div>
    </div>
  </div>
</div>
```

- [ ] **Step 4: Add CSS for switch toggle**

Add to admin.css:
```css
.sidebar-switch-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 15px;
    color: #ccc;
    font-size: 14px;
}
.switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #555;
    transition: .3s;
    border-radius: 24px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .slider { background-color: #4CAF50; }
input:checked + .slider:before { transform: translateX(20px); }
```

- [ ] **Step 5: Commit**

```bash
git add FrontEnd/admin/partials/_sidebar.php FrontEnd/admin/view_admin.php FrontEnd/admin/admin.css
git commit -m "feat(admin-ui): add sidebar buttons and modals for dynamic assignment"
```

---

### Task 5: Frontend Admin — JS lógica

**Files:**
- Modify: `FrontEnd/js/admin/app.js` — add handler logic

**Interfaces:**
- Consumes: Modal HTML IDs from Task 4
- Produces: Button click handlers, API calls, progress modal

- [ ] **Step 1: Read existing app.js to understand patterns**

- [ ] **Step 2: Add limpiar handler**

```javascript
document.getElementById('btnLimpiarAsignaciones')?.addEventListener('click', function() {
    if (!confirm('¿Seguro de limpiar todas las asignaciones? Los alumnos perderán su asiento.')) return;

    apiPost('/admin/asignacion/limpiar', {})
        .then(res => {
            if (res.success) {
                showAlert('Asignaciones limpiadas', 'success');
                refreshData();
            } else {
                showAlert(res.message, 'danger');
            }
        });
});
```

- [ ] **Step 3: Add asignar handler (dry-run → confirm → execute)**

```javascript
document.getElementById('btnAsignarAsientos')?.addEventListener('click', function() {
    // First: dry run
    apiPost('/admin/asignacion/ejecutar', { dry_run: true })
        .then(res => {
            if (!res.success) {
                showAlert(res.message, 'danger');
                return;
            }
            const d = res.data;
            document.getElementById('vistaPreviaBody').innerHTML = `
                <p>Se asignarán:</p>
                <ul class="list-unstyled">
                    <li><strong>LI:</strong> ${d.li} alumnos → ${d.li} asientos</li>
                    <li><strong>LISI:</strong> ${d.lisi} alumnos → ${d.lisi} asientos</li>
                </ul>
                ${d.sin_asiento > 0 ? `<div class="alert alert-warning">${d.sin_asiento} alumno(s) sin asiento disponible</div>` : ''}
                <p class="text-muted mb-0">Total: ${d.li + d.lisi} asientos a asignar</p>
            `;
            const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
            modal.show();
        });
});

document.getElementById('btnConfirmarAsignacion')?.addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('modalVistaPrevia')).hide();

    const modalProgreso = new bootstrap.Modal(document.getElementById('modalProgreso'));
    modalProgreso.show();

    apiPost('/admin/asignacion/ejecutar', { dry_run: false })
        .then(res => {
            if (res.success) {
                const d = res.data;
                document.getElementById('barraProgreso').style.width = '100%';
                document.getElementById('progresoTexto').innerHTML =
                    `<span class="text-success">✓ Asignación completada</span><br>
                     LI: ${d.li} asientos | LISI: ${d.lisi} asientos`;
                refreshData();
            } else {
                document.getElementById('progresoTexto').innerHTML =
                    `<span class="text-danger">✗ Error: ${res.message}</span>`;
            }
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('modalProgreso'))?.hide();
            }, 2500);
        });
});
```

- [ ] **Step 4: Add publicar switch handler**

```javascript
document.getElementById('switchPublicar')?.addEventListener('change', function() {
    apiPost('/admin/asignacion/publicar', { publicado: this.checked })
        .then(res => {
            if (res.success) {
                showAlert(res.message, 'success');
            } else {
                this.checked = !this.checked;
                showAlert(res.message, 'danger');
            }
        })
        .catch(() => {
            this.checked = !this.checked;
        });
});
```

- [ ] **Step 5: Initialize switch state on page load**

```javascript
// Inside the existing initialization
apiGet('/admin/asignacion/estado')
    .then(res => {
        if (res.success && res.data) {
            document.getElementById('switchPublicar').checked = res.data.publicado;
        }
    });
```

- [ ] **Step 6: Commit**

```bash
git add FrontEnd/js/admin/app.js
git commit -m "feat(admin-js): add assignment flow with dry-run, progress modal, publish toggle"
```

---

### Task 6: Frontend Alumno — Vista Condicional

**Files:**
- Modify: `FrontEnd/view_confirmacion.php` — conditional message after confirm
- Modify: `FrontEnd/asientos.php` — pass publicado flag to JS
- Modify: `FrontEnd/js/asientos.js` — conditional rendering based on publicado

**Interfaces:**
- Consumes: API response including `asignacion_publicada` flag
- Produces: Conditional UI states for student

- [ ] **Step 1: Read view_confirmacion.php to find confirmation success message**

After the student confirms, if not published show: "Tu asiento se asignará al cerrar el registro"

- [ ] **Step 2: Read asientos.php to see how data is fetched**

Add a check for `asignacion_publicada` in the API response. The API already returns `mi_asiento`, `mi_grupo`, `asientos`. We need to add `asignacion_publicada` to this response.

In `ServicioAsientos::obtenerMapaAsientos()`, add:
```php
$config = $this->modelo->obtenerEstadoConfig();
$asignacionPublicada = (bool)($config['publicado'] ?? false);
```

And in the response:
```php
'asignacion_publicada' => $asignacionPublicada,
```

- [ ] **Step 3: In asientos.php, pass the flag to JS**

After fetching the API response, pass it:
```php
$asignacionPublicada = $data['data']['asignacion_publicada'] ?? false;
```

And in the JS data embed:
```javascript
window.__ASIGNACION_PUBLICADA__ = <?php echo json_encode($asignacionPublicada); ?>;
```

- [ ] **Step 4: In asientos.js, conditionally render**

If not published, render all seats without highlighting anyone's seat.

- [ ] **Step 5: In view_qr.php, conditionally hide seat number**

If not published, don't show "Fila X, Asiento Y" on the QR page.

- [ ] **Step 6: Commit**

```bash
git add FrontEnd/view_confirmacion.php FrontEnd/asientos.php FrontEnd/js/asientos.js FrontEnd/view_qr.php API/servicios/ServicioAsientos.php
git commit -m "feat(student): conditional seat visibility based on published flag"
```

---

### Task 7: Tests — test_api.sh

**Files:**
- Modify: `API/test_api.sh` — add assignment endpoint tests

- [ ] **Step 1: Read existing test_api.sh to understand pattern**

- [ ] **Step 2: Add test for estado endpoint**

```bash
echo -e "${YELLOW}Prueba 20: Obtener estado de asignación como admin...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp20.txt -w "%{http_code}" -X GET $BASE_URL/admin/asignacion/estado \
    -H "Authorization: Bearer $ADMIN_TOKEN")

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo -e "${GREEN}✅ Éxito: Estado de asignación obtenido.${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
fi
```

- [ ] **Step 3: Add test for dry-run**

```bash
echo -e "${YELLOW}Prueba 21: Vista previa de asignación (dry-run)...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp21.txt -w "%{http_code}" -X POST $BASE_URL/admin/asignacion/ejecutar \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -d '{"dry_run": true}')

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo -e "${GREEN}✅ Éxito: Vista previa obtenida.${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
fi
```

- [ ] **Step 4: Commit**

```bash
git add API/test_api.sh
git commit -m "test: add assignment endpoint tests"
```
