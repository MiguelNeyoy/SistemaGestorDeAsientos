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
                <div id="table-container">
                    <?php include 'partials/_directory_table.php'; ?>
                </div>
                
                <!-- Asientos Map (hidden by default) -->
                <div id="asientos-controls" class="mb-2 d-flex align-items-center justify-content-center gap-2" style="display: none;">
                    <select id="selectEventoAsientos" class="form-select form-select-sm" style="width: 150px;">
                        <option value="li">Evento 1 (LI)</option>
                        <option value="lisi">Evento 2 (LISI)</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomOut()">-</button>
                    <span id="zoomLevel">100%</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomIn()">+</button>
                </div>
                <div id="asientos-container" style="display: none;">
                    <iframe id="asientosIframe" src="../asientos.php?evento=li" style="width: 100%; height: 75vh; border: none;"
                    onload="initIframeDrag()"></iframe>
                </div>
            </div>
        </main>
    </div>

    <script>
        window.BASE_API_URL = "<?php echo $JS_BASE_API_URL; ?>";
        window.ADMIN_TOKEN = "<?php echo $_SESSION['admin_token']; ?>";

        // Funciones para mostrar/ocultar mapa de asientos
        function showAsientosMap(evento) {
            if (!evento) evento = 'li';
            
            var tableContainer = document.getElementById('table-container');
            var asientosContainer = document.getElementById('asientos-container');
            var asientosControls = document.getElementById('asientos-controls');
            var iframe = document.getElementById('asientosIframe');
            var selectEvento = document.getElementById('selectEventoAsientos');
            
            if (tableContainer) tableContainer.style.display = 'none';
            if (asientosControls) asientosControls.style.display = 'flex';
            if (asientosContainer) asientosContainer.style.display = 'block';
            if (iframe) iframe.src = '../asientos.php?evento=' + evento;
            if (selectEvento) selectEvento.value = evento;
        }

        function showTableAlumnos() {
            var tableContainer = document.getElementById('table-container');
            var asientosContainer = document.getElementById('asientos-container');
            var asientosControls = document.getElementById('asientos-controls');
            
            if (asientosContainer) asientosContainer.style.display = 'none';
            if (asientosControls) asientosControls.style.display = 'none';
            if (tableContainer) tableContainer.style.display = 'block';
        }

        // Funciones de zoom
        var zoomLevel = 1;
        var zoomMin = 0.5;
        var zoomMax = 2;

        function updateZoom() {
            var iframe = document.getElementById('asientosIframe');
            var zoomSpan = document.getElementById('zoomLevel');
            
            if (iframe) {
                // Aplicar scale al contenido del iframe
                try {
                    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    var container = iframeDoc.querySelector('.contenedor-scroll') || iframeDoc.body;
                    if (container) {
                        container.style.transform = 'scale(' + zoomLevel + ')';
                        container.style.transformOrigin = 'top center';
                        // Necesario para que funcione el scroll
                        container.style.width = '200%';
                        container.style.height = '200%';
                    }
                } catch(e) {
                    console.log('No se puede acceder al contenido:', e);
                }
            }
            if (zoomSpan) {
                zoomSpan.textContent = Math.round(zoomLevel * 100) + '%';
            }
        }

        function zoomIn() {
            if (zoomLevel < zoomMax) {
                zoomLevel += 0.25;
                updateZoom();
            }
        }

        function zoomOut() {
            if (zoomLevel > zoomMin) {
                zoomLevel -= 0.25;
                updateZoom();
            }
        }

        // Funciones para arrastrar (pan) el mapa
        var isDragging = false;
        var startX, startY;
        var scrollLeft, scrollTop;
        var dragContainer = null;

        function initDrag() {
            var container = document.getElementById('asientos-container');
            if (!container) {
                console.log('Contenedor no encontrado');
                return;
            }

            container.style.cursor = 'grab';

            container.onmousedown = function(e) {
                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'SELECT') return;
                isDragging = true;
                dragContainer = container;
                container.style.cursor = 'grabbing';
                startX = e.clientX;
                startY = e.clientY;
                scrollLeft = container.scrollLeft;
                scrollTop = container.scrollTop;
                e.preventDefault();
            };

            document.onmouseup = function() {
                if (isDragging && dragContainer) {
                    isDragging = false;
                    dragContainer.style.cursor = 'grab';
                    dragContainer = null;
                }
            };

            document.onmousemove = function(e) {
                if (!isDragging || !dragContainer) return;
                
                var deltaX = e.clientX - startX;
                var deltaY = e.clientY - startY;
                
                dragContainer.scrollLeft = scrollLeft - deltaX;
                dragContainer.scrollTop = scrollTop - deltaY;
            };
        }

        // Inicializar drag dentro del iframe
        function initIframeDrag() {
            var iframe = document.getElementById('asientosIframe');
            try {
                var iframeWin = iframe.contentWindow;
                var iframeDoc = iframeWin.document;
                var body = iframeDoc.body;
                
                body.style.cursor = 'grab';
                
                var iframeIsDragging = false;
                var iframeStartX, iframeStartY;
                var iframeScrollLeft, iframeScrollTop;

                body.onmousedown = function(e) {
                    iframeIsDragging = true;
                    body.style.cursor = 'grabbing';
                    iframeStartX = e.clientX;
                    iframeStartY = e.clientY;
                    iframeScrollLeft = body.scrollLeft;
                    iframeScrollTop = body.scrollTop;
                    return false;
                };

                iframeWin.onmouseup = function() {
                    if (iframeIsDragging) {
                        iframeIsDragging = false;
                        body.style.cursor = 'grab';
                    }
                };

                iframeWin.onmousemove = function(e) {
                    if (!iframeIsDragging) return;
                    
                    var deltaX = e.clientX - iframeStartX;
                    var deltaY = e.clientY - iframeStartY;
                    
                    body.scrollLeft = iframeScrollLeft - deltaX;
                    body.scrollTop = iframeScrollTop - deltaY;
                };
            } catch(e) {
                console.log('No se puede acceder al iframe:', e);
            }
        }

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

        // Event listener for event selector in map
        document.getElementById('selectEventoAsientos').addEventListener('change', function(e) {
            showAsientosMap(e.target.value);
        });

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

            // Inicializar drag del mapa de asientos
            initDrag();
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