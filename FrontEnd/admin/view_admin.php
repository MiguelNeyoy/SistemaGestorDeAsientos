<?php
session_start();
require_once '../config.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_token']);
    header("Location: ../loginAdmin.php");
    exit;
}

$tieneSesion = isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token']);

if (!$tieneSesion) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../admin.css">
</head>

<body>


    <div id="dashboardView">

        <nav class="navbar navbar-dark admin-navbar mb-4 shadow-sm">
            <div class="container-fluid px-4 py-2">
                <span class="navbar-brand mb-0 h1 fs-4 fw-bold m-0 p-0 text-white" style="text-shadow: none;">
                    <i class="bi bi-speedometer2 me-2 text-info"></i>Panel de Administración
                </span>
                <span class="text-light d-none d-md-block small fw-bold">Sistema Gestor de Asientos</span>
                <div class="d-flex align-items-center gap-3">
                    <button id="btnEscanearQR" class="btn btn-primary btn-sm"><i
                            class="bi bi-qr-code-scan me-1"></i><span class="d-none d-md-inline">Escanear
                            QR</span></button>
                    <button id="btnEnviarCorreos" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#enviarQRModal"><i class="bi bi-envelope-paper me-1"></i><span
                            class="d-none d-md-inline">Enviar QRs</span></button>
                    <button id="btnLogout" class="btn btn-outline-danger btn-sm"><i
                            class="bi bi-box-arrow-right me-1"></i><span class="d-none d-md-inline">Cerrar
                            Sesión</span></button>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">

            <div class="d-flex justify-content-between align-items-center mb-4 text-white">
                <h3 class=" m-0 fw-light" style="text-shadow: 1px 1px 3px rgba(0, 0,0, 0.8); color: black;"><i
                        class="bi bi-bar-chart-fill me-2 text-info"></i>Resumen del Evento
                </h3>
                <span id="lastUpdated" class="badge bg-secondary rounded-pill px-3 py-2"><i
                        class="bi bi-clock me-1"></i>Actualizando...</span>
            </div>

            <div class="row g-3 mb-5">

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card text-white bg-success h-100" onclick="setFilterType('CONFIRMADOS')"
                        title="Filtrar por Confirmados">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-person-check-fill metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Alumnos
                                Confirmados</h6>
                            <h2 class="card-text fw-bold m-0">
                                <span id="metric-confirmados">0</span>
                            </h2>
                            <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;"><span
                                    id="metric-total-alumnos">0</span> en total</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card text-white bg-primary h-100" onclick="setFilterType('INVITADOS')"
                        title="Filtrar por alumnos con invitados">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-people-fill metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Total
                                Invitados</h6>
                            <h2 class="card-text fw-bold m-0" id="metric-total">0</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card text-white bg-info h-100" onclick="setFilterType('LI4-1')"
                        title="Filtrar por LI 4-1 (Matutino)">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-mortarboard-fill metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">LI 4-1
                            </h6>
                            <h2 class="card-text fw-bold m-0" id="metric-li41">0</h2>
                            <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;">+<span
                                    id="guests-li41">0</span> invitados</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card text-white h-100"
                        style="background-color: #6f42c1 !important;" onclick="setFilterType('LI4-2')"
                        title="Filtrar por LI 4-2 (Vespertino)">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-mortarboard-fill metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">LI 4-2
                            </h6>
                            <h2 class="card-text fw-bold m-0" id="metric-li42">0</h2>
                            <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;">+<span
                                    id="guests-li42">0</span> invitados</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card bg-warning text-dark h-100" onclick="setFilterType('LISI4-1')"
                        title="Filtrar por LISI 4-1 (Matutino)">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-pc-display metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">LISI 4-1
                            </h6>
                            <h2 class="card-text fw-bold m-0" id="metric-lisi41">0</h2>
                            <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;">+<span
                                    id="guests-lisi41">0</span> invitados</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card text-white bg-danger h-100" onclick="setFilterType('LISI4-2')"
                        title="Filtrar por LISI 4-2 (Vespertino)">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-pc-display metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">
                                LISI 4-2
                            </h6>
                            <h2 class="card-text fw-bold m-0" id="metric-lisi42">0</h2>
                            <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;">+<span
                                    id="guests-lisi42">0</span> invitados</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card metric-card bg-dark h-100" onclick="setFilterType('RECHAZADOS')"
                        title="Filtrar por Rechazados">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-person-x-fill metric-icon"></i>
                            <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">No
                                Asistirán
                            </h6>
                            <h2 class="card-text fw-bold m-0" id="metric-rechazados">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="../asientos.php" style="text-decoration: none; display: block; height: 100%;">
                        <div class="card metric-card bg-black h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-grid-3x3-gap-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Mapa
                                    de asientos</h6>

                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="card shadow rounded-3 mb-4"
                style="background-color: #fff; border: 1px solid #e2e8f0; overflow: hidden;">
                <div class="card-header p-3 d-flex flex-wrap justify-content-between align-items-center gap-3"
                    style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <h5 class="m-0 fw-bold" style="color: #334155;"><i
                            class="bi bi-list-check me-2 text-primary"></i>Directorio de
                        Asistencia</h5>

                    <!-- Buscador con icono integrado y Botón Mostrar Todo -->
                    <div class="d-flex gap-2 align-items-center">
                        <button id="btnMostrarTodo" class="btn btn-sm btn-outline-secondary d-none text-nowrap"
                            onclick="setFilterType('ALL')">
                            <i class="bi bi-funnel-fill me-1"></i>Mostrar Todo
                        </button>
                        <div style="position: relative; width: 300px; max-width: 100%;">
                            <i class="bi bi-search"
                                style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" id="searchInput" placeholder="Buscar alumno..."
                                style="margin-top: 0; padding-left: 45px; padding-right: 15px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #fff; color: #1e293b; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-size: 0.95rem;">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" id="directorioCardBody">
                    <div id="directorioHintMobile" style="display: none;">
                        <i class="bi bi-info-circle me-2 fs-5"></i><br>
                        Utiliza la barra de búsqueda o toca alguna tarjeta métrica arriba para mostrar alumnos.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0 align-middle text-nowrap" id="alumnosTable">
                            <thead style="background-color: #f1f5f9; color: #475569;">
                                <tr>
                                    <th class="ps-3 border-bottom-0">No. Cuenta</th>
                                    <th class="border-bottom-0">Nombre Completo</th>
                                    <th class="border-bottom-0">Carrera/Turno</th>
                                    <th class="text-center border-bottom-0">Invitados Autorizados</th>
                                    <th class="border-bottom-0">Correo Contacto</th>
                                    <th class="text-center border-bottom-0">Asiento</th>
                                    <th class="text-center border-bottom-0">Estado</th>
                                    <th class="text-center pe-3 border-bottom-0">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="alumnosTableBody" style="color: #334155; font-size: 0.95rem;">
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <div class="spinner-border spinner-border-sm me-2 text-primary" role="status">
                                        </div>
                                        Cargando datos del servidor...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Configuración global para JS
        window.BASE_API_URL = "<?php echo $BASE_API_URL; ?>";
        // Set secure token dynamically from PHP session
        window.ADMIN_TOKEN = "<?php echo $_SESSION['admin_token']; ?>";
    </script>

    <?php include 'modals/modal_editar_alumno.php'; ?>
    <?php include 'modals/modal_escaner_qr.php'; ?>
    <?php include 'modals/modal_resultado_qr.php'; ?>
    <?php include 'modals/modal_enviar_qr.php'; ?>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="../js/admin/app.js?v=5"></script>
</body>

</html>