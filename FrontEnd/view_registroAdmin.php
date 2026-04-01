<?php
session_start();
// URL base de la API, se usa para no reescribir manualmente
require_once 'config.php';

$error = "";
$success_msg = "";
if (!isset($_SESSION['admin_token']) || empty($_SESSION['admin_token'])) {
    // Si no hay sesión de administrador, redirigimos al login
    header("Location: view_admin.php");
    exit;
}

if (isset($_POST['registrar'])) {
    // Limpiamos los datos ingresados
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    // Endpoint de la API que registra a un nuevo administrador
    $apiUrl = $BASE_API_URL . "/admin/registro";

    // Inicializamos cURL para consumir la API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'usuario' => $usuario,
        'contrasena' => $contrasena
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $_SESSION['admin_token']
    ]);
    // Desactivamos verificación SSL en desarrollo local
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Ejecutamos la petición y obtenemos la respuesta y código HTTP
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    print_r($response);

    // Si la respuesta HTTP es 200 o 201 (OK/Created), procesamos el éxito
    if (($httpCode == 200 || $httpCode == 201) && $response) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success'] === true) {
            $success_msg = "Administrador registrado exitosamente.";
        }
        else {
            $error = isset($data['message']) ? $data['message'] : "Error al registrar administrador";
        }
    }
    else {
        if ($response) {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : "No se pudo registrar al administrador";
        }
        else {
            $error = "No se pudo comunicar con el sistema. Intente de nuevo más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bienvenida.css">
</head>

<body>

    <div class="container">
        <h1 class="titulo-evento">SISTEMA GESTOR</h1>
        <h2 class="subtitulo">Registro de Administrador</h2>

        <!-- Mensajes de error o éxito -->
        <?php if ($error != "") { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php
}?>
        <?php if ($success_msg != "") { ?>
            <p style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; font-weight: bold;">
                <?php echo htmlspecialchars($success_msg); ?>
            </p>
        <?php
}?>

        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit" name="registrar">Registrar Administrador</button>
        </form>
        
        <div style="margin-top: 15px; text-align: center;">
            <a href="view_admin.php" style="color: #fff; text-decoration: none;">Volver al panel</a>
        </div>
    </div>

</body>

</html>
