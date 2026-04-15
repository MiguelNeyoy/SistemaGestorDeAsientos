<?php

require_once(__DIR__ . '/../servicios/ServicioQr.php');

class ControladorQr
{
    private $servicioQr;

    public function __construct()
    {
        $this->servicioQr = new ServicioQr();
    }

    /**
     * Genera el qr y manda a llamar al servicio de que crea el token
     */
    public function generarQr()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['numero_cuenta'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Número de cuenta requerido"]);
            return;
        }

        $res = $this->servicioQr->generarQrAlumno($input);
        echo json_encode($res);
    }

    /**
     * Recibe la informacion del QR (Token)
     * Manda a llamar al servicio que valida el qr
     */
    public function validarQr()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['token'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Token de QR no proporcionado"]);
            return;
        }

        $res = $this->servicioQr->validarQrToken($input);
        echo json_encode($res);
    }

    /**
     * Registra la llegada de un alumno tras escanear y confirmar
     */
    public function confirmarLlegada()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['numero_cuenta'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Número de cuenta requerido"]);
            return;
        }

        $res = $this->servicioQr->confirmarLlegadaQr($input);
        echo json_encode($res);
    }
}
