<?php
session_start();
require_once 'config.php';
$error = "";

// Si ya tiene sesión, mandarlo al panel
if (isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token'])) {
    header("Location: admin/view_admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginAdmin'])) {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $apiUrl = $BASE_API_URL . "/admin/login";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'usuario' => $usuario,
        'contrasena' => $contrasena
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (($httpCode == 200 || $httpCode == 201) && $response) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success'] === true && !empty($data['data']['token'])) {
            // Limpiar sesión de alumno si existía previamente para evitar colisiones
            unset($_SESSION['jwt_token']);
            unset($_SESSION['tipo']);

            $_SESSION['admin_token'] = $data['data']['token'];
            header("Location: admin/view_admin.php");
            exit;
        } else {
            $error = isset($data['message']) ? $data['message'] : "Credenciales inválidas";
        }
    } else {
        if ($response) {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : "Error al iniciar sesión";
        } else {
            $error = "Error de conexión con el servidor.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/LoginAdmin.css">
</head>

<body>
    <div id="loginView" class="container mt-5">
        <div class="form-box shadow-lg">
            <div class="text-center mb-4">
                <div class="d-flex justify-content-center mb-3">
                     <i class="bi bi-person-badge text-primary" style="font-size: 3rem;"></i>
                </div>
                <h1 class="titulo-evento text-dark fw-bold">Gestión de Acceso</h1>
                <h2 class="subtitulo text-secondary">Administración</h2>
                <div class="alert alert-info mt-3 shadow-sm border-0 text-start">
                    <i class="bi bi-shield-lock-fill me-2"></i><strong>Seguridad:</strong> Ingresa tus credenciales exclusivas del panel.
                </div>
            </div>

            <?php if ($error != ""): ?>
                <div id="loginError" class="alert alert-danger text-center shadow-sm border-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="">
                <div class="mb-3 position-relative">
                    <i class="bi bi-person position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); font-size: 1.2rem;"></i>
                    <input type="text" name="usuario" id="adminUser" class="form-control form-control-lg bg-light border-0" placeholder="Usuario" required style="padding-left: 45px; box-shadow: none;">
                </div>
                <div class="mb-4 position-relative">
                    <i class="bi bi-key position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); font-size: 1.2rem;"></i>
                    <input type="password" name="contrasena" id="adminPass" class="form-control form-control-lg bg-light border-0" placeholder="Contraseña" required style="padding-left: 45px; box-shadow: none;">
                </div>
                <button type="submit" name="loginAdmin" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Panel
                </button>
            </form>
        </div>
    </div>
</body>

</html>
