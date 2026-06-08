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
        $qr = $this->qrModelo->obtenerPorNumCuentaConGrupo($numCuenta);
        if ($qr && isset($qr['qr_habilitado']) && $qr['qr_habilitado'] == 0) {
            throw new Exception("El acceso por código QR para tu grupo está deshabilitado temporalmente.");
        }
        return $qr;
    }

    public function validarTokenSolo($token)
    {
        $qr = $this->qrModelo->obtenerPorTokenConGrupo($token);

        if (!$qr) {
            return ["success" => false, "message" => "Token inválido"];
        }

        if (isset($qr['qr_habilitado']) && $qr['qr_habilitado'] == 0) {
            return ["success" => false, "message" => "El acceso por código QR para este grupo está deshabilitado temporalmente", "data" => $qr];
        }

        if ($qr['escaneado'] == 1) {
            return ["success" => false, "message" => "Este pase ya ha sido utilizado", "data" => $qr];
        }

        return [
            "success" => true,
            "message" => "QR válido y disponible",
            "data" => $qr
        ];
    }

    public function validarAcceso($token)
    {
        $qr = $this->qrModelo->obtenerPorTokenConGrupo($token);

        if (!$qr) {
            return ["success" => false, "message" => "Token inválido"];
        }

        if (isset($qr['qr_habilitado']) && $qr['qr_habilitado'] == 0) {
            return ["success" => false, "message" => "El acceso por código QR para este grupo está deshabilitado temporalmente", "data" => $qr];
        }

        if ($qr['escaneado'] == 1) {
            return ["success" => false, "message" => "Este pase ya ha sido utilizado", "data" => $qr];
        }

        // Marcar como escaneado
        $this->qrModelo->marcarEscaneado($token);

        return [
            "success" => true,
            "message" => "Acceso permitido",
            "data" => $qr
        ];
    }

    public function toggleAccesoGrupo($grupo, $accion)
    {
        // 1. Get all carrera/turno combinations for this short name
        $detalles = $this->grupoModelo->obtenerDetallesGrupo($grupo);
        if (empty($detalles))
            return false;

        $allNumsCuenta = [];

        // 2. Collect all students from all variations of this group
        foreach ($detalles as $detalle) {
            $alumnos = $this->alumnoModelo->obtenerConfirmadosPorGrupo($detalle['carrera'], $detalle['turno']);
            foreach ($alumnos as $alumno) {
                $allNumsCuenta[] = $alumno['numCuenta'];
            }
        }

        if (empty($allNumsCuenta)) {
            // Even if no students, update the group status
            return $this->grupoModelo->actualizarEstado($grupo, ($accion === 'habilitar' ? 1 : 0));
        }

        // 3. Perform the bulk QR action
        if ($accion === 'habilitar') {
            $this->grupoModelo->actualizarEstado($grupo, 1);
            return $this->qrModelo->habilitarGrupo($allNumsCuenta);
        } else {
            $this->grupoModelo->actualizarEstado($grupo, 0);
            return $this->qrModelo->deshabilitarGrupo($allNumsCuenta);
        }
    }

    public function obtenerEstadoGrupo($grupo)
    {
        return $this->grupoModelo->obtenerEstado($grupo);
    }

    public function marcarEscaneado($token)
    {
        return $this->qrModelo->marcarEscaneado($token);
    }

    public function resetearEvento($evento)
    {
        $evento = strtolower(trim($evento));
        if (!in_array($evento, ['li', 'lisi'])) {
            throw new Exception("Evento inválido. Debe ser 'li' o 'lisi'.");
        }
        return $this->qrModelo->resetearPorEvento($evento);
    }
}
