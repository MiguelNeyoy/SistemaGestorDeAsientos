<?php


class AsientoModel{
    private $db;


    public function __construct() {
        require_once(__DIR__ . './API/configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    #Visualizacion para el administrador
    public function mostrarTodosLosAsientos(){
        $sql = 'SELECT alumno.nombre,alumno.apellido,asiento.letra,asiento.numero  
                FROM asiento
                INNER JOIN alumno
                ON asiento.numCuenta = alumno.numCuenta';
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    #Visualizacion para el administrador
    public function mostrarSoloUnAsiento($numCuenta){

    $sql = 'SELECT alumno.nombre, alumno.apellido, asiento.letra, asiento.numero
            FROM alumno
            INNER JOIN asiento
            ON asiento.numCuenta = alumno.numCuenta
            WHERE alumno.numCuenta = :numCuenta';

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':numCuenta', $numCuenta);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    #Visualizacion para el administrador
    public function estadoAsiento($numCuenta){

    $sql = 'SELECT letra,numero,estado
            FROM asiento';

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':numCuenta', $numCuenta);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
    }



}#Fin de la Clase AsientoModel