<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once "config.php";
require_once "helpers/api_helper.php";

if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
  header("Location: index.php");
  exit;
}

$tipoUsuario = 'alumno';
$token = $_SESSION['jwt_token'];

$eventoInput = $_GET['evento'] ?? 'li';
$evento = ($eventoInput === 'lisi') ? 'lisi' : 'li';

$miAsiento = null;
$asientosGrupo = [];
$asientosEscaneados = [];

$data = api_get("/asientos/misAsiento?evento=" . $evento, $token);

if ($data && $data['success']) {
  $asientoData = $data['data'];
  $miAsiento = $asientoData['letra'] . $asientoData['numero'];
}

$dataMapa = api_get("/asientos/mapa/" . $evento, $token);

$asignacionPublicada = false;
if ($dataMapa && $dataMapa['success'] && isset($dataMapa['data']['asientos'])) {
  $asignacionPublicada = $dataMapa['data']['asignacion_publicada'] ?? false;
  foreach ($dataMapa['data']['asientos'] as $asiento) {
    if ($asiento['estado'] === 'ocupado') {
      $asientosGrupo[] = trim($asiento['id_asiento']);
    }
    if (!empty($asiento['escaneado'])) {
      $asientosEscaneados[] = trim($asiento['id_asiento']);
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
  <script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
</head>

<body>

<div id="panzoom-instruction" class="panzoom-msg">
  <span class="d-none d-md-inline">Haz clic y arrastra para mover. Usa <b>Ctrl + Rueda</b> para zoom.</span>
  <span class="d-md-none">Arrastra para mover. Usa dos dedos para zoom.</span>
</div>

<nav class="navbar navbar-dark navbar-seatmap ">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a href="home_alumno" class="btn btn-light btn-sm">
      ← Regresar
    </a>
    <span class="navbar-brand">
      Mapa de Asientos
    </span>
    <span></span>
  </div>
</nav>

<aside class=" py-2 border-bottom border-secondary">
  <ul class="d-flex justify-content-center align-items-center flex-wrap gap-4 list-unstyled mb-0 legend-list">
    <li class="d-flex align-items-center gap-2">
      <span style="display: inline-block; width: 16px; height: 16px; border-radius: 4px; background-color: #111167; box-shadow: 0 0 6px rgba(17, 17, 103, 0.5);"></span>
      <span class="text-black">Mi Asiento</span>
    </li>
    <li class="d-flex align-items-center gap-2">
      <span style="display: inline-block; width: 16px; height: 16px; border-radius: 4px; background-color: #5c5c5c; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
      <span class="text-black">Asignado</span>
    </li>
    <li class="d-flex align-items-center gap-2">
      <span style="display: inline-block; width: 16px; height: 16px; border-radius: 4px; background-color: #5c5c5c; opacity: 0.45; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
      <span class="text-black">Disponible</span>
    </li>
  </ul>
</aside>

<div class="contenedor-scroll">
  <div class="mapa-envoltura">
    <div class="cabina">Cabina</div>
    <div class="zona-superior"></div>
    <div class="teatro"></div>
    <div class="mesa">Escenario</div>
  </div>
</div>

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

    <!-- CONTENEDOR -->
    <div class="contenedor-scroll">
      <div class="mapa-envoltura">
        <!-- <div class="cabina">Cabina</div> -->
        <div class="zona-superior"></div>
        <div class="teatro"></div>
        <!-- <div class="mesa">Escenario</div> -->
      </div>
    </div>

    <!-- PASAR DATOS A JS -->
    <script>
      window.__SEAT_DATA__ = {
        tipoUsuario: "<?php echo $tipoUsuario; ?>",
        miAsiento: "<?php echo $miAsiento; ?>",
        asientosGrupo: <?php echo json_encode($asientosGrupo); ?>,
        asientosOcupados: <?php echo json_encode($asientosOcupados); ?>,
        asientosConfirmados: <?php echo json_encode($asientosConfirmados); ?>,
        asientosEscaneados: <?php echo json_encode($asientosEscaneados); ?>,
        asignacionPublicada: <?php echo json_encode($asignacionPublicada); ?>
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