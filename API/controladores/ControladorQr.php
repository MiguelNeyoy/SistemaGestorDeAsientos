<?php

require_once __DIR__ . '/../servicios/ServicioQr.php';

class ControladorQr {
    private $servicioQr;

    public function __construct() {
        $this->servicioQr = new ServicioQr();
    }

    /**
     * Devuelve el QR del alumno autenticado (Prueba Piloto)
     */
    public function obtenerQrAlumno() {
        // numCuenta comes from JWT, injected in $_SERVER by index.php
        $numCuenta = $_SERVER['JWT_NUMERO_CUENTA'] ?? null;
        
        if (!$numCuenta) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "No autorizado"]);
            return;
        }

        $qr = $this->servicioQr->obtenerQrAlumno($numCuenta);
        
        if ($qr) {
            echo json_encode(["success" => true, "data" => $qr]);
        } else {
            // No QR yet (pilot mode or not confirmed)
            echo json_encode(["success" => false, "message" => "Pase no disponible aún."]);
        }
    }

    /**
     * Valida un token QR sin marcar (Admin only)
     */
    public function validarQr() {
        $data = json_decode(file_get_contents("php://input"), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Token no proporcionado"]);
            return;
        }

        $result = $this->servicioQr->validarTokenSolo($token);
        echo json_encode($result);
    }

    /**
     * Marca un token QR como escaneado (Admin only)
     */
    public function marcarQr() {
        $data = json_decode(file_get_contents("php://input"), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Token no proporcionado"]);
            return;
        }

        $validacion = $this->servicioQr->validarTokenSolo($token);

        if (!$validacion['success']) {
            echo json_encode($validacion);
            return;
        }

        $this->servicioQr->marcarEscaneado($token);

        echo json_encode([
            "success" => true,
            "message" => "QR marcado como utilizado",
            "data" => $validacion['data']
        ]);
    }

    /**
     * Habilita/Deshabilita el acceso QR para un grupo (Admin only)
     */
    public function toggleGrupo() {
        $data = json_decode(file_get_contents("php://input"), true);
        $grupo = $data['grupo'] ?? null;
        $accion = $data['accion'] ?? null; // 'habilitar' | 'deshabilitar'

        if (!$grupo || !$accion) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Datos incompletos"]);
            return;
        }

        $success = $this->servicioQr->toggleAccesoGrupo($grupo, $accion);
        
        if ($success) {
            echo json_encode(["success" => true, "message" => "Operación completada con éxito para el grupo $grupo"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error al procesar la acción de grupo"]);
        }
    }

    public function obtenerEstadoGrupo() {
        $grupo = $_GET['grupo'] ?? null;
        if (!$grupo) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Grupo no proporcionado"]);
            return;
        }

        $estado = $this->servicioQr->obtenerEstadoGrupo($grupo);
        echo json_encode(["success" => true, "data" => $estado]);
    }
}
