<?php

require_once __DIR__ . '/../servicios/ServicioAsientos.php';

class ControladorAsientos
{
    private $servicioAsientos;

    public function __construct()
    {
        $this->servicioAsientos = new ServicioAsientos();
    }

    public function verMapaAsientos($evento = 'li')
    {
        try {
            $numCuenta = $_SERVER['JWT_NUMERO_CUENTA'] ?? null;
            $res = $this->servicioAsientos->obtenerMapaAsientos($evento, $numCuenta);
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

    public function reiniciarTeatro($evento = 'li')
    {
        try {
            // Verificar si hay token de admin (asumiendo que index.php valida el token de rutas protegidas)
            if (!isset($_SERVER['JWT_ADMIN_ID'])) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "Acceso denegado"]);
                return;
            }

            $res = $this->servicioAsientos->reiniciarTeatro($evento);
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
