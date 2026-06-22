<?php

require_once __DIR__ . '/../modelos/ModeloAsiento.php';
require_once __DIR__ . '/../modelos/AlumnoModelo.php';

class ServicioAsientos
{
    private $modelo;
    private $modeloAlumno;

    public function __construct()
    {
        $this->modelo = new ModeloAsiento();
        $this->modeloAlumno = new AlumnoModel();
    }

    public function obtenerMapaAsientos($evento, $numCuenta = null, $jwtEventoId = null, $jwtAdminId = null)
    {
        $evento = strtolower(trim($evento));
        if (!in_array($evento, ['li', 'lisi'])) {
            return $this->respuesta(false, "Evento inválido. Debe ser 'li' o 'lisi'.", 400);
        }

        if ($jwtAdminId === null && $jwtEventoId !== null && $jwtEventoId !== $evento) {
            return $this->respuesta(false, "No tienes acceso a este evento", 403);
        }

        try {
            $miGrupo = null;
            $asientosRaw = $this->obtenerAsientosRaw($evento, $numCuenta, $miGrupo);

            $miAsiento = null;
            $asientosProcesados = $this->procesarAsientosMapa($asientosRaw, $numCuenta, $jwtAdminId, $miAsiento);

            return $this->respuesta(true, "Mapa de asientos obtenido", 200, [
                'mi_grupo' => $miGrupo,
                'mi_asiento' => $miAsiento,
                'asientos' => $asientosProcesados
            ]);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: No se pudo obtener el mapa. Detalle: " . $e->getMessage(), 500);
        }
    }

    private function obtenerAsientosRaw($evento, $numCuenta, &$miGrupo)
    {
        if ($numCuenta) {
            $alumno = $this->modeloAlumno->buscarPorNumeroCuenta($numCuenta);
            if ($alumno) {
                $miGrupo = $this->calcularGrupo($alumno['carrera'] ?? '', $alumno['turno']);
                return $this->modelo->obtenerTodosLosAsientosConTurno($evento);
            }
            return [];
        }

        $tabla = "asiento_evento_" . $evento;
        return $this->modelo->obtenerTodosLosAsientos($tabla);
    }

    private function procesarAsientosMapa($asientosRaw, $numCuenta, $jwtAdminId, &$miAsiento)
    {
        $asientos = [];
        foreach ($asientosRaw as $asiento) {
            $idAsiento = $asiento['letra'] . $asiento['numero'];
            $esAsignado = ($asiento['numCuenta'] !== null && $asiento['numCuenta'] === $numCuenta);
            $tieneNumCuenta = ($asiento['numCuenta'] !== null);

            if ($esAsignado) {
                $miAsiento = $idAsiento;
            }

            $dataAsiento = [
                'id_asiento' => $idAsiento,
                'fila' => $asiento['letra'],
                'numero' => $asiento['numero'],
                'turno' => $asiento['turno'] ?? null,
                'estado' => $tieneNumCuenta ? 'ocupado' : 'libre',
                'asignado' => $esAsignado,
                'confirmado' => isset($asiento['asistencia_estado']) && $asiento['asistencia_estado'] == 1,
                'escaneado' => isset($asiento['escaneado']) && $asiento['escaneado'] == 1
            ];

            if ($jwtAdminId !== null && $tieneNumCuenta) {
                $dataAsiento['numCuenta'] = $asiento['numCuenta'];
            }

            $asientos[] = $dataAsiento;
        }
        return $asientos;
    }

    private function calcularGrupo($carrera, $turno)
    {
        $carLower = strtolower(trim($carrera));
        $turnoUpper = strtoupper(trim($turno));

        $prefix = 'LISI';
        if (strpos($carLower, 'informática') !== false || strpos($carLower, 'informatica') !== false) {
            $prefix = 'LI';
        }

        $turnoNum = ($turnoUpper === 'M' || $turnoUpper === '1') ? '1' : '2';

        return "{$prefix}4-{$turnoNum}";
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

    public function reiniciarTeatro($evento)
    {
        $evento = strtolower(trim($evento));
        if (!in_array($evento, ['li', 'lisi'])) {
            return $this->respuesta(false, "Evento inválido. Debe ser 'li' o 'lisi'.", 400);
        }
        $tabla = "asiento_evento_" . $evento;

        try {
            $this->modelo->reiniciarTeatro($tabla);
            return $this->respuesta(true, "Teatro reiniciado correctamente para el evento " . strtoupper($evento), 200);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: No se pudo reiniciar el teatro. Detalle: " . $e->getMessage(), 500);
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
