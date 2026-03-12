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
    <link rel="stylesheet" href="css/bienvenida.css">
</head>
<body>

    <div class="container">
        <h2>Confirmación de asistencia</h2>
        <p>Ingresa tu número de cuenta</p>

        <!-- Bloque para mostrar errores si existen -->
        <?php if ($error != "") { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form method="post">
            <input type="text" name="numCuenta" placeholder="Número de cuenta" required>
            <button type="submit" name="buscar">Buscar</button>
        </form>

    </div>

</body>
</html>