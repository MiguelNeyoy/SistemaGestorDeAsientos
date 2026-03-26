<?php

class AdministradorModelo
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    public function verificarAdministrador($usuario)
    {
        $sql = 'SELECT * FROM administrador WHERE usuario = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerMetricas()
    {
        $sql = "SELECT alumno.numCuenta, alumno.nombre, alumno.apellido, alumno.carrera, alumno.turno, alumno.cantInvitado 
                FROM alumno 
                JOIN asistencia ON alumno.numCuenta = asistencia.numCuenta 
                WHERE asistencia.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $alumnosConfirmados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_invitados = 0;
        $por_turno = ['M' => 0, 'V' => 0];
        $por_carrera = [];
        $individual = [];

        foreach ($alumnosConfirmados as $alumno) {
            $invitados = (int)$alumno['cantInvitado'];
            $total_invitados += $invitados;
            
            $turno = strtoupper(trim($alumno['turno']));
            if (isset($por_turno[$turno])) {
                $por_turno[$turno] += $invitados;
            } else {
                $por_turno[$turno] = $invitados;
            }

            $carreraRaw = strtolower(trim($alumno['carrera']));
            $carreraAgrupada = 'Otra';
            if (strpos($carreraRaw, 'informática') !== false || strpos($carreraRaw, 'informatica') !== false) {
                $carreraAgrupada = 'Licenciatura en Informática';
            } elseif (strpos($carreraRaw, 'sistemas de información') !== false || strpos($carreraRaw, 'sistemas de informacion') !== false) {
                $carreraAgrupada = 'Ingeniería en Sistemas de Información';
            }

            if (!isset($por_carrera[$carreraAgrupada])) {
                $por_carrera[$carreraAgrupada] = 0;
            }
            $por_carrera[$carreraAgrupada] += $invitados;

            $individual[] = [
                'numCuenta' => $alumno['numCuenta'],
                'nombre' => trim($alumno['nombre']) . ' ' . trim($alumno['apellido']),
                'carrera' => $carreraAgrupada,
                'turno' => $turno,
                'invitados' => $invitados
            ];
        }

        return [
            'total_invitados' => $total_invitados,
            'por_turno' => $por_turno,
            'por_carrera' => $por_carrera,
            'individual' => $individual
        ];
    }
}
