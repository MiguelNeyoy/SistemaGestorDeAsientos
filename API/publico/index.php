<?php
require_once "./API/controladores/ControladorAlumno.php";

header("Access-Control-Allow-Origin: *");
header("Control-Type: application/json; charset-UTF-8");

$method = $_SERVER["REQUEST_METHOD"];
$alumnoController = new AlumnoController;

//Aqui se reciben toda las peticiones y dependiedo lo que  se requiera se mandada a al controlador correspondiente 

switch ($method) {
    case 'POST':
        //si va a mandar dartos
        $alumnoController -> crearAlumno();
        break;
    case 'GET':

        //Si van a consular o mostrar datos 

        $alumnoController -> validarAlumno();
        break;
    case 'PUT':
        // si van a modificar datos 
        $alumnoController -> actualizarDatosAlumno();
        break;
    case 'DELETE':
        // si se van a eliminar o desactivar datos
        $alumnoController -> eliminarAlumno();
        break;
    
    default:
        http_response_code(405);
        echo json_encode(["menssage" => "Metodo no permitido"]);
        break;
}