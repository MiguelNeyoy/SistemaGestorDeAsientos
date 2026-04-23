<?php

class ModeloAsiento
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    public function obtenerTodosLosAsientos()
    {
        try {
            $sql = 'SELECT a.idAsiento, a.numCuenta, a.letra, a.numero, a.estado,
                           al.nombre, al.apellido
                    FROM asiento a
                    LEFT JOIN alumno al ON a.numCuenta = al.numCuenta
                    ORDER BY a.letra, a.numero';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al obtener asientos - " . $e->getMessage());
        }
    }

    public function obtenerAsientoPorAlumno($numCuenta)
    {
        try {
            $sql = 'SELECT idAsiento, numCuenta, letra, numero, estado
                    FROM asiento
                    WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en ModeloAsiento: Fallo al buscar asiento del alumno - " . $e->getMessage());
        }
    }
}
