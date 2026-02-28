<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables del archivo .env desde la raíz del proyecto
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Validar que existan las variables necesarias
if (!isset($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"])) {
    die("Error: Variables de entorno incompletas en el archivo .env");
}

$DB_HOST = $_ENV["DB_HOST"];
$DB_USER = $_ENV["DB_USER"];   // ← corregido (antes decía BB_USER)
$DB_PASS = $_ENV["DB_PASS"];
$DB_NAME = $_ENV["DB_NAME"];
$DB_PORT = $_ENV["DB_PORT"] ?? 3306; // Puerto opcional (default 3306)
