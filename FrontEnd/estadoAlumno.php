<?php
// URL base de la API, se usa para no reescribir manualmente
$BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";

// Obtenemos el número de cuenta de la URL
$cuenta = isset($_GET['cuenta']) ? $_GET['cuenta'] : '';

if (empty($cuenta)) {
    die("Número de cuenta no proporcionado");
}

$alumno = null;
$errorApi = "";

// -------------------------------------------------------------
// 1. OBTENER INFORMACIÓN DEL ALUMNO A TRAVÉS DE LA API (GET)
// -------------------------------------------------------------
$apiUrlGet = $BASE_API_URL . "/alumnos/" . urlencode($cuenta);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrlGet);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$responseGet = curl_exec($ch);
$httpCodeGet = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

print_r($responseGet);

if ($httpCodeGet == 200 && $responseGet) {
    $dataGet = json_decode($responseGet, true);
    if (isset($dataGet['success']) && $dataGet['success'] === true) {
        // Almacenamos la información del alumno devuelta por la API
        $alumno = $dataGet['data'];
    } else {
        die(isset($dataGet['message']) ? $dataGet['message'] : "Alumno no encontrado.");
    }
} else {
    // Si la API falla, detenemos la ejecución mostrando un mensaje de error
    die("Error al consultar el alumno en el sistema. (Código de error: $httpCodeGet)");
}


?>
<?php
// Actualizacion del correo
$datos = [];

if (isset($_POST['actualizar_correo'])) {
    $newEmail = $_POST['correo_actualizar'];
    $datos = [
        "id_alumno" => $cuenta,
        "correo" => $newEmail
    ];

    if ($newEmail == $alumno['email']) {
        $messaje = "correo igual";
    }
}


//VALIDACIÓN DEL CORREO
$messaje = ""; // Inicializar
if (isset($_POST['actualizar_correo'])) {
    $newEmail = trim($_POST['correo_actualizar']);

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $messaje = "Correo inválido";
    } else if ($newEmail == $alumno['email']) {
        $messaje = "El correo ingresado es igual al actual";
    } else {
        $messaje = "Correo válido y diferente al actual";
        // Aquí podrías enviar $datos a la API para actualizar
        $datos = [
            "id_alumno" => $cuenta,
            "correo" => $newEmail
        ];
    }
}

//conexion a la api

if (!empty($datos['correo'])) {
    echo ("entra al if");
    $url_api = $BASE_API_URL . "/alumnos/" . $datos['id_alumno'] . "/correo";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url_api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($datos)
    ]);
    // Ejecutar petición
    $respuesta = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Verificar errores de cURL
    if (curl_errno($ch)) {
        echo 'Error cURL: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $resultado = json_decode($respuesta, true);

    print_r($resultado);
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Confirmar asistencia</title>
    <link rel="stylesheet" href="css/bienvenida.css">

    <script>
        // Función en Javascript para mostrar/ocultar los campos extras
        // dependiendo de si el alumno asiste o no.
        function mostrarCampos() {
            let opcion = document.querySelector('input[name="asiste"]:checked')?.value;
            if (opcion == "Si") {
                document.getElementById("extra").style.display = "block";
            } else {
                document.getElementById("extra").style.display = "none";
            }
        }
    </script>
</head>

<body>

    <div class="container"> <!-- Contenedor principal -->

        <h2><?php echo htmlspecialchars($alumno['nombre'] . " " . $alumno['apellido']); ?></h2>

        <p>Carrera: <?php echo htmlspecialchars($alumno['carrera']); ?></p>
        <p>Turno: <?php echo htmlspecialchars($alumno['turno']); ?></p>

        <!-- Bloque para mostrar posibles errores devueltos por la API -->
        <?php if ($errorApi != "") { ?>
            <p class="error" style="color:red;"><?php echo htmlspecialchars($errorApi); ?></p>
        <?php } ?>

        <!-- Verificamos la asistencia desde la BD, por defecto era "Pendiente" -->
        <!-- OJO: Si el modelo trae un estado vacío en "asistencia", asumimos pendiente -->
        <?php
        $estadoAsistencia = isset($alumno['asistencia']) ? $alumno['asistencia'] : "Pendiente";
        if ($estadoAsistencia == "Pendiente" || $estadoAsistencia == "" || $errorApi != "") {
        ?>
            <!-- Formulario de confirmación de asistencia -->
            <form method="post">
                <p>¿Asistirás a la clausura?</p>

                <label>
                    <input type="radio" name="asiste" value="Si" onclick="mostrarCampos()" required> Si
                </label>

                <label>
                    <input type="radio" name="asiste" value="No" onclick="mostrarCampos()"> No
                </label>

                <!-- Este bloque se muestra/oculta basado en el radio button de asistencia -->
                <div id="extra" style="display:none">
                    <p>Correo</p>
                    <!-- El backend validará que el correo coincida con el original guardado -->
                    <!-- Por defecto lo cargamos del modelo -->
                    <input type="email" name="correo" placeholder="Escribe tu correo" required
                        value="<?php echo htmlspecialchars($alumno['email']); ?>">

                    <p>Invitados</p>
                    <select name="invitados">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <button type="submit" name="confirmar">Confirmar asistencia</button>
            </form>

        <?php } else { ?>

            <!-- Si el alumno ya confirmó su asistencia, se le muestra su estado actual -->
            <div class="estado">
                <h3>Tu estado actual</h3>

                <p>Asistencia: <strong><?php echo htmlspecialchars($estadoAsistencia); ?></strong></p>

                <?php if ($estadoAsistencia == "Si") { ?>
                    <!-- Se capturan los datos -->
                    <p>Invitados: <?php echo htmlspecialchars(isset($alumno['cantInvitado']) ? $alumno['cantInvitado'] : "0"); ?></p>
                    <p>Correo: <?php echo htmlspecialchars(isset($alumno['email']) ? $alumno['email'] : ""); ?></p>
                <?php } ?>

                <!-- BOTÓN PARA ACTUALIZAR CORREO -->
                <form method="post" style="margin-top:15px;">
                    <p>Actualizar correo:</p>
                    <input type="email" name="correo_actualizar" placeholder="Nuevo correo" required>
                    <button type="submit" name="actualizar_correo">Actualizar correo</button>
                </form>

                <?php if (isset($mensajeCorreo)) { ?>
                    <p style="color:green;"><?php echo htmlspecialchars($mensajeCorreo); ?></p>
                <?php } ?>


                <!-- HTML Mensaje de validaciones -->

                <?php if (!empty($messaje)): ?>
                    <p style="color:<?php echo $messaje == "Correo actualizado correctamente" ? "green" : "red"; ?>;">
                        <?php echo htmlspecialchars($messaje); ?>
                    </p>
                <?php endif; ?>


                <br>
                <p style="text-align: center;"><a href="bienvenida.php">Regresar al inicio</a></p>
            </div>

        <?php } ?>

    </div>

</body>

</html>