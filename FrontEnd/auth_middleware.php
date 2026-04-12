<?php
session_start();

function verify_access($rolesPermitidos = ['alumno', 'admin']) {
    $auth = [
        'isLoggedIn' => false,
        'tipo' => 'guest',
        'token' => null
    ];

    // Verificar si es un Administrador
    if (isset($_SESSION['admin_token'])) {
        $auth['isLoggedIn'] = true;
        $auth['tipo'] = 'admin';
        $auth['token'] = $_SESSION['admin_token'];
    } 
    // Verificar si es un Alumno
    else if (isset($_SESSION['jwt_token'])) {
        $auth['isLoggedIn'] = true;
        $auth['tipo'] = $_SESSION['tipo'] ?? 'alumno'; 
        $auth['token'] = $_SESSION['jwt_token'];
    }

    // Verificar si el rol recuperado tiene permiso en la matriz exigida
    if ($auth['isLoggedIn'] && in_array($auth['tipo'], $rolesPermitidos)) {
        return $auth; 
    }

    // Flujo bloqueado, expulsa al usuario al index
    header("Location: index.php");
    exit;
}
?>
