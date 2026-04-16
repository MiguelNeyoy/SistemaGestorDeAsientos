<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . '/../controladores/ControladorAlumno.php';
require_once __DIR__ . '/../controladores/ControladorAsientos.php';
require_once __DIR__ . '/../controladores/ControladorQr.php';
require_once __DIR__ . '/../controladores/ControladorAdministrador.php';
// Configurar CORS al inicio para asegurar que siempre se envíen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Si es una solicitud OPTIONS (preflight), terminar aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Global Error Handler para retornar JSON en lugar de texto plano (que rompe CORS/JSON)
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Error interno del servidor",
        "details" => $e->getMessage()
    ]);
    exit;
});

// Definir las rutas
$rutas = [
    '/admin/login' => [
        'POST' => ['ControladorAdministrador', 'loginAdmin']
    ],
    '/admin/alumnos' => [
        'GET' => ['ControladorAdministrador', 'obtenerTodosAlumnos']
    ],
    '/admin/metricas' => [
        'GET' => ['ControladorAdministrador', 'obtenerMetricas']
    ],
    '/admin/alumnos/editar' => [
        'PUT' => ['ControladorAdministrador', 'editarAlumno']
    ],
    // Cambio: Para validar, usamos POST para no enviar datos sensibles y retornamos JWT
    '/alumnos/validar' => [
        'POST' => ['ControladorAlumno', 'validarAlumno']
    ],
    // Cambio: Removemos {id} de las rutas, se usará el JWT
    '/alumnos/asistencia' => [
        'POST' => ['ControladorAlumno', 'confirmarAsistencia']
    ],
    '/alumnos/correo' => [
        'POST' => ['ControladorAlumno', 'actualizarCorreo']
    ],
    '/alumnos/estado' => [
        'GET' => ['ControladorAlumno', 'obtenerEstado']
    ],
    '/asientos/reiniciar' => [
        'POST' => ['ControladorAsientos', 'reiniciarTeatro']
    ],
    '/asientos/mapa' => [
        'GET' => ['ControladorAsientos', 'verMapaAsientos']
    ],
    '/qr/generar' => [
        'POST' => ['ControladorQr', 'generarQr']
    ],
    '/qr/validar' => [
        'POST' => ['ControladorQr', 'validarQr']
    ],
    '/qr/confirmar' => [
        'POST' => ['ControladorQr', 'confirmarLlegada']
    ],
    '/admin/enviar-qrs' => [
        'POST' => ['ControladorQr', 'enviarQrs']
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

            $parametros = array_values(array_filter($coincidencias, 'is_string', ARRAY_FILTER_USE_KEY));

            // ------------- VALIDACION JWT -------------
            $rutasProtegidasAlumno = ['/alumnos/asistencia', '/alumnos/correo', '/alumnos/estado'];
            $rutasProtegidasAdmin = ['/admin/alumnos', '/admin/metricas', '/admin/alumnos/editar', '/qr/generar', '/qr/validar', '/qr/confirmar', '/admin/enviar-qrs'];

            if (in_array($rutaDefinida, $rutasProtegidasAlumno) || in_array($rutaDefinida, $rutasProtegidasAdmin)) {
                $headers = null;
                
                // Intento robusto de obtener el header de Authorization
                if (isset($_SERVER['Authorization'])) {
                    $headers = trim($_SERVER["Authorization"]);
                } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                    $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
                } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                    $headers = trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
                } elseif (function_exists('apache_request_headers')) {
                    $requestHeaders = apache_request_headers();
                    foreach ($requestHeaders as $key => $value) {
                        if (strtolower($key) === 'authorization') {
                            $headers = trim($value);
                            break;
                        }
                    }
                }

                if (!empty($headers)) {
                    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                        $token = $matches[1];
                        try {
                            $secret_key = $_SERVER['JWT_KEY'] ?? $_ENV['JWT_KEY'] ?? getenv('JWT_KEY');
                            if (!$secret_key) {
                                throw new Exception("JWT_KEY no configurada en el servidor.");
                            }
                            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secret_key, 'HS256'));
                            if (in_array($rutaDefinida, $rutasProtegidasAdmin)) {
                                if (!isset($decoded->data->role) || $decoded->data->role !== 'admin') {
                                    http_response_code(403);
                                    echo json_encode(["error" => "Acceso denegado. Permisos insuficientes."]);
                                    exit;
                                }
                                $_SERVER['JWT_ADMIN_ID'] = $decoded->data->admin_id ?? null;
                            } else {
                                // Inyectar el número de cuenta en $_SERVER para que el controlador lo use
                                $_SERVER['JWT_NUMERO_CUENTA'] = $decoded->data->numero_cuenta ?? null;
                            }
                        } catch (Exception $e) {
                            http_response_code(401);
                            echo json_encode(["error" => "Token inválido o expirado."]);
                            exit;
                        }
                    } else {
                        http_response_code(401);
                        echo json_encode(["error" => "Formato de token no válido."]);
                        exit;
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["error" => "Se requiere un token de autenticación (Authorization Bearer)."]);
                    exit;
                }
            }
            // ------------------------------------------

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
