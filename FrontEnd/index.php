<?php
session_start();

// URL base de la API
require_once "config.php";

// Logout handler
if (isset($_GET['logout'])) {
    unset($_SESSION['jwt_token']);
    session_destroy();
    header("Location: index");
    exit;
}

$error = "";

if (isset($_POST['buscar'])) {

    $numCuenta = trim($_POST['NumCuenta']);

    // Validar solo números
    if (!ctype_digit($numCuenta)) {

        $error = "El número de cuenta solo debe contener dígitos.";

    }
    // Validar longitud
    elseif (strlen($numCuenta) < 7 || strlen($numCuenta) > 8) {

        $error = "El número de cuenta debe tener 7 u 8 dígitos.";

    } else {

        // Si trae 8 dígitos, quitar el último
        if (strlen($numCuenta) === 8) {
            $numCuenta = substr($numCuenta, 0, 7);
        }

        // Endpoint API
        $apiUrl = $BASE_API_URL . "/alumnos/validar";

        // cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode(['numero_cuenta' => $numCuenta])
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['Content-Type: application/json']
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Validación correcta
        if ($httpCode == 200 && $response) {

            $data = json_decode($response, true);

            if (isset($data['registro_cerrado']) && $data['registro_cerrado'] === true) {
                header("Location: registro_cerrado");
                exit;
            }

            if (isset($data['confirmacion_no']) && $data['confirmacion_no'] === true) {
                header("Location: confirmacion_no");
                exit;
            }

            if (
                isset($data['success']) &&
                $data['success'] === true &&
                !empty($data['token'])
            ) {

                unset($_SESSION['admin_token']);

                $_SESSION['jwt_token'] = $data['token'];

                $token = $data['token'];

                // Consultar estado del alumno
                $apiEstado = $BASE_API_URL . "/alumnos/estado";

                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => $apiEstado,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token
                    ],
                    CURLOPT_SSL_VERIFYPEER => false
                ]);

                $responseEstado = curl_exec($ch);

                $httpCodeEstado = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                if ($httpCodeEstado == 200 && $responseEstado) {

                    $dataEstado = json_decode($responseEstado, true);

                    if (
                        isset($dataEstado['success']) &&
                        $dataEstado['success']
                    ) {

                        $alumno = $dataEstado['data'];

                        $estado = $alumno['asistencia'];

                        if ($estado == "Si") {
                            header("Location: home_alumno");
                        } else {
                            header("Location: view_confirmacion");
                        }

                        exit;

                    } else {

                        $error = "No se pudo obtener el estado del alumno.";
                    }

                } else {

                    $error = "Error al consultar el estado del alumno.";
                }

            } else {

                $error = isset($data['message'])
                    ? $data['message']
                    : "Número de cuenta no encontrado";
            }

        } else {

            if ($response) {

                $data = json_decode($response, true);

                if (isset($data['registro_cerrado']) && $data['registro_cerrado'] === true) {
                    header("Location: registro_cerrado");
                    exit;
                }

                if (isset($data['confirmacion_no']) && $data['confirmacion_no'] === true) {
                    header("Location: confirmacion_no");
                    exit;
                }

                $error = isset($data['message'])
                    ? $data['message']
                    : "Número de cuenta no válido";

            } else {

                $error = "No se pudo comunicar con el sistema.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bienvenida.css?v=<?= filemtime(__DIR__ . '/css/bienvenida.css') ?>">
    <title>Sistema gestor de asistencia</title>
</head>

<body>

    <header class="pantalla-azul">

        <img src="img/logouas.png" alt="UAS" class="icon">

        <div class="titulo-header">

            <h1>
                Ceremonia de Graduación 2026
            </h1>

        </div>

        <img src="img/convision.png" alt="Vision" class="icon">

    </header>

    <div class="formulario">

        <form method="post">

            <img src="img/logofimaz.png" alt="FiMAZ" class="icon-pequeño">

            <!-- MENSAJE ERROR -->
            <?php if (!empty($error)) { ?>

                <div class="error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php } ?>

            

            <input
                type="text"
                name="NumCuenta"
                placeholder="Número de cuenta"
                maxlength="8"
                inputmode="numeric"
                required
            >

            <button
                type="submit"
                name="buscar"
            >
                Iniciar sesión
            </button>

        </form>

        <div class="alerta">

            Ingresa tu número de cuenta
            <strong>sin incluir el guion</strong>.

            En caso de no contar con dicha información,
            deberá solicitarla en la ventanilla del
            departamento de Control Escolar.

        </div>

    </div>

</body>
</html>