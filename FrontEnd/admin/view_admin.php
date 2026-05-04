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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href='./admin.css?v=8'>
</head>

<body class="admin-body">

    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'partials/_sidebar.php'; ?>

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
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomOut">-</button>
                    <span id="zoomLevel">100%</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomIn">+</button>
                </div>
                <div id="asientos-container" class="admin-hidden">
                    <iframe id="asientosIframe" src="../asientos.php?evento=li" style="width: 100%; height: 75vh; border: none;"></iframe>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script type="module" src="../js/admin/app.js?v=9"></script>
</body>

</html>