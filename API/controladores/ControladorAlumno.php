<?php

require_once __DIR__ . '/../servicios/ServicioAlumno.php';

class AlumnoController {

    private $servicio;

    public function __construct() {
        $this->servicio = new ServicioAlumno();
    }

    public function buscarAlumno() {

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "JSON inválido"
            ]);
            return;
        }

        echo $this->servicio->buscarAlumno($datos);
    }

    public function confirmarAsistencia() {

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "JSON inválido"
            ]);
            return;
        }

        echo $this->servicio->confirmarAsistencia($datos);
    }
}
