<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['jwt_token'])) {
    header("Location: index.php");
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

<style>

/* =========================
   CONFIGURACIÓN GENERAL
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{

    font-family: Arial, Helvetica, sans-serif;

    background: url('img/logo.png') no-repeat center center fixed;

    background-size: cover;

    min-height:100vh;
}

/* CAPA OSCURA */

body::before{

    content:"";

    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    background:rgba(0,0,0,0.45);

    z-index:-1;
}

/* =========================
   HEADER
========================= */

.header-container{

    width:100%;

    background:#0b4f94;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:15px 25px;

    gap:20px;

    flex-wrap:wrap;
}

/* LOGO */

.header-logo-right img{

    width:110px;

    max-width:100%;

    object-fit:contain;
}

/* TEXTO */

.header-text{

    flex:1;

    text-align:center;

    color:white;
}

.header-text h1{

    font-size:clamp(20px, 3vw, 42px);

    margin-bottom:8px;

    font-weight:bold;
}

.header-text h3{

    font-size:clamp(14px, 2vw, 24px);

    font-weight:400;
}

/* FRANJA */

.header-bottom{

    background:#005baa;

    color:white;

    text-align:center;

    padding:10px;

    font-size:clamp(10px, 2vw, 20px);

    letter-spacing:clamp(2px, 1vw, 10px);

    word-break:break-word;
}

/* =========================
   BOTÓN HAMBURGUESA
========================= */

.menu-toggle{

    display:none;

    position:fixed;

    top:20px;

    right:20px;

    z-index:9999;

    background:#0B3C5D;

    color:white;

    border:none;

    padding:12px 16px;

    border-radius:12px;

    font-size:24px;

    cursor:pointer;

    box-shadow:0 4px 10px rgba(0,0,0,0.3);

    transition:0.3s;
}

.menu-toggle:hover{

    transform:scale(1.05);
}

/* =========================
   NAVBAR
========================= */

.navbar-custom{

    background:#0B3C5D;

    padding:15px 20px;

    display:flex;

    justify-content:center;

    align-items:center;

    transition:0.4s ease;
}

/* =========================
   LINKS MENÚ MÓVIL
========================= */

.mobile-links{

    display:none;
}

.mobile-link{

    width:80%;

    background:white;

    color:#0B3C5D;

    text-decoration:none;

    padding:14px;

    border-radius:12px;

    text-align:center;

    font-weight:bold;

    transition:0.3s;
}

.mobile-link:hover{

    transform:scale(1.03);

    background:#e9ecef;
}

/* BOTÓN CERRAR SESIÓN */

.logout-btn{

    width:80%;

    border:1px solid white;

    color:white;

    text-decoration:none;

    padding:12px;

    border-radius:12px;

    text-align:center;

    transition:0.3s;
}

.logout-btn:hover{

    background:white;

    color:#0B3C5D;
}

/* =========================
   CONTENEDOR
========================= */

.main-container{

    width:100%;

    max-width:1200px;

    margin:auto;

    padding:20px;
}

/* =========================
   ALERTAS
========================= */

.alerts-container{

    display:flex;

    flex-direction:column;

    gap:15px;
}

.custom-alert{

    border:none;

    border-radius:15px;

    font-size:16px;

    padding:15px 20px;
}

/* ANIMACIÓN ALERTAS */

.fade-alert{

    opacity:0;

    transform:translateY(-20px);

    animation:fadeSlide 0.8s ease forwards;
}

.delay-1{

    animation-delay:0.8s;
}

@keyframes fadeSlide{

    to{

        opacity:1;

        transform:translateY(0);
    }
}

/* =========================
   CARDS
========================= */

.row-cards{

    display:flex;

    flex-wrap:wrap;

    gap:20px;

    margin-top:30px;
}

.col-responsive{

    flex:1 1 400px;
}

.option-card{

    border:none;

    border-radius:20px;

    overflow:hidden;

    background:rgba(255,255,255,0.95);

    box-shadow:0 5px 20px rgba(0,0,0,0.2);

    transition:0.3s;

    width:100%;

    animation:slideUp 0.8s ease;
}

.option-card:hover{

    transform:translateY(-8px);
}

.card-header-custom{

    height:8px;

    background:#0B3C5D;
}

.card-body{

    padding:30px 20px;

    text-align:center;
}

.card-title{

    font-size:clamp(20px, 3vw, 28px);

    color:#0B3C5D;

    margin-bottom:15px;
}

.card-text{

    font-size:clamp(14px, 2vw, 17px);

    color:#555;

    margin-bottom:25px;
}

.btn-custom{

    width:100%;

    padding:12px;

    border-radius:12px;

    font-size:clamp(14px, 2vw, 17px);

    font-weight:bold;

    transition:0.3s;
}

.btn-custom:hover{

    transform:scale(1.03);
}

/* =========================
   ANIMACIONES
========================= */

@keyframes slideUp{

    from{
        opacity:0;
        transform:translateY(40px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:768px){

    .menu-toggle{
        display:block;
    }

    .navbar-custom{

        position:fixed;

        top:0;

        right:-100%;

        width:260px;

        height:100vh;

        background:#0B3C5D;

        flex-direction:column;

        justify-content:center;

        align-items:center;

        z-index:9998;

        box-shadow:-5px 0 15px rgba(0,0,0,0.3);
    }

    .navbar-custom.active{

        right:0;
    }

    .mobile-links{

        display:flex;

        flex-direction:column;

        gap:15px;

        width:100%;

        align-items:center;
    }

    /* OCULTAR CARDS EN MÓVIL */

    .desktop-cards{

        display:none;
    }

    .header-container{

        flex-direction:column;

        justify-content:center;

        text-align:center;
    }

    .header-logo-right img{

        width:80px;
    }

    .header-text h1{

        font-size:24px;
    }

    .header-text h3{

        font-size:16px;
    }

    .main-container{

        padding:15px;
    }

    .card-body{

        padding:25px 15px;
    }
}

/* CELULARES */

@media(max-width:480px){

    .header-logo-right{

        display:none;
    }

    .header-container{

        padding:15px 10px;
    }

    .header-bottom{

        padding:8px;
    }

    .main-container{

        padding:10px;
    }

    .card-body{

        padding:20px 12px;
    }

    .col-responsive{

        flex:1 1 100%;
    }
}

</style>

</head>

<body>

<!-- HEADER -->

<div class="header-container">

    <!-- TEXTO -->

    <div class="header-text">

        <h1>
            Dirección General de Servicios Escolares
        </h1>

        <h3>
            Módulo de Servicios a los Alumnos
        </h3>

    </div>

    <!-- LOGO -->

    <div class="header-logo-right">

        <img src="img/logouas.png" alt="Logo UAS">

    </div>

</div>

<!-- FRANJA -->

<div class="header-bottom">

    UNIVERSIDAD AUTÓNOMA DE SINALOA

</div>

<!-- BOTÓN HAMBURGUESA -->

<button class="menu-toggle" id="menuToggle">

    ☰

</button>

<!-- NAVBAR / MENÚ -->

<div class="navbar-custom" id="mobileMenu">

    <div class="mobile-links">

        <a href="view_qr.php" class="mobile-link">

            Código QR

        </a>

        <a href="asientos.php" class="mobile-link">

            Mapa de Asientos

        </a>

        <a href="index.php" class="logout-btn">

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

                    <a href="view_qr.php" class="btn btn-primary btn-custom">

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

                    <a href="asientos.php" class="btn btn-secondary btn-custom">

                        Ver Asientos

                    </a>

                </div>

            </div>

        </div>

    </div>

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