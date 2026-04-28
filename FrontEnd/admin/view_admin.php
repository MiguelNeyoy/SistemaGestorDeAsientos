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
    <link rel="stylesheet" href='./admin.css?v=6'>
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
                <?php include 'partials/_directory_table.php'; ?>
            </div>
        </main>
    </div>

    <script>
        window.BASE_API_URL = "<?php echo $JS_BASE_API_URL; ?>";
        window.ADMIN_TOKEN = "<?php echo $_SESSION['admin_token']; ?>";

        function abrirModalAgregarAlumno() {
            const modal = new bootstrap.Modal(document.getElementById('agregarAlumnoModal'));
            modal.show();
        }

        function abrirModalEscanerQR() {
            const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
            modal.show();
        }

        function abrirModalEnviarQR() {
            const modal = new bootstrap.Modal(document.getElementById('enviarQRModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const btnToggle = document.getElementById('btnToggleSidebar');
            const sidebar = document.querySelector('.admin-sidebar');
            
            if (btnToggle && sidebar) {
                btnToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('admin-sidebar--active');
                });

                document.addEventListener('click', (e) => {
                    if (window.innerWidth <= 767 && 
                        sidebar.classList.contains('admin-sidebar--active') && 
                        !sidebar.contains(e.target)) {
                        sidebar.classList.remove('admin-sidebar--active');
                    }
                });
            }

            // Lógica para visibilidad de la tabla en versión móvil
            const searchInput = document.getElementById('searchInput');
            const tbody = document.getElementById('alumnosTableBody');
            const hint = document.getElementById('directorioHintMobile');

            if (window.innerWidth <= 768 && hint) {
                hint.style.display = 'block';
            }

            function showMobileTable() {
                if (tbody) tbody.classList.add('has-results');
                if (hint) hint.style.display = 'none';
            }

            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    if (searchInput.value.trim().length > 0) {
                        showMobileTable();
                    }
                });
            }

            document.querySelectorAll('.admin-sidebar__link, #btnMostrarTodo').forEach(el => {
                el.addEventListener('click', showMobileTable);
            });
        });
    </script>

    <!-- Modals -->
    <?php include 'modals/modal_agregar_alumno.php'; ?>
    <?php include 'modals/modal_editar_alumno.php'; ?>
    <?php include 'modals/modal_escaner_qr.php'; ?>
    <?php include 'modals/modal_resultado_qr.php'; ?>
    <?php include 'modals/modal_enviar_qr.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="../js/admin/app.js?v=7"></script>
</body>

</html>