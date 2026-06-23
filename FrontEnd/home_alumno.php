<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['jwt_token'])) {
    header("Location: index");
    exit;
}

$token = $_SESSION['jwt_token'];

// CONSULTAR ESTADO DEL ALUMNO
$apiEstado = $BASE_API_URL . "/alumnos/estado";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiEstado,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
]);

$responseEstado = curl_exec($ch);

curl_close($ch);

$dataEstado = json_decode($responseEstado, true);

$alumno = $dataEstado['data'] ?? null;

$estado = $alumno['asistencia'] ?? "Pendiente";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home Alumno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/responsive.css?v=<?= filemtime(__DIR__ . '/css/responsive.css') ?>">



</head>

<body style="background-image: url('img/logoFondo.webp');>

    <!-- HEADER -->

    <div class="header-container">

        <!-- TEXTO -->

        <div class="header-text">

            <h1>
                Registro de Asistencia a Ceremonia de Graduación
            </h1>



        </div>




    </div>

    <!-- FRANJA -->

    <div class="header-bottom">

        <span>UNIVERSIDAD AUTÓNOMA DE SINALOA</span>

        <a href="index?logout=1" class="logout-header-btn">Cerrar Sesión</a>

    </div>

    <!-- BOTÓN HAMBURGUESA -->

    <button class="menu-toggle" id="menuToggle">

        ☰

    </button>

    <!-- NAVBAR / MENÚ -->

    <div class="navbar-custom" id="mobileMenu">

        <div class="mobile-links">

            <a href="view_qr" class="mobile-link">

                Código QR

            </a>

            <a href="asientos" class="mobile-link">

                Mapa de Asientos

            </a>

            <a href="index?logout=1" class="logout-btn">

                Cerrar Sesión

            </a>

        </div>

    </div>

    <!-- CONTENIDO -->

    <div class="main-container">

        <!-- ALERTAS -->

        <div class="alerts-container">

            <!-- BIENVENIDA -->

            <div class="alert alert-primary text-center shadow-sm custom-alert fade-alert">

                Bienvenido,

                <strong>
                    <?php echo htmlspecialchars($alumno['nombre'] ?? 'Alumno'); ?>
                </strong>

            </div>

            <!-- ESTADO -->

            <?php if ($estado === "Si"): ?>

                <div class="alert alert-success text-center shadow-sm custom-alert fade-alert delay-1">

                    Tu asistencia ha sido confirmada.

                </div>

            <?php elseif ($estado === "No"): ?>

                <div class="alert alert-danger text-center shadow-sm custom-alert fade-alert delay-1">

                    Indicaste que no asistirás a la ceremonia.

                </div>

            <?php else: ?>

                <div class="alert alert-warning text-center shadow-sm custom-alert fade-alert delay-1">

                    Tu asistencia aún está pendiente.

                </div>

            <?php endif; ?>

        </div>

        <!-- CARDS SOLO DESKTOP -->

        <div class="row-cards desktop-cards">

            <!-- QR -->

            <div class="col-responsive">

                <div class="card option-card h-100">

                    <div class="card-header-custom"></div>

                    <div class="card-body">

                        <h5 class="card-title">

                            Código QR

                        </h5>

                        <p class="card-text">

                            Accede a tu código QR personal para el evento.

                        </p>

                        <a href="view_qr" class="btn btn-primary btn-custom">

                            Ver QR

                        </a>

                    </div>

                </div>

            </div>

            <!-- ASIENTOS -->

            <div class="col-responsive">

                <div class="card option-card h-100">

                    <div class="card-header-custom"></div>

                    <div class="card-body">

                        <h5 class="card-title">

                            Mapa de Asientos

                        </h5>

                        <p class="card-text">

                            Consulta tu asiento asignado y el mapa completo.

                        </p>

                        <a href="asientos" class="btn btn-secondary btn-custom">

                            Ver Asientos

                        </a>

                    </div>

                </div>

            </div>

        </div>

        <?php
        $carreraInvH = strtolower($alumno['carrera'] ?? '');
        $esLI = strpos($carreraInvH, 'informática') !== false || strpos($carreraInvH, 'informatica') !== false;
        ?>

        <article class="d-flex flex-row gap-2 mt-3">
            <div class="alert alert-info text-center shadow-sm custom-alert flex-fill mb-0">
                <strong>🎓 Ceremonia de Clausura — 15 de Julio de 2026</strong><br>
                <?= $esLI ? 'Horario: 11:30 AM' : 'Horario: 10:00 AM' ?>
            </div>

            <div class="alert alert-info text-center shadow-sm custom-alert flex-fill mb-0">
                <strong>📅 Cierre de confirmaciones: 9 de Julio de 2026</strong><br>
                Después de esta fecha no podrás modificar tu asistencia.
            </div>

            <div class="alert alert-info text-center shadow-sm custom-alert flex-fill mb-0">
                <strong>💺 Los asientos se asignarán una vez que cierre el periodo de confirmaciones.</strong>
            </div>
        </article>

    </div>

    <script>
        const menuToggle = document.getElementById("menuToggle");

        const mobileMenu = document.getElementById("mobileMenu");

        menuToggle.addEventListener("click", () => {

            mobileMenu.classList.toggle("active");

        });
    </script>

</body>

</html>