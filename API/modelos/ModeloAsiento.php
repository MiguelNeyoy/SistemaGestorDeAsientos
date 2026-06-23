<?php

class ModeloAsiento
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    private function validarTabla($tabla)
    {
        $tablasPermitidas = ['asiento_evento_li', 'asiento_evento_lisi'];
        if (!in_array($tabla, $tablasPermitidas, true)) {
            throw new InvalidArgumentException("Tabla no permitida: " . $tabla);
        }
        return $tabla;
    }

    public function obtenerTodosLosAsientos($tabla)
    {
        try {
            $tabla = $this->validarTabla($tabla);
            $sql = "SELECT a.idAsiento, a.numCuenta, a.letra, a.numero, a.estado,
                           al.nombre, al.apellido,
                           COALESCE(asi.estado, 0) AS asistencia_estado,
                           COALESCE(q.escaneado, 0) AS escaneado
                    FROM {$tabla} a
                    LEFT JOIN alumno al ON a.numCuenta = al.numCuenta
                    LEFT JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                    LEFT JOIN qr q ON a.numCuenta = q.numCuenta
                    ORDER BY a.letra, a.numero";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener asientos - " . $e->getMessage());
        }
    }

    public function obtenerTodosLosAsientosConTurno($evento)
    {
        try {
            $tabla = "asiento_evento_" . $evento;
            $tabla = $this->validarTabla($tabla);
            $sql = "SELECT a.idAsiento, a.numCuenta, a.letra, a.numero, a.estado,
                           al.turno,
                           COALESCE(asi.estado, 0) AS asistencia_estado,
                           COALESCE(q.escaneado, 0) AS escaneado
                    FROM {$tabla} a
                    LEFT JOIN alumno al ON a.numCuenta = al.numCuenta
                    LEFT JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                    LEFT JOIN qr q ON a.numCuenta = q.numCuenta
                    ORDER BY a.letra, a.numero";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener todos los asientos con turno - " . $e->getMessage());
        }
    }

    public function obtenerAsientoPorAlumno($numCuenta)
    {
        try {
            $sql = "SELECT idAsiento, numCuenta, letra, numero, estado, 'LI' as evento
                    FROM asiento_evento_li
                    WHERE numCuenta = ?
                    UNION
                    SELECT idAsiento, numCuenta, letra, numero, estado, 'LISI' as evento
                    FROM asiento_evento_lisi
                    WHERE numCuenta = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta, $numCuenta]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al buscar asiento del alumno - " . $e->getMessage());
        }
    }

    public function reiniciarTeatro($tabla)
    {
        try {
            $tabla = $this->validarTabla($tabla);
            $sql = "UPDATE {$tabla} SET estado = 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al reiniciar asientos - " . $e->getMessage());
        }
    }

    public function liberarAsientoPorAlumno($numCuenta)
    {
        try {
            $sql = 'UPDATE asiento_evento_li SET numCuenta = NULL, estado = 0 WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta]);

            $sql = 'UPDATE asiento_evento_lisi SET numCuenta = NULL, estado = 0 WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta]);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al liberar asiento por alumno - " . $e->getMessage());
        }
    }

    public function limpiarTabla($tabla)
    {
        try {
            $tabla = $this->validarTabla($tabla);
            $sql = "UPDATE {$tabla} SET numCuenta = NULL, estado = 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al limpiar tabla - " . $e->getMessage());
        }
    }

    public function obtenerAsientosDisponibles($tabla)
    {
        try {
            $tabla = $this->validarTabla($tabla);
            $sql = "SELECT idAsiento FROM {$tabla} ORDER BY letra ASC, numero ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener asientos disponibles - " . $e->getMessage());
        }
    }

    public function obtenerAlumnosConfirmadosPorEvento()
    {
        try {
            $sql = "SELECT a.numCuenta, a.nombre, a.apellido, a.turno, a.carrera
                    FROM alumno a
                    JOIN asistencia asi ON a.numCuenta = asi.numCuenta
                    WHERE asi.estado = 1
                    ORDER BY
                        CASE WHEN UPPER(a.turno) IN ('M', '1') THEN 0 ELSE 1 END ASC,
                        a.apellido ASC,
                        a.nombre ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener alumnos confirmados - " . $e->getMessage());
        }
    }

    public function asignarBatch($tabla, $asignaciones)
    {
        try {
            $tabla = $this->validarTabla($tabla);
            $this->db->beginTransaction();
            $sql = "UPDATE {$tabla} SET numCuenta = ? WHERE idAsiento = ?";
            $stmt = $this->db->prepare($sql);
            foreach ($asignaciones as $asignacion) {
                $stmt->execute([$asignacion['numCuenta'], $asignacion['idAsiento']]);
            }
            return $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Error en ModeloAsiento: Fallo al asignar batch - " . $e->getMessage());
        }
    }

    public function guardarFechaAsignacion()
    {
        try {
            $sql = "INSERT INTO config_asignacion (id, publicado, fecha_asignacion)
                    VALUES (1, 0, NOW())
                    ON DUPLICATE KEY UPDATE fecha_asignacion = NOW()";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al guardar fecha de asignacion - " . $e->getMessage());
        }
    }

    public function obtenerEstadoConfig()
    {
        try {
            $sql = "SELECT publicado, fecha_asignacion, fecha_publicacion
                    FROM config_asignacion WHERE id = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row === false) {
                return ['publicado' => 0, 'fecha_asignacion' => null, 'fecha_publicacion' => null];
            }
            return $row;
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener estado config - " . $e->getMessage());
        }
    }

    public function actualizarPublicado($publicado)
    {
        try {
            $sql = "INSERT INTO config_asignacion (id, publicado, fecha_publicacion)
                    VALUES (1, ?, NOW())
                    ON DUPLICATE KEY UPDATE publicado = ?, fecha_publicacion = IF(? = 1, NOW(), fecha_publicacion)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$publicado, $publicado, $publicado]);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al actualizar publicado - " . $e->getMessage());
        }
    }

    public function getDb()
    {
        return $this->db;
    }
}
