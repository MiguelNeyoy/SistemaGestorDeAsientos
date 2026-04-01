<?php
session_start();
// URL base de la API, se usa para no reescribir manualmente
$BASE_API_URL = "https://graduacion.inftecn.com/API/publico";

$error = "";

if (isset($_POST['buscar'])) {
    // Limpiamos el número de cuenta ingresado
    $numCuenta = trim($_POST['numCuenta']);
    // Endpoint de la API que valida si un número de cuenta existe
    $apiUrl = $BASE_API_URL . "/alumnos/validar";
    // Inicializamos cURL para consumir la API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['numero_cuenta' => $numCuenta]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
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
        if (isset($data['success']) && $data['success'] === true && !empty($data['token'])) {
            // Guardamos el token en la sesión
            $_SESSION['jwt_token'] = $data['token'];
            // El alumno existe en el sistema, lo enviamos al siguiente paso
            header("Location: view_confirmacion.php");
            exit;
        }
        else {
            // Si la API dice que success es false, obtenemos su mensaje de error
            $error = isset($data['message']) ? $data['message'] : "Número de cuenta no encontrado";
        }
    }
    else {
        // Si el código HTTP trae error (ej. 404, 400), intentamos leer el mensaje que mandó la API
        if ($response) {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : "Número de cuenta no válido o no encontrado";
        }
        else {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bienvenida.css">
</head>

<body>

    <div class="container">
        <h1 class="titulo-evento">CLAUSURA 2022 - 2026</h1>
        <h2 class="subtitulo">Confirmación de asistencia</h2>

        <div class="alert alert-info mt-3">
            Ingresa tu número de cuenta <strong>sin el último dígito</strong>.
        </div>

        <!-- Bloque para mostrar errores si existen -->
        <?php if ($error != "") { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php
}?>

        <form method="post">
            <input type="text" name="numCuenta" placeholder="Número de cuenta" required>
            <button type="submit" name="buscar">Ingresar</button>
        </form>

    </div>

</body>

</html>