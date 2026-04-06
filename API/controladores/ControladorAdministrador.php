<?php

require_once(__DIR__ . '/../servicios/ServicioAdministrador.php');

class ControladorAdministrador
{
    private $servicioAdmin;

    public function __construct()
    {
        $this->servicioAdmin = new ServicioAdministrador();
    }

    public function loginAdmin()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->loginAdmin($input);
        echo json_encode($respuesta);
    }

    public function obtenerTodosAlumnos()
    {
        $respuesta = $this->servicioAdmin->obtenerTodosAlumnos();
        echo json_encode($respuesta);
    }

    public function obtenerMetricas()
    {
        $respuesta = $this->servicioAdmin->obtenerMetricas();
        echo json_encode($respuesta);
    }

    public function actualizarCorreoAlumno()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->actualizarCorreoAlumno($input);
        echo json_encode($respuesta);
    }
}
