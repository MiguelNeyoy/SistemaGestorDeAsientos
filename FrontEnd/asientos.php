<?php
session_start();

require_once "config.php";
require_once "auth_middleware.php";

$authData = verify_access(['alumno', 'admin']);

$token = $authData['token'];
$tipoUsuario = $authData['tipo'];

//  OBTENER DATOS DEL ALUMNO (solo si es alumno)
$miAsiento = null;

if ($tipoUsuario === "alumno") {
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $BASE_API_URL . "/alumnos/estado",
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
    $alumno = $data['data'];

    // Ajusta según tu API
    $miAsiento = $alumno['letra'] . $alumno['numero']; // ej: A5
  }
}

//  OBTENER ASIENTOS CONFIRMADOS (solo admin)
$asientosOcupados = [];

if ($tipoUsuario === "admin") {
  //  AQUÍ debes consumir tu endpoint real
  // ejemplo:
  /*
  $ch = curl_init();
  curl_setopt_array($ch, [
      CURLOPT_URL => $BASE_API_URL . "/asientos/ocupados",
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
      $asientosOcupados = $data['data']; // ["A1","A2"]
  }
  */

  //  TEMPORAL (PRUEBA)
  $asientosOcupados = ["A1", "A2", "B5", "C10"];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mapa de Asientos Teatro</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>

<body>



  <!-- Contenedor con scroll -->
  <div class="contenedor-scroll">
    <h1>Asientos teatro</h1>
    <div class="cabina">Cabina</div>
    <div class="zona-superior"></div>
    <div class="teatro"></div>
    <div class="mesa">Mesa directiva</div>
  </div>

  <!--  PASAR DATOS A JS -->
  <script>
    const tipoUsuario = "<?php echo $tipoUsuario; ?>";
    const miAsiento = "<?php echo $miAsiento; ?>";
    const asientosOcupados = <?php echo json_encode($asientosOcupados); ?>;
  </script>

  <!-- ZONA SUPERIOR -->
  <script>
    const zonaSuperior = document.querySelector('.zona-superior');
    const letrasSuperior = "KLM";

    letrasSuperior.split("").reverse().forEach(letra => {
      const filaDiv = document.createElement('div');
      filaDiv.classList.add('fila');

      const secciones = [
        { inicio: 1, fin: 11 },
        { inicio: 12, fin: 27 },
        { inicio: 28, fin: 38 }
      ];

      secciones.forEach(sec => {
        const secDiv = document.createElement('div');
        secDiv.classList.add('seccion');

        for (let n = sec.inicio; n <= sec.fin; n++) {
          const asiento = document.createElement('div');
          const idAsiento = letra + n;

          if ((letra === "M" && (n < 12 || n > 27)) ||
            (letra === "L" && (n >= 17 && n <= 22)) ||
            (letra === "M" && (n >= 16 && n <= 23))) {
            asiento.classList.add('hueco');
          } else {
            asiento.classList.add('asiento');
            asiento.textContent = idAsiento;

            //  LÓGICA
            if (tipoUsuario === "alumno") {
              if (idAsiento === miAsiento) {
                asiento.classList.add('confirmado');
              }
            }

            if (tipoUsuario === "admin") {
              if (asientosOcupados.includes(idAsiento)) {
                asiento.classList.add('confirmado');
              }
            }
          }

          secDiv.appendChild(asiento);
        }

        filaDiv.appendChild(secDiv);
      });

      zonaSuperior.appendChild(filaDiv);
    });
  </script>

  <!-- TEATRO -->


  <script>
    const teatro = document.querySelector('.teatro');
    const filas = 10;
    const letras = "JIHGFEDCBA";

    for (let f = 0; f < filas; f++) {
      const filaDiv = document.createElement('div');
      filaDiv.classList.add('fila');

      let secciones = [
        { inicio: 1, fin: 7 },
        { inicio: 8, fin: 23 },
        { inicio: 24, fin: 30 }
      ];

      if (f === 0) {
        secciones = [{ inicio: 1, fin: 34 }];
      }

      secciones.forEach(sec => {
        const secDiv = document.createElement('div');
        secDiv.classList.add('seccion');

        for (let n = sec.inicio; n <= sec.fin; n++) {
          const asiento = document.createElement('div');
          const idAsiento = letras[f] + n;

          asiento.classList.add('asiento');
          asiento.textContent = idAsiento;

          // LÓGICA
          if (tipoUsuario === "alumno") {
            if (idAsiento === miAsiento) {
              asiento.classList.add('confirmado');
            }
          }

          if (tipoUsuario === "admin") {
            if (asientosOcupados.includes(idAsiento)) {
              asiento.classList.add('confirmado');
            }
          }

          secDiv.appendChild(asiento);
        }

        filaDiv.appendChild(secDiv);
      });

      teatro.appendChild(filaDiv);
    }
  </script>

</body>

</html>