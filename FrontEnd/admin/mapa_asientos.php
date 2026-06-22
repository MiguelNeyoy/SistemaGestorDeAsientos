<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/api_helper.php';

if (!isset($_SESSION['admin_token']) || empty($_SESSION['admin_token'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$token = $_SESSION['admin_token'];

$eventoInput = $_GET['evento'] ?? 'li';
$evento = ($eventoInput === 'lisi') ? 'lisi' : 'li';

$asientosOcupados = [];
$asientosConfirmados = [];
$asientosEscaneados = [];

$endpoint = "/asientos/mapa/" . $evento;
$data = api_get($endpoint, $token);

if ($data && $data['success'] && isset($data['data']['asientos'])) {
    foreach ($data['data']['asientos'] as $asiento) {
        if (isset($asiento['estado']) && $asiento['estado'] === "ocupado") {
            if (isset($asiento['escaneado']) && $asiento['escaneado'] === true) {
                $asientosEscaneados[] = $asiento['id_asiento'];
            } elseif (isset($asiento['confirmado']) && $asiento['confirmado'] === true) {
                $asientosConfirmados[] = $asiento['id_asiento'];
            } else {
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
  <title>Mapa de Asientos - Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/asientos.css?v=<?= filemtime(__DIR__ . '/../css/asientos.css') ?>">
  <link rel="stylesheet" href="css/asientos-admin.css?v=<?= filemtime(__DIR__ . '/css/asientos-admin.css') ?>">
  <script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
</head>

<body class="navbar-hidden">

<div id="panzoom-instruction" class="panzoom-msg">
  <span class="d-none d-md-inline">Haz clic y arrastra para mover. Usa <b>Ctrl + Rueda</b> para zoom.</span>
  <span class="d-md-none">Arrastra para mover. Usa dos dedos para zoom.</span>
</div>



<div class="contenedor-scroll">
  <div class="mapa-envoltura">
    <div class="cabina">Cabina</div>
    <div class="zona-superior"></div>
    <div class="teatro"></div>
    <div class="mesa">Escenario</div>
  </div>
</div>

<script>
  window.__SEAT_DATA__ = {
    tipoUsuario: "admin",
    miAsiento: null,
    asientosGrupo: [],
    asientosOcupados: <?php echo json_encode($asientosOcupados); ?>,
    asientosConfirmados: <?php echo json_encode($asientosConfirmados); ?>,
    asientosEscaneados: <?php echo json_encode($asientosEscaneados); ?>
  };
</script>

<script type="module" src="../js/asientos.js?v=<?= filemtime(__DIR__ . '/../js/asientos.js') ?>"></script>

</body>
</html>
