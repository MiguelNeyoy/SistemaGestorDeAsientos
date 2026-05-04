<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['jwt_token'])) {
    header("Location: index.php");
    exit;
}

$token = $_SESSION['jwt_token'];

// Consultar estado del alumno
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
  <title>Home Alumno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('img/logo.png') no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      background-color: rgba(255,255,255,0.9);
    }
    @media (max-width: 768px) {
      body {
        background: url('img/logo.png') no-repeat center top;
        background-size: contain;
      }
    }
  </style>
</head>
<body>

<!-- Navbar con botón de cerrar sesión -->
<nav class="navbar navbar-dark shadow-sm sticky-top" style="background-color: #0B3C5D;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <span class="navbar-brand fw-bold text-white">Facultad de Informática</span>
    <span class="text-white">Bienvenido, <?php echo htmlspecialchars($alumno['nombre'] ?? 'Alumno'); ?></span>
    <a href="index.php" class="btn btn-outline-light ms-3">Cerrar Sesión</a>
  </div>
</nav>

<div class="container mt-4">
  <!-- Estado de asistencia -->
  <?php if ($estado === "Si"): ?>
    <div class="alert alert-success text-center">
      ✅ Tu asistencia ha sido confirmada.
    </div>
  <?php elseif ($estado === "No"): ?>
    <div class="alert alert-danger text-center">
      ❌ Indicaste que no asistirás a la ceremonia.
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      ⚠ Tu asistencia aún está pendiente.
    </div>
  <?php endif; ?>

  <!-- Opciones en cards -->
  <div class="row mt-4">
    <div class="col-md-6 mb-3">
      <div class="card h-100 text-center shadow">
        <div class="card-body">
          <h5 class="card-title">Código QR</h5>
          <p class="card-text">Accede a tu código QR personal para el evento.</p>
          <a href="view_qr.php" class="btn btn-primary">Ver QR</a>
        </div>
      </div>
    </div>

    <div class="col-md-6 mb-3">
      <div class="card h-100 text-center shadow">
        <div class="card-body">
          <h5 class="card-title">Mapa de Asientos</h5>
          <p class="card-text">Consulta tu asiento asignado y el mapa completo.</p>
          <a href="asientos.php" class="btn btn-secondary">Ver Asientos</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
