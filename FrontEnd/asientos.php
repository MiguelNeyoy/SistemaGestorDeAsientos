<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once "config.php";
require_once "auth_middleware.php";

$authData = verify_access(['alumno', 'admin']);

$token = $authData['token'];
$tipoUsuario = $authData['tipo'];

//  IMPORTANTE → obtener evento desde URL
$evento = $_GET['evento'] ?? 'li';

// ==============================
//  OBTENER MI ASIENTO (ALUMNO)
// ==============================
$miAsiento = null;
$asientosGrupo = [];

if ($tipoUsuario === "alumno") {
  //  MI ASIENTO
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . "/asientos/misAsiento?evento=" . $evento,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data && $data['success']) {
    $asientoData = $data['data'];
    $miAsiento = $asientoData['letra'] . $asientoData['numero'];
  }

  //  MAPA (grupo)
  $endpoint = ($evento === 'lisi')
    ? "/asientos/mapa/lisi"
    : "/asientos/mapa/li";

  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $responseMapa = curl_exec($ch);
  curl_close($ch);

  $dataMapa = json_decode($responseMapa, true);

  if ($dataMapa && $dataMapa['success'] && isset($dataMapa['data']['asientos'])) {
    foreach ($dataMapa['data']['asientos'] as $asiento) {
      $asientosGrupo[] = trim($asiento['id_asiento']); //  trim por seguridad
    }
  }
}

// ==============================
//  ADMIN 
// ==============================
$asientosOcupados = [];

if ($tipoUsuario === "admin") {
  $endpoint = ($evento === 'lisi')
    ? "/asientos/mapa/lisi"
    : "/asientos/mapa/li";

  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data && $data['success'] && isset($data['data']['asientos'])) {
    foreach ($data['data']['asientos'] as $asiento) {
      if (isset($asiento['estado']) && $asiento['estado'] === "ocupado") {
        $asientosOcupados[] = $asiento['id_asiento'];
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mapa de Asientos Teatro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/asientos.css?v=<?= filemtime(__DIR__ . '/css/asientos.css') ?>">
  <!-- Panzoom Library -->
  <script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
</head>

<body class="<?= (isset($_GET['hideNavbar']) && $_GET['hideNavbar'] == '1') ? 'navbar-hidden' : '' ?>">

<!-- Mensaje de instrucción (flotante) -->
<div id="panzoom-instruction" class="panzoom-msg">
  <span class="d-none d-md-inline">Haz clic y arrastra para mover. Usa <b>Ctrl + Rueda</b> para zoom.</span>
  <span class="d-md-none">Arrastra para mover. Usa dos dedos para zoom.</span>
</div>

<!-- NAVBAR (Condicional) -->
<?php if (!isset($_GET['hideNavbar']) || $_GET['hideNavbar'] != '1'): ?>
<nav class="navbar navbar-dark shadow-sm sticky-top" style="background-color: #0B3C5D;">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    <?php if ($tipoUsuario === 'admin'): ?>
    <a href="admin/view_admin" class="btn btn-outline-light btn-sm">
      ← Regresar
    </a>
    <?php endif; ?>

    <span class="navbar-brand fw-bold text-white">
      Mapa de Asientos
    </span>

    <?php if ($tipoUsuario === 'admin'): ?>
    <!-- SELECT EVENTO (solo visible para admin) -->
    <div class="d-flex align-items-center">
      <select id="selectEvento" class="form-select form-select-sm" style="width: 150px; margin-right:10px;">
        <option value="li" <?= $evento === 'li' ? 'selected' : '' ?>>Evento 1</option>
        <option value="lisi" <?= $evento === 'lisi' ? 'selected' : '' ?>>Evento 2</option>
      </select>
      <span id="eventoDescripcion" class="text-white fw-bold"></span>
    </div>
    <?php endif; ?>

  </div>
</nav>
<?php endif; ?>
<!-- CONTENEDOR -->
<div class="contenedor-scroll">
  <div class="mapa-envoltura">
    <div class="cabina">Cabina</div>
    <div class="zona-superior"></div>
    <div class="teatro"></div>
    <div class="mesa">Escenario</div>
  </div>
</div>

<!-- PASAR DATOS A JS -->
<script>
  window.__SEAT_DATA__ = {
    tipoUsuario: "<?php echo $tipoUsuario; ?>",
    miAsiento: "<?php echo $miAsiento; ?>",
    asientosGrupo: <?php echo json_encode($asientosGrupo); ?>,
    asientosOcupados: <?php echo json_encode($asientosOcupados); ?>
  };
</script>

<script type="module" src="js/asientos.js?v=<?= filemtime(__DIR__ . '/js/asientos.js') ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const selectEvento = document.getElementById("selectEvento");
  const eventoDescripcion = document.getElementById("eventoDescripcion");

  if (selectEvento && eventoDescripcion) {
    function actualizarDescripcion() {
      if (selectEvento.value === "li") {
        eventoDescripcion.textContent = "Licenciatura en Informática";
      } else if (selectEvento.value === "lisi") {
        eventoDescripcion.textContent = "Licenciatura en Ingeniería en Sistemas de Información";
      }
    }
    actualizarDescripcion();
    selectEvento.addEventListener("change", actualizarDescripcion);
  }
});
</script>

</body>
</html>
