<?php


class AlumnoModel
{
    private $db;


    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    public function obtenerAlumnos()
    {
        $sql = 'SELECT a.*, asi.estado as asistencia_estado 
                FROM alumno a 
                LEFT JOIN asistencia asi ON a.numCuenta = asi.numCuenta';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAlumnosPorNumeroDeCuenta($NumeroCuenta)
    {
        $sql = 'SELECT * FROM alumno WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$NumeroCuenta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorNumeroCuenta($numeroCuenta)
    {
        $sql = 'SELECT * FROM alumno WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numeroCuenta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarConfirmacion($idAlumno)
    {
        $sql = 'SELECT estado FROM asistencia WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idAlumno]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        // Retorna el estado (0 o 1) si existe, false si no existe
        return $resultado !== false ? $resultado['estado'] : false;
    }

    public function actualizarConfirmacion($idAlumno, $asistira, $numInvitados)
    {
        try {
            // Convertir asistira a estado: 1 = "confirmado", 0 = "no_asistira"
            $estado = $asistira ? 1 : 0;

            // 1. Verificar si ya existe registro en asistencia
            $stmtCheck = $this->db->prepare('SELECT COUNT(*) FROM asistencia WHERE numCuenta = ?');
            $stmtCheck->execute([$idAlumno]);
            $existe = $stmtCheck->fetchColumn() > 0;

            if ($existe) {
                $sql = 'UPDATE asistencia SET estado = ? WHERE numCuenta = ?';
                $stmt = $this->db->prepare($sql);
                $resultado = $stmt->execute([$estado, $idAlumno]);
            } else {
                $sql = 'INSERT INTO asistencia (numCuenta, estado) VALUES (?, ?)';
                $stmt = $this->db->prepare($sql);
                $resultado = $stmt->execute([$idAlumno, $estado]);
            }

            if (!$resultado) {
                return ['success' => false, 'error' => 'Error en asistencia'];
            }

            // 2. Si va a asistir, crear registros de invitados
            // if ($asistira && $numInvitados > 0) {
            //     $sqlInvitado = 'INSERT INTO invitado (numCuenta) VALUES (?)';
            //     $stmtInvitado = $this->db->prepare($sqlInvitado);

            //     for ($i = 0; $i < $numInvitados; $i++) {
            //         $resultadoInvitado = $stmtInvitado->execute([$idAlumno]);
            //         if (!$resultadoInvitado) {
            //             return ['success' => false, 'error' => 'Error al insertar invitado'];
            //         }
            //     }
            // }

            // 3. Actualizar cantidad de invitados en tabla alumno
            $sqlAlumno = 'UPDATE alumno SET cantInvitado = ? WHERE numCuenta = ?';
            $stmtAlumno = $this->db->prepare($sqlAlumno);
            $resultadoAlumno = $stmtAlumno->execute([$numInvitados, $idAlumno]);

            if (!$resultadoAlumno) {
                return ['success' => false, 'error' => 'Error al actualizar alumno'];
            }

            return true;
        } catch (PDOException $e) {
            // Retornar error específico de la BD para debugging
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarCorreo($idAlumno, $correo)
    {
        $sql = 'UPDATE alumno SET email = ? WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo, $idAlumno]);
        return $stmt->rowCount() > 0;
    }
}
