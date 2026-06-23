<?php
session_start();
require_once '../config.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_token']);
    header("Location: ../loginAdmin.php");
    exit;
}

if (!isset($_SESSION['admin_token']) || empty($_SESSION['admin_token'])) {
    header("Location: ../loginAdmin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Clausura</title>
    <!-- Se mantiene Bootstrap por compatibilidad con otras vistas y modales (Grid y JS) -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href='./admin.css?v=<?= filemtime(__DIR__ . '/admin.css') ?>'>
    <link rel="stylesheet" href='../css/pages/admin.css?v=<?= filemtime(__DIR__ . '/../css/pages/admin.css') ?>'>
</head>

<body class="admin-body">

    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'partials/_sidebar.php'; ?>
        <div id="sidebarOverlay" class="admin-sidebar-overlay"></div>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Topbar -->
            <?php include 'partials/_topbar.php'; ?>

            <div class="admin-content">
                <!-- Students Table -->
                <div id="table-container">
                    <?php include 'partials/_directory_table.php'; ?>
                </div>

                <!-- Asientos Map (hidden by default) -->
                <div id="asientos-controls" class="admin-hidden mb-2 d-flex align-items-center justify-content-center gap-2">
                    <select id="selectEventoAsientos" class="form-select form-select-sm" style="width: 150px;">
                        <option value="li">Evento 1 (LI)</option>
                        <option value="lisi">Evento 2 (LISI)</option>
                    </select>
                </div>
                <div id="asientos-container" class="admin-hidden">
                    <iframe id="asientosIframe" src="mapa_asientos.php?evento=li" style="width: 100%; height: 75vh; border: none;"></iframe>
                </div>
            </div>
        </main>
    </div>

    <script>
        window.__APP_CONFIG__ = {
            apiUrl: "<?php echo $JS_BASE_API_URL; ?>",
            token: "<?php echo $_SESSION['admin_token']; ?>"
        };
    </script>

    <!-- Modals -->
    <?php include 'modals/modal_agregar_alumno.php'; ?>
    <?php include 'modals/modal_editar_alumno.php'; ?>
    <?php include 'modals/modal_escaner_qr.php'; ?>
    <?php include 'modals/modal_resultado_qr.php'; ?>
    <?php include 'modals/modal_enviar_qr.php'; ?>
    <?php include 'modals/modal_eliminar_alumno.php'; ?>

    <!-- Modal Vista Previa Asignacion -->
    <div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Vista previa de asignación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="vistaPreviaBody">
            <p class="text-center">Cargando...</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnConfirmarAsignacion">Asignar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Progreso Asignacion -->
    <div class="modal fade" id="modalProgreso" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Asignando asientos...</h5>
          </div>
          <div class="modal-body text-center" id="progresoBody">
            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
              <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="progress mb-3">
              <div class="progress-bar progress-bar-striped progress-bar-animated" id="barraProgreso" style="width:0%"></div>
            </div>
            <p id="progresoTexto" class="mb-0">Procesando asignación...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.2/html2pdf.bundle.min.js"></script>
    <script type="module" src="../js/admin/app.js?v=<?= filemtime(__DIR__ . '/../js/admin/app.js') ?>"></script>
</body>

</html>