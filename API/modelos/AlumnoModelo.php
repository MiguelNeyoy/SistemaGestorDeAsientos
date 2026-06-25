<?php


class AlumnoModel
{
    private $db;


    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    public function getDb()
    {
        return $this->db;
    }

    public function obtenerAlumnos()
    {
        $sql = 'SELECT a.*, asi.estado as asistencia_estado,
                       COALESCE(ali.letra, alisi.letra) as letra,
                       COALESCE(ali.numero, alisi.numero) as numero,
                       COALESCE(ali.idAsiento, alisi.idAsiento) as idAsiento
                FROM alumno a 
                LEFT JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                LEFT JOIN asiento_evento_li ali ON a.numCuenta = ali.numCuenta
                LEFT JOIN asiento_evento_lisi alisi ON a.numCuenta = alisi.numCuenta';
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

    public function obtenerConfirmadosConQRPorGrupo($carrera, $turno)
    {
        $conditions = ['asi.estado = 1'];
        $params = [];

        if ($carrera !== 'ALL') {
            $conditions[] = 'a.carrera = ?';
            $params[] = $carrera;
        }
        if ($turno !== 'ALL') {
            $conditions[] = 'a.turno = ?';
            $params[] = $turno;
        }

        $sql = "SELECT a.numCuenta, a.nombre, a.apellido, a.email, a.carrera, a.turno, q.token
                FROM alumno a
                JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                JOIN qr q ON a.numCuenta = q.numCuenta
                WHERE " . implode(' AND ', $conditions);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerConfirmadosPorGrupo($carrera, $turno)
    {
        $sql = 'SELECT a.numCuenta 
                FROM alumno a 
                JOIN asistencia asi ON a.numCuenta = asi.numCuenta 
                WHERE a.carrera = ? AND a.turno = ? AND asi.estado = 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$carrera, $turno]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resetearConfirmaciones()
    {
        try {
            $this->db->beginTransaction();

            // 1. Eliminar todos los registros de asistencia
            $this->db->exec('DELETE FROM asistencia');

            // 2. Resetear cantidad de invitados
            $this->db->exec('UPDATE alumno SET cantInvitado = 0');

            $this->db->exec('UPDATE alumno SET email = 0');

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene los alumnos que ya escanearon su QR y tienen asiento asignado en el evento.
     * Ordenados por turno (Matutino primero) y luego alfabéticamente por apellido.
     *
     * @param string $evento 'li' o 'lisi'
     * @return array Lista de alumnos escaneados
     */
    public function obtenerEscaneadosPorEvento($evento)
    {
        $tablasPermitidas = ['li' => 'asiento_evento_li', 'lisi' => 'asiento_evento_lisi'];
        if (!isset($tablasPermitidas[$evento])) {
            throw new \InvalidArgumentException("Evento no válido: " . $evento);
        }
        $tabla = $tablasPermitidas[$evento];

        $sql = "SELECT a.numCuenta, a.nombre, a.apellido, a.carrera, a.turno,
                       ae.letra, ae.numero
                FROM alumno a
                INNER JOIN qr q ON a.numCuenta = q.numCuenta
                INNER JOIN {$tabla} ae ON a.numCuenta = ae.numCuenta
                WHERE q.escaneado = 1
                ORDER BY
                    CASE WHEN UPPER(a.turno) IN ('M', '1') THEN 0 ELSE 1 END ASC,
                    a.apellido ASC,
                    a.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarAlumno($numCuenta)
    {
        $sql = 'DELETE FROM alumno WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numCuenta]);
        return $stmt->rowCount() > 0;
    }

    public function eliminarAsistencia($numCuenta)
    {
        $sql = 'DELETE FROM asistencia WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numCuenta]);
    }

    public function eliminarQr($numCuenta)
    {
        $sql = 'DELETE FROM qr WHERE numCuenta = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numCuenta]);
    }
}
