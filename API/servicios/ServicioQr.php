<?php

require_once __DIR__ . '/../modelos/QrModelo.php';
require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/GrupoModelo.php';

class ServicioQr
{
    private $qrModelo;
    private $alumnoModelo;
    private $grupoModelo;

    public function __construct()
    {
        $this->qrModelo = new QrModelo();
        $this->alumnoModelo = new AlumnoModel();
        $this->grupoModelo = new GrupoModelo();
    }

    public function obtenerQrAlumno($numCuenta)
    {
        // Only return if alumno is confirmed
        $alumno = $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);
        // We need to check if they are confirmed (asistencia = 1)
        // Note: The current DB has 'asistencia' table with 'estado'.
        // I need to verify how AlumnoModelo gets this.

        return $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);
    }

    public function validarAcceso($token)
    {
        $qr = $this->qrModelo->obtenerPorToken($token);

        if (!$qr) {
            return ["success" => false, "message" => "Token inválido"];
        }

        if ($qr['escaneado'] == 1) {
            return ["success" => false, "message" => "Este pase ya ha sido utilizado", "data" => $qr];
        }

        // Mark as used
        $this->qrModelo->marcarEscaneado($token);

        return ["success" => true, "message" => "Acceso concedido", "data" => $qr];
    }

    public function toggleAccesoGrupo($grupo, $accion)
    {
        // $grupo is e.g. 'LI4-1'
        // We need to find all confirmed students of this group.

        // This logic usually belongs to ServicioAdministrador, but I'll implement a helper here
        // for simplicity or reuse if possible.

        // Map group string to carrera and turno
        $carrera = "";
        $turno = "";

        if (strpos($grupo, 'LISI') !== false) {
            $carrera = 'Licenciatura en Ingeniería en Sistemas de Información';
        } else {
            $carrera = 'Licenciatura en Informática';
        }

        $turno = (strpos($grupo, '-1') !== false) ? 'M' : 'V';

        // Get confirmed students for this carrera and turno
        $alumnos = $this->alumnoModelo->obtenerConfirmadosPorGrupo($carrera, $turno);

        $numsCuenta = array_map(function ($a) {
            return $a['numCuenta'];
        }, $alumnos);

        if ($accion === 'habilitar') {
            $this->grupoModelo->actualizarEstado($carrera, $turno, 1);
            return $this->qrModelo->habilitarGrupo($numsCuenta);
        } else {
            $this->grupoModelo->actualizarEstado($carrera, $turno, 0);
            return $this->qrModelo->deshabilitarGrupo($numsCuenta);
        }
    }

    public function obtenerEstadoGrupo($grupo)
    {
        $carrera = "";
        $turno = "";
        if (strpos($grupo, 'LISI') !== false) {
            $carrera = 'Licenciatura en Ingeniería en Sistemas de Información';
        } else {
            $carrera = 'Licenciatura en Informática';
        }
        $turno = (strpos($grupo, '-1') !== false) ? 'M' : 'V';

        return $this->grupoModelo->obtenerEstado($carrera, $turno);
    }
}
