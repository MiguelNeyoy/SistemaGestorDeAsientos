<?php
session_start();
require_once "config.php";

// Verificar token en sesión
if (!isset($_SESSION['jwt_token'])) {
    header("Location: index.php");
    exit;
}

$token = $_SESSION['jwt_token'];

// Función para consumir API
function apiRequest($url, $token) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// 1. Verificar asistencia
$estadoAlumno = apiRequest($BASE_API_URL . "/alumnos/estado", $token);
if (!$estadoAlumno || ($estadoAlumno['data']['asistencia'] ?? "No") !== "Si") {
    $error = "Aún no has confirmado tu asistencia. Por favor, completa el formulario primero.";
}

// 2. Intentar obtener token QR
if (!isset($error)) {
    $qrData = apiRequest($BASE_API_URL . "/alumnos/qr", $token);
    
    if (!$qrData || !$qrData['success']) {
        // Si el éxito es falso, es que aún no está habilitado o generado
        $error = "El pase de acceso aún no ha sido habilitado para tu grupo o confirmación. Mantente al pendiente.";
    } elseif (empty($qrData['data']['token'])) {
        $error = "Pase generado pero sin token válido. Contacta a soporte.";
    } elseif (($qrData['data']['escaneado'] ?? 0) == 1) {
        $error = "Este pase ya ha sido utilizado.";
    } else {
        $qrToken = $qrData['data']['token'];
        $alumno = $estadoAlumno['data'];
        
        // Consultar el asiento asignado del alumno
        $asientoResp = apiRequest($BASE_API_URL . "/asientos/misAsiento", $token);
        if ($asientoResp && $asientoResp['success']) {
            $alumno['asiento'] = $asientoResp['data']['letra'] . $asientoResp['data']['numero'];
        } else {
            $alumno['asiento'] = 'Sin asignar';
        }

        // Determine event and check if assignment is published
        $carrera = strtolower($alumno['carrera'] ?? '');
        $evento = (strpos($carrera, 'informática') !== false || strpos($carrera, 'informatica') !== false) ? 'li' : 'lisi';
        $mapaResp = apiRequest($BASE_API_URL . "/asientos/mapa/" . $evento, $token);
        $asignacionPublicada = $mapaResp['data']['asignacion_publicada'] ?? false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Pase QR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
  <style>
    body {
      background: url('img/logo.png') no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      background: rgba(255,255,255,0.9); /* contraste para legibilidad */
      border-radius: 12px;
    }
    #qrcode {
      background: #fff; /* fondo blanco para escaneo */
      display: inline-block;
      padding: 10px;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <a href="home_alumno.php" class="btn btn-outline-primary mb-3">← Regresar</a>

  <div class="card shadow text-center">
    <div class="card-body">
      <h2 class="fw-bold">Mi Pase de Acceso</h2>

      <?php if (isset($error)): ?>
        <div class="alert alert-warning mt-3">
          <?php echo htmlspecialchars($error); ?>
        </div>
        <?php if (strpos($error, "asistencia") !== false): ?>
          <a href="asientos.php" class="btn btn-primary mt-3">Mira tu asiento </a>
        <?php endif; ?>
      <?php else: ?>
        <div id="qrcode" class="d-flex justify-content-center my-4"></div>
        <h5><?php echo htmlspecialchars($alumno['nombre']); ?></h5>
        <p>Asiento: <?php echo $asignacionPublicada ? htmlspecialchars($alumno['asiento']) : 'No disponible'; ?> | Carrera: <?php echo htmlspecialchars($alumno['carrera']); ?></p>
        <p class="text-muted">Presenta este código al ingresar al teatro.</p>
        <button id="downloadBtn" class="btn btn-success mt-3">Descargar mi pase</button>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!isset($error)): ?>
<script>
  // Generar QR dinámico
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: "<?php echo $qrToken; ?>",
    width: 200,
    height: 200
  });

  // Descargar QR como imagen
  document.getElementById("downloadBtn").addEventListener("click", function() {
    var canvas = document.querySelector("#qrcode canvas");
    var link = document.createElement("a");
    link.download = "mi_pase_qr.png";
    link.href = canvas.toDataURL();
    link.click();
  });
</script>
<?php endif; ?>

</body>
</html>

