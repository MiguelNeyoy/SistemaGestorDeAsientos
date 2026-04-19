<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function verify_access($rolesPermitidos = ['alumno', 'admin'])
{
    $auth = [
        'isLoggedIn' => false,
        'tipo' => 'guest',
        'token' => null
    ];

    // Determinar prioridad según los roles permitidos y sesión activa
    $tieneAdmin = isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token']);
    $tieneAlumno = isset($_SESSION['jwt_token']) && !empty($_SESSION['jwt_token']);

    // Si la página permite alumnos y tiene sesión de alumno, le damos prioridad al alumno
    // (Esto resuelve el caso donde un admin entra a una vista compartida como alumno)
    if ($tieneAlumno && in_array('alumno', $rolesPermitidos)) {
        $auth['isLoggedIn'] = true;
        $auth['tipo'] = $_SESSION['tipo'] ?? 'alumno';
        $auth['token'] = $_SESSION['jwt_token'];
        return $auth;
    }

    // Si tiene sesión de admin y la página permite admin
    if ($tieneAdmin && in_array('admin', $rolesPermitidos)) {
        $auth['isLoggedIn'] = true;
        $auth['tipo'] = 'admin';
        $auth['token'] = $_SESSION['admin_token'];
        return $auth;
    }

    // Caso de respaldo: Si el usuario tiene sesión de alumno pero la página es SOLO de admin
    // o viceversa, y no se cumplieron las condiciones anteriores, el flujo caerá al redireccionamiento.

    // Flujo bloqueado, expulsa al usuario al index
    header("Location: index.php");
    exit;
}
?>