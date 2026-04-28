<?php
// Detectar si estamos en el entorno local o producción
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($host === 'localhost' || $host === '127.0.0.1') {
    // Entorno local: todo apunta a localhost
    $BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";
    $JS_BASE_API_URL = $BASE_API_URL;
} elseif (strpos($host, 'ngrok') !== false) {
    // Entorno ngrok:
    // - PHP CURL va directo a localhost (evita la página de advertencia de ngrok)
    $BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";
    // - El navegador remoto DEBE pasar por el túnel ngrok (localhost no existe en su dispositivo)
    $JS_BASE_API_URL = "https://" . $host . "/SistemaGestorDeAsientos/API/publico";
} else {
    // Entorno de producción (Hostinger)
    $BASE_API_URL = "https://" . $host . "/API/publico";
    $JS_BASE_API_URL = $BASE_API_URL;
}
?>