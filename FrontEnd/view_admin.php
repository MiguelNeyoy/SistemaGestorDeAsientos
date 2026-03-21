<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Clausura</title>
    <!-- Bootstrap para la base de la UI interna -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilo base del proyecto (Reutilizando la semántica y estética) -->
    <link rel="stylesheet" href="css/bienvenida.css">
    <!-- Estilos específicos del Dashboard del Admin -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <!-- VISTA DE LOGIN -->
    <div id="loginView" class="container mt-5" style="display: none; max-width: 450px;">
        <div class="text-center mb-4">
            <h1 class="titulo-evento">CLAUSURA 2022 - 2026</h1>
            <h2 class="subtitulo">Acceso a Dashboard de Administración</h2>
            <div class="alert alert-info mt-3">
                Ingresa tus credenciales de <strong>administrador</strong> para continuar.
            </div>
        </div>

        <div id="loginError" class="alert alert-danger text-center" style="display: none;"></div>

        <form id="loginForm" class="p-4 rounded" style="background: rgba(255,255,255,0.05);">
            <div class="mb-3">
                <input type="text" id="adminUser" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="mb-4">
                <input type="password" id="adminPass" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="w-100 align-items-center justify-content-center">Ingresar al Panel</button>
        </form>
    </div>

    <!-- VISTA DE DASHBOARD -->
    <div id="dashboardView" style="display: none;">
        <!-- Navegación -->
        <nav class="navbar navbar-dark mb-4 admin-navbar">
            <div class="container-fluid px-4 py-2">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold titulo-evento m-0 p-0" style="text-shadow: none;">Panel de Administración</span>
                <span class="text-light text-muted d-none d-md-block">Sistema Gestor de Asientos</span>
                <div class="d-flex align-items-center">
                    <a href="view_registroAdmin.php" class="btn btn-outline-info btn-sm me-3">Nuevo Admin</a>
                    <button id="btnLogout" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-white m-0">Métricas Generales</h3>
                <span id="lastUpdated" class="badge bg-secondary">Actualizando...</span>
            </div>

            <!-- Tarjetas de Métricas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card metric-card text-white bg-primary mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase fw-light">Total Invitados</h5>
                            <h1 class="card-text fw-bold display-4" id="metric-total">0</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card text-white bg-success mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase fw-light">Turno Matutino</h5>
                            <h1 class="card-text fw-bold display-4" id="metric-m">0</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card text-white bg-info mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase fw-light">Turno Vespertino</h5>
                            <h1 class="card-text fw-bold display-4" id="metric-v">0</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card text-white bg-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase fw-light text-dark">Ingeniería</h5>
                            <h1 class="card-text fw-bold display-4 text-dark" id="metric-ing">0</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card text-white bg-danger mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase fw-light">Informática</h5>
                            <h1 class="card-text fw-bold display-4" id="metric-inf">0</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Estado de Alumnos -->
            <div class="card bg-dark text-white border-secondary mb-4">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Estado de Confirmación de Alumnos</h5>
                    <input type="text" id="searchInput" class="form-control form-control-sm w-25" placeholder="Buscar por cuenta o nombre...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover m-0" id="alumnosTable">
                            <thead class="table-secondary">
                                <tr>
                                    <th>No. Cuenta</th>
                                    <th>Nombre Completo</th>
                                    <th>Crr/Trn</th>
                                    <th class="text-center">Invitados Autorizados</th>
                                    <th>Correo Contacto</th>
                                    <th class="text-center">Estado ASISTENCIA</th>
                                </tr>
                            </thead>
                            <tbody id="alumnosTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">Cargando datos...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
