<?php
require_once "./API/controladores/ControladorAlumno.php";

header("Access-Control-Allow-Origin: *");
header("Control-Type: application/json; charset-UTF-8");

$url = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$url = rtrim($url,'/');
$url = substr($url, strlen('/API/publico/index.php'));

$rutas = [
    
];