<?php
require_once "./API/controladores/ControladorAlumno.php";
//Se configurarn los cors
header("Access-Control-Allow-Origin: *");
header("Control-Type: application/json; charset-UTF-8");

//se definen las rutas
$rutas = [
    '/alumnos/{id}' => [
        'GET' => ['AlumnoController' , 'validarAlumno'],
        'GET' => ['AlumoController', 'confirmarAsistencia'],
        'GET' => ['AlumnoController' , 'obtenerEstado']
    ],

    '/asientos/{id}' => [
        'GET' => ['controlador_asientos', 'reinciarTeatro'],
        'GET' => ['controlador_asientos', 'verMapaAsientos'],
    ],
    '/qr/{id}' => [
        'GET' => ['qr_controlador', 'generarQr'],
        'GET' => ['qr_controlador', 'validarQr'],
    ]

];

//Obtenemos el metodo http 
$metodoHttp = $_SERVER['REQUEST_METHOD'];
//limpiamos la URL
$uriActual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = './API/public/index.php'; //esto es esgun la carptea de ubicacion

if (strpos($uriActual, $basePath) === 0) {
    $uriActual = substr($uriActual, strlen($basePath));
}
 
$rutaEncontrada = false;

foreach($rutas as $rutaDefinida => $metodoPermitido){
    $patron = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $rutaDefinida);
    $patron = "#^" . $patron . "$#"; // Añadimos delimitadores para match exacto

    // Verificamos si la URI actual coincide con el patrón
    if (preg_match($patron, $uriActual, $coincidencias)) {
        $rutaEncontrada = true;

        // Validamos si el método HTTP (Ej: GET) está definido para esa ruta
        if (isset($metodosPermitidos[$metodoHttp])) {
            
            $accion = $metodosPermitidos[$metodoHttp];
            $nombreControlador = $accion[0];
            $nombreMetodo = $accion[1];

            // Quitamos el primer elemento de coincidencias (que es la URL completa)
            // para quedarnos solo con los parámetros dinámicos (Ej: el '123' del {id})
            array_shift($coincidencias);

            // Verificamos que la clase exista (para evitar errores fatales)
            if (class_exists($nombreControlador)) {
                $controlador = new $nombreControlador();
                
                // Ejecutamos el método y le pasamos los parámetros dinámicos
                // Si la ruta era /alumnos/123, esto equivale a $controlador->obtenerPorId('123')
                call_user_func_array([$controlador, $nombreMetodo], $coincidencias);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "El controlador $nombreControlador no existe."]);
            }

        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["error" => "Método $metodoHttp no permitido en esta ruta."]);
        }
        break; // Detenemos el loop porque ya encontramos la ruta
    }
}

// Si terminó el ciclo y no encontró nada:
if (!$rutaEncontrada) {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "La ruta $uriActual no fue encontrada."]);
}