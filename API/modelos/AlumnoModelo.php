<?php


class AlumnoModel{
    private $db;


    public function __construct() {
        require_once(__DIR__ . './API/configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    public function obtenerAlumnos(){
        $sql = 'SELECT * FROM Alumnos';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAlumnosPorNumeroDeCuenta($NumeroCuenta){
        $sql = 'SELECT * FROM Alumnos WHERE ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}