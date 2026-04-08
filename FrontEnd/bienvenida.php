<?php
// URL base de la API, se usa para no reescribir manualmente
$BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";

$error = "";

if (isset($_POST['buscar'])) {

    // Limpiamos el número de cuenta ingresado
    $numCuenta = trim($_POST['numCuenta']);

    // Endpoint de la API que valida si un número de cuenta existe
    $apiUrl = $BASE_API_URL . "/alumnos/" . urlencode($numCuenta);

    // Inicializamos cURL para consumir la API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Desactivamos verificación SSL en desarrollo local
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Ejecutamos la petición y obtenemos la respuesta y código HTTP
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Si la respuesta HTTP es 200 (OK), procesamos el éxito
    if ($httpCode == 200 && $response) {
        $data = json_decode($response, true);

        // Verificamos la bandera 'success' de la respuesta JSON
        if (isset($data['success']) && $data['success'] === true) {
            // El alumno existe en el sistema, lo enviamos al siguiente paso
            header("Location: estadoAlumno.php?cuenta=" . urlencode($numCuenta));
            exit;
        } else {
            // Si la API dice que success es false, obtenemos su mensaje de error
            $error = isset($data['message']) ? $data['message'] : "Número de cuenta no encontrado";
        }
    } else {
        // Si el código HTTP trae error (ej. 404, 400), intentamos leer el mensaje que mandó la API
        if ($response) {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : "Número de cuenta no válido o no encontrado";
        } else {
            // Error general en caso de que la API este caída o haya fallado cURL
            $error = "No se pudo comunicar con el sistema. Intente de nuevo más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Confirmación de asistencia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fuente -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Tu CSS -->
    <link rel="stylesheet" href="css/bienvenida.css">
</head>

<body>

    <div class="container">
        <h1 class="titulo-evento">CLAUSURA 2022 - 2026</h1>
        <h2 class="subtitulo">Confirmación de asistencia</h2>


        <div class="alert alert-info mt-3">
            Ingresa tu número de cuenta <strong>sin el último dígito</strong>.
        </div>

        <!-- FORMULARIO -->
        <div class="form-box">

            <b class="titulo-facultad">Facultad de informática</b>

            <h1 class="titulo-evento">CLAUSURA 2022 - 2026</h1>
            <h2 class="subtitulo">Confirmación de asistencia</h2>

            <div class="alert alert-info mt-3">
                Ingresa tu número de cuenta <strong>sin el último dígito.</strong>En caso de no tenerlo, solicítalo en
                ventanilla
                con control escolar
            </div>

            <!-- ERROR -->
            <?php if ($error != "") { ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>

            <form method="post">
                <input type="text" name="numCuenta" placeholder="Número de cuenta" required>
                <button type="submit" name="buscar">Ingresar</button>
            </form>

        </div>

        <!-- LEYENDA (AFUERA Y AL LADO) -->
        <div class="leyenda">
            <div class="alert alert-warning">
                Este formulario es para registrar tu asistencia al evento de clausura 2022 - 2026.<br><br>
                ⚠ La confirmación estará disponible hasta una semana antes del evento.
                Después de esa fecha, el sistema se cerrará y no será posible registrar tu asistencia,
                por lo que tendrás que acercarte a control escolar.
            </div>
        </div>

    </div>

</body>

</html>