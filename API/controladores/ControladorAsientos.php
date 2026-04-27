<?php

require_once __DIR__ . '/../servicios/ServicioAsientos.php';

class ControladorAsientos
{
    private $servicioAsientos;

    public function __construct()
    {
        $this->servicioAsientos = new ServicioAsientos();
    }

    public function verMapaAsientos()
    {
        try {
            $res = $this->servicioAsientos->obtenerMapaAsientos();
            echo json_encode($res);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error interno en el controlador de asientos"
            ]);
        }
    }

    public function verMiAsiento()
    {
        try {
            $numCuenta = $_SERVER['JWT_NUMERO_CUENTA'] ?? null;

            if (!$numCuenta) {
                http_response_code(401);
                echo json_encode(["success" => false, "message" => "No autorizado"]);
                return;
            }

            $res = $this->servicioAsientos->obtenerMiAsiento($numCuenta);
            echo json_encode($res);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error interno en el controlador de asientos"
            ]);
        }
    }
}
