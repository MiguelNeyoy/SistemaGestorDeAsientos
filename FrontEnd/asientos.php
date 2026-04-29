<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once "config.php";
require_once "auth_middleware.php";

$authData = verify_access(['alumno', 'admin']);

$token = $authData['token'];
$tipoUsuario = $authData['tipo'];

//Mostrar token para pruebas 
if ($tipoUsuario === "admin" && isset($_SESSION['admin_token'])) {
  echo "<pre>Token actual: " . $_SESSION['admin_token'] . "</pre>";
}

//  OBTENER MI ASIENTO (solo si es alumno)
$miAsiento = null;

if ($tipoUsuario === "alumno") {
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . "/asientos/misAsiento",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data['success']) {
    $asientoData = $data['data'];
    $miAsiento = $asientoData['letra'] . $asientoData['numero'];
  }
}

//  OBTENER MAPA DE ASIENTOS (solo admin) - Llamar a ambos eventos
$asientosOcupados = [];

if ($tipoUsuario === "admin") {
  // Llamar a evento LI
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . "/asientos/mapa/li",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data['success'] && isset($data['data']['asientos'])) {
    foreach ($data['data']['asientos'] as $asiento) {
      if ($asiento['ocupado']) {
        $asientosOcupados[] = $asiento['asiento'];
      }
    }
  }

  // Llamar a evento LISI
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . "/asientos/mapa/lisi",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data['success'] && isset($data['data']['asientos'])) {
    $asientosInfo = $data['data']['asientos'];
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mapa de Asientos Teatro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="css/asientos.css">
</head>

<body>

  <!-- NavBar y Controles -->
  <nav class="navbar navbar-dark shadow-sm sticky-top" style="background-color: #0B3C5D;">
    <div class="container-fluid px-3 d-flex justify-content-between align-items-center">
      <!-- Botón Volver -->
   <?php if ($tipoUsuario === 'admin'): ?>
  <a id="btnRegresar" 
     href="admin/view_admin.php"
     class="btn btn-outline-light btn-sm d-flex align-items-center gap-2" style="border-radius: 8px;">
     <i class="bi bi-arrow-left"></i> 
     <span class="d-none d-md-inline">Regresar al panel</span>
  </a>
<?php endif; ?>

      <!-- Titulo -->
      <span class="navbar-brand mb-0 h1 fs-5 fw-bold m-0 p-0 text-white">
        Mapa de Asientos
      </span>

      <!-- Selector Zonas (Transform Zoom) -->
      <div class="m-0" style="width: auto;">
        <select id="selectEvento" class="form-select form-select-sm text-dark fw-bold border-0 shadow-sm"
          style="border-radius: 8px; font-size: 0.85rem;">
          <option value="todos">Seleccionar Evento</option>
          <option value="superior">Evento 1</option>
          <option value="inferior">Evento 2</option>
        </select>
      </div>
      <!-- Espaciador para centrar titulo en escritorio -->
      <div class="d-none d-lg-block" style="width: 140px;"></div>
    </div>
  </nav>



  <!-- Contenedor con scroll -->
  <div class="contenedor-scroll shadow-inner">
    <div class="mapa-envoltura">
      <div class="cabina">Cabina</div>
      <div class="zona-superior"></div>
      <div class="teatro"></div>
      <div class="mesa">Escenario</div>
    </div>
  </div>

  <!--  PASAR DATOS A JS GLOBAL -->
  <script>
    window.TIPO_USUARIO = "<?php echo $tipoUsuario; ?>";
    window.MI_ASIENTO = "<?php echo $miAsiento; ?>";
    window.ASIENTOS_INFO = <?php echo json_encode($asientosInfo); ?>;
  </script>

  <script src="js/asientos.js"></script>


</body>

</html>