<?php

require_once __DIR__ . '/../modelos/QrModelo.php';
require_once __DIR__ . '/../modelos/AlumnoModelo.php';

class ServicioQr
{
    private $qrModelo;
    private $alumnoModelo;

    public function __construct()
    {
        $this->qrModelo = new QrModelo();
        $this->alumnoModelo = new AlumnoModel();
    }

    public function obtenerQrAlumno($numCuenta)
    {
        // Only return if alumno is confirmed
        $alumno = $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);
        // We need to check if they are confirmed (asistencia = 1)
        // Note: The current DB has 'asistencia' table with 'estado'.
        // I need to verify how AlumnoModelo gets this.

        return $this->qrModelo->obtenerPorNumCuenta($numCuenta);
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
        // We need a custom query for this.
        $alumnos = $this->alumnoModelo->obtenerConfirmadosPorGrupo($carrera, $turno);

        $numsCuenta = array_map(function ($a) {
            return $a['numCuenta']; }, $alumnos);

        if ($accion === 'habilitar') {
            return $this->qrModelo->habilitarGrupo($numsCuenta);
        } else {
            return $this->qrModelo->deshabilitarGrupo($numsCuenta);
        }
    }
}
