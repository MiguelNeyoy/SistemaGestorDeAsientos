<?php

require_once __DIR__ . '/../servicios/ServicioAsientos.php';

class ControladorAsientos
{

    private $servicioAsientos;

    public function __construct()
    {
        $this->servicioAsientos = new ServicioAsientos();
    }

    /**
     * Reinicia todos los asientos SOLO PARA TEST
     */
    public function reiniciarTeatro()
    {
        // Lógica para reiniciar el teatro
        http_response_code(200);
        echo json_encode(["success" => true, "mensaje" => "Teatro reiniciado."]);
    }

    /**
     * para ver todos los asiento si es que exite el mapeo del teator
     */
    public function verMapaAsientos()
    {
        // Lógica para ver el mapa de asientos
        $mapa = []; // Simulación de mapa de asientos
        http_response_code(200);
        echo json_encode(["success" => true, "mapa" => $mapa]);
    }
}
