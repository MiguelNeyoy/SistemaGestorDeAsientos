<?php
session_start();
// URL base de la API, se usa para no reescribir manualmente
$BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";

if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
    header("Location: index.php");
    exit;
}
$token = $_SESSION['jwt_token'];

$alumno = null;
$errorApi = "";

// =============================================================
// CONSUMO 1: OBTENER INFORMACIÓN DEL ALUMNO (GET)
// =============================================================
// Endpoint: GET /alumnos/{numero_cuenta}
// Propósito: Validar que el alumno existe y obtener sus datos
//            (nombre, apellido, carrera, turno, email, asistencia, cantInvitado).
// Respuesta esperada:
//   200 → { success: true, data: { ...datosDelAlumno } }
//   404 → { success: false, message: "Alumno no encontrado" }
// =============================================================
$apiUrlGet = $BASE_API_URL . "/alumnos/estado";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrlGet);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$responseGet = curl_exec($ch);
$httpCodeGet = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCodeGet == 200 && $responseGet) {
    $dataGet = json_decode($responseGet, true);
    if (isset($dataGet['success']) && $dataGet['success'] === true) {
        // Almacenamos la información del alumno devuelta por la API
        $alumno = $dataGet['data'];
    }
    else {
        die(isset($dataGet['message']) ? $dataGet['message'] : "Alumno no encontrado.");
    }
}
else {
    // Si la API falla, detenemos la ejecución mostrando un mensaje de error
    die("Error al consultar el alumno en el sistema. (Código de error: $httpCodeGet)");
}


// =============================================================
// CONSUMO 2: CONFIRMAR ASISTENCIA (POST)
// =============================================================
// Endpoint: POST /alumnos/{numero_cuenta}/asistencia
// Propósito: Registrar si el alumno asistirá o no a la clausura,
//            junto con su correo y cantidad de invitados.
// Datos enviados (JSON):
//   { id_alumno, asistira (1 o 0), num_invitados, correo }
// Respuesta esperada:
//   200 → { success: true, message: "Confirmación guardada correctamente" }
//   400 → Datos incompletos o correo inválido
//   403 → El correo no coincide
//   409 → El alumno ya confirmó asistencia
//   500 → Error interno del servidor
// =============================================================
$mensajeConfirmacion = "";

if (isset($_POST['confirmar'])) {
    $asiste = isset($_POST['asiste']) ? $_POST['asiste'] : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $invitados = isset($_POST['invitados']) ? (int)$_POST['invitados'] : 0;

    // Convertir "Si"/"No" a 1/0 para la API
    $asistira = ($asiste === "Si") ? 1 : 0;

    // Si no asiste, forzar invitados a 0 y usar el correo del alumno por defecto
    if ($asistira === 0) {
        $invitados = 0;
        $correo = $alumno['email'];
    }

    // Armar el arreglo de datos que espera la API
    $datosConfirmacion = [
        "asistira" => $asistira,
        "num_invitados" => $invitados,
        "correo" => $correo
    ];

    // Enviar la petición POST a la API
    $urlConfirmar = $BASE_API_URL . "/alumnos/asistencia";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $urlConfirmar,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ],
        CURLOPT_POSTFIELDS => json_encode($datosConfirmacion),
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $respuestaConfirmar = curl_exec($ch);
    $httpCodeConfirmar = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $mensajeConfirmacion = "Error de conexión: " . curl_error($ch);
        curl_close($ch);
    }
    else {
        curl_close($ch);
        $resultadoConfirmar = json_decode($respuestaConfirmar, true);

        if ($httpCodeConfirmar == 200 && isset($resultadoConfirmar['success']) && $resultadoConfirmar['success']) {
            // Confirmación exitosa (el correo se actualizó automáticamente si era diferente)
            header("Location: view_confirmacion.php");
            exit;
        }
        else {
            // Capturar el mensaje de error de la API
            $mensajeConfirmacion = isset($resultadoConfirmar['message'])
                ? $resultadoConfirmar['message']
                : "Error al confirmar asistencia (Código: $httpCodeConfirmar)";
        }
    }
}


// =============================================================
// CONSUMO 3: ACTUALIZAR CORREO (POST)
// =============================================================
// Endpoint: POST /alumnos/{numero_cuenta}/correo
// Propósito: Actualizar el correo electrónico del alumno después
//            de que ya confirmó su asistencia.
// Datos enviados (JSON):
//   { id_alumno, correo }
// Respuesta esperada:
//   200 → { success: true, message: "Correo actualizado correctamente" }
//   400 → Datos incompletos o correo inválido
//   500 → Error interno del servidor
// =============================================================
$mensajeCorreo = "";

if (isset($_POST['actualizar_correo'])) {
    $newEmail = trim($_POST['correo_actualizar']);

    // Validación local del correo antes de enviarlo a la API
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $mensajeCorreo = "Correo inválido";
    }
    else if ($newEmail == $alumno['email']) {
        $mensajeCorreo = "El correo ingresado es igual al actual";
    }
    else {
        // El correo es válido y diferente, enviarlo a la API
        $datosCorreo = [
            "correo" => $newEmail
        ];

        $urlCorreo = $BASE_API_URL . "/alumnos/correo";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $urlCorreo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_POSTFIELDS => json_encode($datosCorreo),
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $respuestaCorreo = curl_exec($ch);
        $httpCodeCorreo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $mensajeCorreo = "Error de conexión: " . curl_error($ch);
            curl_close($ch);
        }
        else {
            curl_close($ch);
            $resultadoCorreo = json_decode($respuestaCorreo, true);

            if ($httpCodeCorreo == 200 && isset($resultadoCorreo['success']) && $resultadoCorreo['success']) {
                $mensajeCorreo = "Correo actualizado correctamente";
                // Actualizar el correo en la variable local para reflejar el cambio
                $alumno['email'] = $newEmail;
            }
            else {
                $mensajeCorreo = isset($resultadoCorreo['message'])
                    ? $resultadoCorreo['message']
                    : "Error al actualizar correo (Código: $httpCodeCorreo)";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Confirmar asistencia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <p class="error"><?php echo htmlspecialchars($errorApi); ?></p>
        <?php
}?>

        <!-- Verificamos la asistencia desde la BD, por defecto era "Pendiente" -->
        <?php
$estadoAsistencia = isset($alumno['asistencia']) ? $alumno['asistencia'] : "Pendiente";
if ($estadoAsistencia == "Pendiente" || $estadoAsistencia == "" || $errorApi != "") {
?>
            <!-- Formulario de confirmación de asistencia (CONSUMO 2) -->
            <form method="post">
                <p>¿Asistirás a la clausura?</p>

                <label>
                    <input type="radio" name="asiste" value="Si" onclick="mostrarCampos()" required> Si
                </label>

                <label>
                    <input type="radio" name="asiste" value="No" onclick="mostrarCampos()"> No
                </label>

                <!-- Este bloque se muestra/oculta basado en el radio button de asistencia -->
                <div id="extra" class="extra-campos">
                    <p class="mensaje-correo">
                        Ingresa tu correo electrónico para recibir información importante sobre la clausura. <br>
                        <strong>Tienes solo un intento para hacerlo.</strong>
                    </p>
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

            <!-- Mensaje de resultado de confirmación -->
            <?php if (!empty($mensajeConfirmacion)): ?>
                <?php
        // Verde si el correo se actualizó, rojo si hubo error
        $claseMsg = (strpos($mensajeConfirmacion, "actualizado correctamente") !== false) ? "mensaje-ok" : "mensaje-error";
?>
                <p class="<?php echo $claseMsg; ?>"><?php echo htmlspecialchars($mensajeConfirmacion); ?></p>
            <?php
    endif; ?>

        <?php
}
else { ?>

            <!-- Si el alumno ya confirmó su asistencia, se le muestra su estado actual -->
            <div class="estado">
                <h3>Tu estado actual</h3>

                <p>Asistencia: <strong><?php echo htmlspecialchars($estadoAsistencia); ?></strong></p>

                <?php if ($estadoAsistencia == "Si") { ?>
                    <!-- Se muestran los datos de confirmación -->
                    <p>Invitados: <?php echo htmlspecialchars(isset($alumno['cantInvitado']) ? $alumno['cantInvitado'] : "0"); ?></p>
                    <p>Correo: <?php echo htmlspecialchars(isset($alumno['email']) ? $alumno['email'] : ""); ?></p>
                <?php
    }?>

                <!-- Formulario para actualizar correo (CONSUMO 3) -->

                <form method="post" class="form-correo">
                    <input hidden type="email" name="correo_actualizar" placeholder="Escribe tu correo"
                        value="<?php echo htmlspecialchars(isset($alumno['email']) ? $alumno['email'] : ''); ?>" required>
                </form>

                <p> <strong>Si deseas actualizar tu correo electrónico, Acude con un administrador para hacerlo</strong></p>

                <!-- Mensaje de resultado de actualización de correo -->
                <?php if (!empty($mensajeCorreo)): ?>
                    <p class="<?php echo $mensajeCorreo == "Correo actualizado correctamente" ? "mensaje-ok" : "mensaje-error"; ?>">
                        <?php echo htmlspecialchars($mensajeCorreo); ?>
                    </p>
                <?php
    endif; ?>


                <br>
                <p class="enlace-regresar"><a href="index.php">Regresar al inicio</a></p>
            </div>

        <?php
}?>

    </div>

</body>

</html>