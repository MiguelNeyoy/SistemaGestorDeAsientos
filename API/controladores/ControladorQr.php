<?php

class ControladorQr
{

    public function __construct()
    {
        // En el futuro, aquí se podría inicializar un servicio para QR
        // require_once __DIR__ . '/../servicios/ServicioQr.php';
        // $this->servicioQr = new ServicioQr();
    }

    /**
     * Genera el qr y manda a llamar al servicio de que crea el token
     */
    public function generarQr()
    {
        // Lógica para generar un QR
        http_response_code(200);
        echo json_encode(["success" => true, "mensaje" => "QR generado."]);
    }

    /**
     * Recibe la informacion del QR
     * Manda a llamar al servicio que valida el qr
     * Si la validacion es exitosa se llama a
     * Asignar lugar
     */
    public function validarQr()
    {
        // Lógica para validar un QR
        http_response_code(200);
        echo json_encode(["success" => true, "mensaje" => "QR validado."]);
    }
}
