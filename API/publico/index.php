<?php

require_once "./API/controladores/ControladorAlumno.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];

$alumnoController = new AlumnoController();

// Solo aceptamos POST para esta fase
if ($method !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
    exit;
}

// Rutas
if (strpos($requestUri, "/buscar") !== false) {
    $alumnoController->buscarAlumno();
} 
elseif (strpos($requestUri, "/confirmar") !== false) {
    $alumnoController->confirmarAsistencia();
} 
else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Ruta no encontrada"]);
}
