<?php

class ModeloAsiento
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    public function obtenerTodosLosAsientos($tabla)
    {
        try {
            $sql = "SELECT a.idAsiento, a.numCuenta, a.letra, a.numero, a.estado,
                           al.nombre, al.apellido
                    FROM {$tabla} a
                    LEFT JOIN alumno al ON a.numCuenta = al.numCuenta
                    ORDER BY a.letra, a.numero";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener asientos - " . $e->getMessage());
        }
    }

    public function obtenerAsientosPorEventoYTurno($evento, $turno)
    {
        try {
            $tabla = "asiento_evento_" . $evento;
            $sql = "SELECT a.idAsiento, a.numCuenta, a.letra, a.numero, a.estado
                    FROM {$tabla} a
                    JOIN alumno al ON a.numCuenta = al.numCuenta
                    WHERE al.turno = ?
                    ORDER BY a.letra, a.numero";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$turno]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener asientos por grupo - " . $e->getMessage());
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
            $sql = "UPDATE {$tabla} SET estado = 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al reiniciar asientos - " . $e->getMessage());
        }
    }
}
