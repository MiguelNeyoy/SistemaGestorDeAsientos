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

$basePath = './API/public/index.php';

if (strpos($uriActual, $basePath) === 0) {
    $uriActual = substr($uriActual, strlen($basePath));
}