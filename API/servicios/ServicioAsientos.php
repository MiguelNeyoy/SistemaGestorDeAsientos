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

            $config = $this->modelo->obtenerEstadoConfig();
            $asignacionPublicada = (bool)($config['publicado'] ?? false);

            return $this->respuesta(true, "Mapa de asientos obtenido", 200, [
                'mi_grupo' => $miGrupo,
                'mi_asiento' => $miAsiento,
                'asientos' => $asientosProcesados,
                'asignacion_publicada' => $asignacionPublicada
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

        if (strpos($carLower, 'virtual') !== false) {
            return 'LISI-V';
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

    public function ejecutarAsignacion($dryRun = false)
    {
        try {
            $alumnos = $this->modelo->obtenerAlumnosConfirmadosPorEvento();

            if (empty($alumnos)) {
                return $this->respuesta(false, "No hay alumnos confirmados para asignar", 400);
            }

            $alumnosLi = [];
            $alumnosLisi = [];
            foreach ($alumnos as $alumno) {
                $evento = $this->determinarEvento($alumno['carrera']);
                if ($evento === 'li') {
                    $alumnosLi[] = $alumno;
                } else {
                    $alumnosLisi[] = $alumno;
                }
            }

            if (!$dryRun) {
                $this->modelo->limpiarTabla('asiento_evento_li');
                $this->modelo->limpiarTabla('asiento_evento_lisi');
            }

            $resultados = ['li' => 0, 'lisi' => 0, 'sin_asiento' => 0, 'dry_run' => $dryRun];

            $eventos = ['li' => $alumnosLi, 'lisi' => $alumnosLisi];
            foreach ($eventos as $evento => $alumnosEvento) {
                if (empty($alumnosEvento)) continue;

                $tabla = "asiento_evento_" . $evento;
                $asientos = $this->modelo->obtenerAsientosDisponibles($tabla);
                $totalAssignable = min(count($alumnosEvento), count($asientos));
                $resultados[$evento] = $totalAssignable;

                if ($totalAssignable === 0 || $dryRun) continue;

                for ($i = 0; $i < $totalAssignable; $i += 50) {
                    $batch = [];
                    $limit = min(50, $totalAssignable - $i);
                    for ($j = 0; $j < $limit; $j++) {
                        $idx = $i + $j;
                        $batch[] = [
                            'numCuenta' => $alumnosEvento[$idx]['numCuenta'],
                            'idAsiento' => $asientos[$idx]['idAsiento']
                        ];
                    }
                    $this->modelo->asignarBatch($tabla, $batch);
                }
            }

            $totalAlumnos = count($alumnosLi) + count($alumnosLisi);
            $totalAsignados = $resultados['li'] + $resultados['lisi'];
            $resultados['sin_asiento'] = $totalAlumnos - $totalAsignados;

            if (!$dryRun) {
                $this->modelo->guardarFechaAsignacion();
            }

            $mensaje = $dryRun ? "simulada" : "completada";
            return $this->respuesta(true, "Asignación " . $mensaje . " correctamente", 200, $resultados);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo en ejecutarAsignacion. Detalle: " . $e->getMessage(), 500);
        }
    }

    /**
     * Compacta los asientos de un evento, eliminando huecos.
     * Limpia la tabla y reasigna todos los alumnos confirmados en orden consecutivo.
     * Se puede llamar directamente con el nombre del evento sin necesitar un numCuenta.
     */
    public function compactarEvento($evento)
    {
        try {
            $evento = strtolower(trim($evento));
            if (!in_array($evento, ['li', 'lisi'])) {
                return $this->respuesta(false, "Evento inválido. Debe ser 'li' o 'lisi'.", 400);
            }

            $tabla = 'asiento_evento_' . $evento;

            $todosConfirmados = $this->modelo->obtenerAlumnosConfirmadosPorEvento();

            $alumnosEvento = array_filter($todosConfirmados, function ($a) use ($evento) {
                return $this->determinarEvento($a['carrera']) === $evento;
            });
            $alumnosEvento = array_values($alumnosEvento);

            $this->modelo->limpiarTabla($tabla);

            if (empty($alumnosEvento)) {
                return $this->respuesta(true, "Evento compactado (sin alumnos confirmados)", 200);
            }

            $asientos = $this->modelo->obtenerAsientosDisponibles($tabla);
            $total = min(count($alumnosEvento), count($asientos));

            for ($i = 0; $i < $total; $i += 50) {
                $batch = [];
                $limit = min(50, $total - $i);
                for ($j = 0; $j < $limit; $j++) {
                    $idx = $i + $j;
                    $batch[] = [
                        'numCuenta' => $alumnosEvento[$idx]['numCuenta'],
                        'idAsiento' => $asientos[$idx]['idAsiento']
                    ];
                }
                $this->modelo->asignarBatch($tabla, $batch);
            }

            return $this->respuesta(true, "Asientos compactados correctamente para " . strtoupper($evento), 200);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo al compactar evento. Detalle: " . $e->getMessage(), 500);
        }
    }

    public function reAsignarEvento($numCuenta)
    {
        try {
            $alumno = $this->modeloAlumno->buscarPorNumeroCuenta($numCuenta);
            if (!$alumno) {
                return $this->respuesta(false, "Alumno no encontrado", 404);
            }

            $evento = $this->determinarEvento($alumno['carrera']);
            $resultado = $this->compactarEvento($evento);

            $this->modelo->guardarFechaAsignacion();

            return $resultado;
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo en reAsignarEvento. Detalle: " . $e->getMessage(), 500);
        }
    }

    private function determinarEvento($carrera)
    {
        $carLower = strtolower(trim($carrera));
        if (strpos($carLower, 'informática') !== false || strpos($carLower, 'informatica') !== false) {
            return 'li';
        }
        return 'lisi';
    }

    public function limpiarAsignaciones()
    {
        try {
            $this->modelo->limpiarTabla('asiento_evento_li');
            $this->modelo->limpiarTabla('asiento_evento_lisi');
            return $this->respuesta(true, "Asignaciones limpiadas correctamente", 200);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo al limpiar asignaciones. Detalle: " . $e->getMessage(), 500);
        }
    }

    public function obtenerEstadoAsignacion()
    {
        try {
            $config = $this->modelo->obtenerEstadoConfig();
            $confirmados = $this->modelo->obtenerAlumnosConfirmadosPorEvento();
            $totalConfirmados = count($confirmados);

            $db = $this->modelo->getDb();
            $stmtLi = $db->query("SELECT COUNT(*) as total FROM asiento_evento_li WHERE numCuenta IS NOT NULL");
            $asignadosLi = (int)$stmtLi->fetch(PDO::FETCH_ASSOC)['total'];
            $stmtLisi = $db->query("SELECT COUNT(*) as total FROM asiento_evento_lisi WHERE numCuenta IS NOT NULL");
            $asignadosLisi = (int)$stmtLisi->fetch(PDO::FETCH_ASSOC)['total'];

            $stmtLiTotal = $db->query("SELECT COUNT(*) as total FROM asiento_evento_li");
            $totalLi = (int)$stmtLiTotal->fetch(PDO::FETCH_ASSOC)['total'];
            $stmtLisiTotal = $db->query("SELECT COUNT(*) as total FROM asiento_evento_lisi");
            $totalLisi = (int)$stmtLisiTotal->fetch(PDO::FETCH_ASSOC)['total'];

            return $this->respuesta(true, "Estado obtenido", 200, [
                'asignado' => $config['fecha_asignacion'] !== null,
                'fecha_asignacion' => $config['fecha_asignacion'],
                'publicado' => (int)$config['publicado'] === 1,
                'confirmados' => $totalConfirmados,
                'capacidad' => $totalLi + $totalLisi,
                'asignados_li' => $asignadosLi,
                'asignados_lisi' => $asignadosLisi
            ]);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo al obtener estado de asignacion. Detalle: " . $e->getMessage(), 500);
        }
    }

    public function publicarResultados($publicado)
    {
        try {
            $this->modelo->actualizarPublicado($publicado ? 1 : 0);
            $mensaje = $publicado ? "Resultados publicados correctamente" : "Resultados ocultados correctamente";
            return $this->respuesta(true, $mensaje, 200);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error en ServicioAsientos: Fallo al publicar resultados. Detalle: " . $e->getMessage(), 500);
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
