<?php

require_once(__DIR__ . '/../servicios/ServicioAdministrador.php');
require_once(__DIR__ . '/../servicios/ServicioAsientos.php');

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

    public function listarGrupos()
    {
        $respuesta = $this->servicioAdmin->listarGrupos();
        echo json_encode($respuesta);
    }

    public function obtenerMetricas()
    {
        $respuesta = $this->servicioAdmin->obtenerMetricas();
        echo json_encode($respuesta);
    }

    public function editarAlumno()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->editarAlumno($input);
        echo json_encode($respuesta);
    }

    public function resetearConfirmaciones()
    {
        $respuesta = $this->servicioAdmin->resetearConfirmaciones();
        echo json_encode($respuesta);
    }

    public function exportarEscaneados($evento)
    {
        $respuesta = $this->servicioAdmin->obtenerEscaneados($evento);
        echo json_encode($respuesta);
    }

    public function eliminarAlumnos()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->eliminarAlumnos($input);
        echo json_encode($respuesta);
    }

    public function limpiarAsignaciones()
    {
        $servicio = new ServicioAsientos();
        $resultado = $servicio->limpiarAsignaciones();
        echo json_encode($resultado);
    }

    public function ejecutarAsignacion()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $dryRun = isset($data['dry_run']) ? (bool)$data['dry_run'] : false;
        $servicio = new ServicioAsientos();
        $resultado = $servicio->ejecutarAsignacion($dryRun);
        echo json_encode($resultado);
    }

    public function estadoAsignacion()
    {
        $servicio = new ServicioAsientos();
        $resultado = $servicio->obtenerEstadoAsignacion();
        echo json_encode($resultado);
    }

    public function publicarResultados()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $publicado = isset($data['publicado']) ? (bool)$data['publicado'] : false;
        $servicio = new ServicioAsientos();
        $resultado = $servicio->publicarResultados($publicado);
        echo json_encode($resultado);
    }

    public function enviarQRs()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        require_once __DIR__ . '/../servicios/ServicioCorreo.php';
        $servicio = new ServicioCorreo();
        $respuesta = $servicio->enviarQRsPorGrupo($input['carrera'] ?? '', $input['turno'] ?? '');
        echo json_encode($respuesta);
    }

    public function enviarQRIndividual()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        require_once __DIR__ . '/../servicios/ServicioCorreo.php';
        $servicio = new ServicioCorreo();
        $respuesta = $servicio->enviarQRIndividual($input['numCuenta'] ?? '');
        echo json_encode($respuesta);
    }
}
