<?php

class GrupoModelo {
    private $db;
    private $table = "grupos";

    public function __construct() {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::conectar();
    }

    /**
     * Obtiene el estado de habilitación de un grupo basado en su nombre corto (ej: LI4-1)
     */
    public function obtenerEstado($nombreCorto) {
        $query = "SELECT qr_habilitado FROM " . $this->table . " 
                  WHERE nombre_corto = :nombreCorto LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombreCorto', $nombreCorto);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el estado de habilitación de todos los registros con el mismo nombre corto
     */
    public function actualizarEstado($nombreCorto, $estado) {
        $estado = $estado ? 1 : 0;

        $query = "UPDATE " . $this->table . " 
                  SET qr_habilitado = :estado 
                  WHERE nombre_corto = :nombreCorto";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':nombreCorto', $nombreCorto);
        
        return $stmt->execute();
    }

    /**
     * Obtiene las combinaciones de carrera/turno asociadas a un nombre corto
     */
    public function obtenerDetallesGrupo($nombreCorto) {
        $query = "SELECT carrera, turno FROM " . $this->table . " WHERE nombre_corto = :nombreCorto";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombreCorto', $nombreCorto);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los grupos y sus estados
     */
    public function listarTodos() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
