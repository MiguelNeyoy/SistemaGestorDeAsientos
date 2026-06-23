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
}
