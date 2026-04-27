<?php

require_once __DIR__ . '/../modelos/ModeloAsiento.php';

class ServicioAsientos
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new ModeloAsiento();
    }

    public function obtenerMapaAsientos()
    {
        try {
            $asientos = $this->modelo->obtenerTodosLosAsientos();

            $mapa = [];
            foreach ($asientos as $asiento) {
                $idAsiento = $asiento['letra'] . $asiento['numero'];
                $mapa[] = [
                    'asiento' => $idAsiento,
                    'letra' => $asiento['letra'],
                    'numero' => $asiento['numero'],
                    'numCuenta' => $asiento['numCuenta'],
                    'nombre' => $asiento['nombre'] ?? null,
                    'apellido' => $asiento['apellido'] ?? null,
                    'ocupado' => $asiento['numCuenta'] !== null
                ];
            }

            return $this->respuesta(true, "Mapa de asientos obtenido", 200, ['asientos' => $mapa]);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: No se pudo obtener el mapa. Detalle: " . $e->getMessage(), 500);
        }
    }

    public function obtenerMiAsiento($numCuenta)
    {
        if (empty($numCuenta)) {
            return $this->respuesta(false, "Número de cuenta requerido", 400);
        }

        try {
            $asiento = $this->modelo->obtenerAsientoPorAlumno($numCuenta);

            if (!$asiento) {
                return $this->respuesta(false, "No tienes un asiento asignado", 404);
            }

            return $this->respuesta(true, "Asiento encontrado", 200, [
                'letra' => $asiento['letra'],
                'numero' => $asiento['numero']
            ]);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: No se pudo obtener tu asiento. Detalle: " . $e->getMessage(), 500);
        }
    }

    private function respuesta($success, $message, $code, $data = null)
    {
        http_response_code($code);
        return [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
    }
}
