<?php


class AsientoModel{
    private $db;


    public function __construct() {
        require_once(__DIR__ . './API/configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    public function mostrarTodosLosAsientos(){
        $sql = 'SELECT alumno.nombre,alumno.apellido,asiento.letra,asiento.asiento.numero  
                FROM alumno
                INNER JOIN alumno
                ON asiento.numCuenta = alumno.numCuenta';
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    }

