<?php
session_start();
$BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";
$error = "";

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_token']);
    header("Location: view_admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginAdmin'])) {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $apiUrl = $BASE_API_URL . "/admin/login";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'usuario' => $usuario,
        'contrasena' => $contrasena
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (($httpCode == 200 || $httpCode == 201) && $response) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success'] === true && !empty($data['data']['token'])) {
            $_SESSION['admin_token'] = $data['data']['token'];
            header("Location: view_admin.php");
            exit;
        } else {
            $error = isset($data['message']) ? $data['message'] : "Credenciales inválidas";
        }
    } else {
        if ($response) {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : "Error al iniciar sesión";
        } else {
            $error = "Error de conexión con el servidor.";
        }
    }
}

$tieneSesion = isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Clausura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/bienvenida.css">
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>

    <?php if (!$tieneSesion): ?>
        <div id="loginView" class="container mt-5" style="max-width: 450px;">
            <div class="form-box">
            <div class="text-center mb-4">
                <h1 class="titulo-evento">CLAUSURA 2022 - 2026</h1>
                <h2 class="subtitulo">Acceso de Administración</h2>
                <div class="alert alert-info mt-3 shadow-sm">
                    <i class="bi bi-shield-lock me-2"></i> Ingresa tus credenciales para continuar.
                </div>
            </div>

            <?php if ($error != ""): ?>
                <div id="loginError" class="alert alert-danger text-center shadow-sm"><?php echo htmlspecialchars($error); ?></div>
            <?php
            endif; ?>

            <form id="loginForm" method="POST" action="">
                <div style="position: relative; margin-top: 15px;">
                    <i class="bi bi-person" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #555; font-size: 1.2rem;"></i>
                    <input type="text" name="usuario" id="adminUser" placeholder="Usuario" required style="padding-left: 45px; margin-top: 0; box-shadow: none;">
                </div>
                <div style="position: relative; margin-top: 15px;">
                    <i class="bi bi-key" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #555; font-size: 1.2rem;"></i>
                    <input type="password" name="contrasena" id="adminPass" placeholder="Contraseña" required style="padding-left: 45px; margin-top: 0; box-shadow: none;">
                </div>
                <button type="submit" name="loginAdmin">Ingresar al Panel</button>
            </form>
        </div>
    <?php
    else: ?>

        <div id="dashboardView">

            <nav class="navbar navbar-light mb-4 shadow-sm" style="background-color: #ffffff; border-bottom: 2px solid #e2e8f0;">
                <div class="container-fluid px-4 py-2">
                    <span class="navbar-brand mb-0 h1 fs-4 fw-bold titulo-evento m-0 p-0" style="text-shadow: none;">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>Panel de Administración
                    </span>
                    <span class="text-secondary d-none d-md-block small fw-bold">Sistema Gestor de Asientos</span>
                    <div class="d-flex align-items-center gap-3">
                        <button id="btnEscanearQR" class="btn btn-primary btn-sm" onclick="alert('Función de Escanear QR en desarrollo')"><i class="bi bi-qr-code-scan me-1"></i>Escanear QR</button>
                        <button id="btnEnviarCorreos" class="btn btn-success btn-sm" onclick="alert('Función de Enviar Correos masivos en desarrollo')"><i class="bi bi-envelope-paper me-1"></i>Enviar QRs</button>
                        <a href="view_registroAdmin.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Nuevo Admin</a>
                        <button id="btnLogout" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión</button>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 pb-5">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class=" m-0 fw-light"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Resumen del Evento</h3>
                    <span id="lastUpdated" class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-clock me-1"></i>Actualizando...</span>
                </div>

                <div class="row g-3 mb-5">

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card text-white bg-success h-100" onclick="setFilterType('CONFIRMADOS')" title="Filtrar por Confirmados">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-person-check-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Alumnos Confirmados</h6>
                                <h2 class="card-text fw-bold m-0">
                                    <span id="metric-confirmados">0</span>
                                </h2>
                                <small class="d-block mt-1 opacity-75" style="font-size: 0.75rem;"><span id="metric-total-alumnos">0</span> en total</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card text-white bg-primary h-100" onclick="setFilterType('INVITADOS')" title="Filtrar por alumnos con invitados">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-people-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Total Invitados</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-total">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card text-white bg-info h-100" onclick="setFilterType('M')" title="Filtrar por turno Matutino">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-sun-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Matutino</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-m">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card text-white bg-secondary h-100" style="background-color: #6f42c1 !important;" onclick="setFilterType('V')" title="Filtrar por turno Vespertino">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-moon-stars-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Vespertino</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-v">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card bg-warning text-dark h-100" onclick="setFilterType('ING')" title="Filtrar por Ingeniería">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-gear-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Ingeniería</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-ing">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card text-white bg-danger h-100" onclick="setFilterType('INF')" title="Filtrar por Informática">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-pc-display metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Informática</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-inf">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card metric-card bg-dark h-100" onclick="setFilterType('RECHAZADOS')" title="Filtrar por Rechazados">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-person-x-fill metric-icon"></i>
                                <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">No Asistirán</h6>
                                <h2 class="card-text fw-bold m-0" id="metric-rechazados">0</h2>
                            </div>
                        </div>
                    </div>
                      <div class="col-6 col-md-4 col-lg-2">
                        <a href="asientos.php" style="text-decoration: none;">
                        <div class="card metric-card bg-black h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                               <i class="bi bi-grid-3x3-gap-fill metric-icon"></i>
                               <h6 class="card-title text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Mapa de asientos</h6>
     
                            </div>
                        </div>
                    </div>
                        </a>
                </div>

                <div class="card shadow rounded-3 mb-4" style="background-color: #fff; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header p-3 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <h5 class="m-0 fw-bold" style="color: #334155;"><i class="bi bi-list-check me-2 text-primary"></i>Directorio de Asistencia</h5>

                        <!-- Buscador con icono integrado y Botón Mostrar Todo -->
                        <div class="d-flex gap-2 align-items-center">
                            <button id="btnMostrarTodo" class="btn btn-sm btn-outline-secondary d-none text-nowrap" onclick="setFilterType('ALL')">
                                <i class="bi bi-funnel-fill me-1"></i>Mostrar Todo
                            </button>
                            <div style="position: relative; width: 300px; max-width: 100%;">
                                <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                                <input type="text" id="searchInput" placeholder="Buscar alumno..." style="margin-top: 0; padding-left: 45px; padding-right: 15px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #fff; color: #1e293b; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-size: 0.95rem;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="directorioCardBody">
                        <div id="directorioHintMobile" style="display: none;">
                            <i class="bi bi-info-circle me-2 fs-5"></i><br>
                            Utiliza la barra de búsqueda o toca alguna tarjeta métrica arriba para mostrar alumnos.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0 align-middle" id="alumnosTable">
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
                                            <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
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
            // Set secure token dynamically from PHP session
            const ADMIN_TOKEN = "<?php echo $_SESSION['admin_token']; ?>";
        </script>
    <?php
    endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>

</html>