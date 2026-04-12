<?php
// Detectar si estamos en el entorno local o producción
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($host === 'localhost' || $host === '127.0.0.1') {
    // Entorno local
    $BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";
} else {
    // Entorno de producción (Hostinger)
    // Se adapta dinámicamente al dominio actual
    $BASE_API_URL = "https://" . $host . "/API/publico";
}
?>
