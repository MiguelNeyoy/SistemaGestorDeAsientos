<?php
session_start();
// URL base de la API, se usa para no reescribir manualmente
require_once 'config.php';

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
        $estadoAsistencia = isset($alumno['asistencia']) ? $alumno['asistencia'] : "Pendiente";
    } else {
        die(isset($dataGet['message']) ? $dataGet['message'] : "Alumno no encontrado.");
    }
} else {
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

    // Convertir "Si"/"No" a 1/0 para la API
    $asistira = ($asiste === "Si") ? 1 : 0;

    // Si no asiste, usar el correo del alumno por defecto
    if ($asistira === 0) {
        $correo = !empty($alumno['email']) ? $alumno['email'] : 'no_asiste@sin-correo.com';
    }

    // Armar el arreglo de datos que espera la API
    $datosConfirmacion = [
        "asistira" => $asistira,
        "correo" => $correo,
        "num_invitados" => isset($_POST['invitados']) ? (int)$_POST['invitados'] : 0
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
    } else {
        curl_close($ch);
        $resultadoConfirmar = json_decode($respuestaConfirmar, true);

        if ($httpCodeConfirmar == 200 && isset($resultadoConfirmar['success']) && $resultadoConfirmar['success']) {

            if ($asistira == 1) {
                // Determinar el evento para la redirección
                $carreraAl = strtolower($alumno['carrera'] ?? '');
                $evRedirect = (strpos($carreraAl, 'informática') !== false || strpos($carreraAl, 'informatica') !== false) ? 'li' : 'lisi';
                header("Location: home_alumno?evento=" . $evRedirect);
            } else {
                header("Location: confirmacion_no");
            }
            exit;
        } else {
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
    } else if ($newEmail == $alumno['email']) {
        $mensajeCorreo = "El correo ingresado es igual al actual";
    } else {
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
        } else {
            curl_close($ch);
            $resultadoCorreo = json_decode($respuestaCorreo, true);

            if ($httpCodeCorreo == 200 && isset($resultadoCorreo['success']) && $resultadoCorreo['success']) {
                $mensajeCorreo = "Correo actualizado correctamente";
                // Actualizar el correo en la variable local para reflejar el cambio
                $alumno['email'] = $newEmail;
            } else {
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
    <link rel="stylesheet" href="css/bienvenida.css?v=<?= filemtime(__DIR__ . '/css/bienvenida.css') ?>">

    <script>
        // Configuración inyectada desde PHP
        window.__APP_CONFIG__ = {
            apiUrl: <?php echo json_encode($JS_BASE_API_URL); ?>,
            token: <?php echo json_encode($token); ?>
        };
        window.__ALUMNO_DATA__ = {
            numCuenta: <?php echo json_encode($alumno['numCuenta'] ?? ''); ?>,
            asistira: <?php echo json_encode($estadoAsistencia ?? 'Pendiente'); ?>
        };

        // Función para mostrar/ocultar los campos extras
        function mostrarCampos() {
            const extraDiv = document.getElementById("extra");
            if (!extraDiv) return;

            const radioSi = document.querySelector('input[name="asiste"][value="Si"]');
            const isSi = radioSi && radioSi.checked;

            const inputCorreo = document.querySelector('input[name="correo"]');
            const btnConfirmar = document.getElementById("btnConfirmar");

            if (isSi) {
                extraDiv.style.display = "block";
                if (inputCorreo) inputCorreo.required = true;
                if (btnConfirmar) btnConfirmar.innerText = "Confirmar asistencia";
            } else {
                extraDiv.style.display = "none";
                if (inputCorreo) inputCorreo.required = false;
                if (btnConfirmar) btnConfirmar.innerText = "Enviar";
            }
        }

        // Inicialización de eventos
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('input[name="asiste"]').forEach(radio => {
                radio.addEventListener('change', mostrarCampos);
            });
            // Verificación inicial por si hay autocompletado
            mostrarCampos();
        });
    </script>
</head>

<body>

    <div class="container"> <!-- Contenedor principal -->
        <!-- ... resto del contenido ... -->
        <?php /* Mantenemos la lógica de bloques PHP anterior pero simplificada en la inyección JS */ ?>


        <!-- Verificamos la asistencia desde la BD, por defecto era "Pendiente" -->
        <?php
        if ($estadoAsistencia == "Pendiente" || $estadoAsistencia == "" || $errorApi != "") {
        ?>
            <!-- Formulario de confirmación de asistencia (CONSUMO 2) -->
            <form method="post" class="form-box">
                <h2>
                    <?php echo htmlspecialchars($alumno['nombre'] . " " . $alumno['apellido']); ?>
                </h2>

                <p>Carrera:
                    <?php echo htmlspecialchars($alumno['carrera']); ?>
                </p>
                <p>Turno:
                    <?php echo htmlspecialchars($alumno['turno']); ?>
                </p>

                <!-- Bloque para mostrar posibles errores devueltos por la API -->
                <?php if ($errorApi != "") { ?>
                    <p class="error" style="color:red;">
                        <?php echo htmlspecialchars($errorApi); ?>
                    </p>
                <?php } ?>

                <?php
                $carreraInvH = strtolower($alumno['carrera'] ?? '');
                $esLI = strpos($carreraInvH, 'informática') !== false || strpos($carreraInvH, 'informatica') !== false;
                ?>


                <p>¿Asistirás a la clausura?</p>

                <label>
                    <input type="radio" name="asiste" value="Si" onclick="mostrarCampos()" required> Sí
                </label>

                <label>
                    <input type="radio" name="asiste" value="No" onclick="mostrarCampos()"> No
                </label>

                <!-- Este bloque se muestra/oculta basado en el radio button de asistencia -->
                <div id="extra" class="extra-campos">

                    <input type="email" name="correo" placeholder="Escribe tu correo"
                        value="<?php echo isset($alumno['email']) && filter_var($alumno['email'], FILTER_VALIDATE_EMAIL) ? htmlspecialchars($alumno['email']) : ''; ?>">


                    <?php
                    $carreraInv = strtolower($alumno['carrera'] ?? '');

                    /*
    Si contiene "informática"
    entonces es LI → 4 invitados

    cualquier otra carrera → LISI → 3 invitados
*/
                    $esLI =
                        (
                            strpos($carreraInv, 'informática') !== false ||
                            strpos($carreraInv, 'informatica') !== false
                        );

                    $maxInvitados = $esLI ? 4 : 3;
                    ?>

                    <p>Selecciona la cantidad de invitados (Máximo <?php echo $maxInvitados; ?>)</p>

                    <select name="invitados">
                        <?php for ($i = 0; $i <= $maxInvitados; $i++): ?>
                            <option value="<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                </div>

                <button type="submit" name="confirmar" id="btnConfirmar">Confirmar asistencia</button>
            </form>

            <!-- Mensaje de resultado de confirmación -->
            <?php if (!empty($mensajeConfirmacion)): ?>
                <?php
                // Verde si el correo se actualizó, rojo si hubo error
                $colorMsg = (strpos($mensajeConfirmacion, "actualizado correctamente") !== false) ? "green" : "red";
                ?>
                <p style="color:<?php echo $colorMsg; ?>;"><?php echo htmlspecialchars($mensajeConfirmacion); ?></p>
            <?php endif; ?>

        <?php } elseif ($estadoAsistencia == "Si") { ?>

            <!-- ESTADO -->
            <div class="estado">
                <h3>Tu estado actual</h3>

                <p>Asistencia: <strong>Si</strong></p>

                <p>Correo: <?php echo htmlspecialchars(isset($alumno['email']) ? $alumno['email'] : ""); ?></p>
                <p>Tu asiento se asignará al cerrar el registro.</p>

                <!-- Mensaje de resultado de actualización de correo -->
                <?php if (!empty($mensajeCorreo)): ?>
                    <p style="color:<?php echo $mensajeCorreo == "Correo actualizado correctamente" ? "green" : "red"; ?>;">
                        <?php echo htmlspecialchars($mensajeCorreo); ?>
                    </p>
                <?php endif; ?>

                <!-- QR Access Section -->
                <div class="mt-4 text-center">
                    <a href="view_qr" class="btn btn-success px-4">
                        <span class="admin-icon admin-icon--scan admin-icon--white"></span>
                        Obtener mi Pase QR
                    </a>
                </div>

                <br>
                <p style="text-align: center;"><a href="index">Regresar al inicio</a></p>
            </div>

        <?php } elseif ($estadoAsistencia == "No") { ?>

            <div class="estado">
                <h3>Tu estado actual</h3>
                <p>Asistencia: <strong>No</strong></p>
                <p>Si cambias de opinión, acércate al departamento de Control Escolar de tu facultad.</p>
                <br>
                <p style="text-align: center;"><a href="index">Regresar al inicio</a></p>
            </div>

        <?php } ?>

    </div>

    <!-- QR Library and Module -->
</body>

</html>