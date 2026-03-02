<?php

require_once __DIR__ . '/../controladores/ControladorAlumno.php';
require_once __DIR__ . '/../controladores/ControladorAsientos.php';
require_once __DIR__ . '/../controladores/ControladorQr.php';

// Configurar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Si es una solicitud OPTIONS (preflight), terminar aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Definir las rutas
$rutas = [
    '/alumnos/{id}' => [
        'GET' => ['AlumnoController', 'validarAlumno'],
        'GET' => ['AlumoController', 'confirmarAsistencia'],
        'GET' => ['AlumnoController', 'obtenerEstado']
    ],

    '/asientos/{id}' => [
        'GET' => ['controlador_asientos', 'reinciarTeatro'],
        'GET' => ['controlador_asientos', 'verMapaAsientos'],
    ],
    '/qr/generar' => [
        'POST' => ['ControladorQr', 'generarQr']
    ],
    '/qr/validar' => [
        'POST' => ['ControladorQr', 'validarQr']
    ]
];

// Obtener el método HTTP y la URI
$metodoHttp = $_SERVER['REQUEST_METHOD'];
$uriActual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Limpiar la URI para que no incluya subdirectorios si es que el proyecto no está en la raíz del servidor
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if (strpos($uriActual, $scriptName) === 0) {
    $uriActual = substr($uriActual, strlen($scriptName));
}

$rutaEncontrada = false;

foreach ($rutas as $rutaDefinida => $metodosPermitidos) {
    $patron = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $rutaDefinida);
    $patron = "#^" . $patron . "$#";

    if (preg_match($patron, $uriActual, $coincidencias)) {
        if (isset($metodosPermitidos[$metodoHttp])) {
            $rutaEncontrada = true;
            $accion = $metodosPermitidos[$metodoHttp];
            $nombreControlador = $accion[0];
            $nombreMetodo = $accion[1];

            $parametros = array_filter($coincidencias, 'is_string', ARRAY_FILTER_USE_KEY);

            if (class_exists($nombreControlador)) {
                $controlador = new $nombreControlador();
                call_user_func_array([$controlador, $nombreMetodo], $parametros);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "El controlador '$nombreControlador' no existe."]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método $metodoHttp no permitido para esta ruta."]);
        }
        break;
    }
}

if (!$rutaEncontrada) {
    http_response_code(404);
    echo json_encode(["error" => "Ruta '$uriActual' no encontrada."]);
}
