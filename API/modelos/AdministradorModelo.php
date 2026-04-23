<?php

class AdministradorModelo
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    public function verificarAdministrador($usuario)
    {
        $sql = 'SELECT * FROM administrador WHERE usuario = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Removido obtenerInvitadosPorCarrera por no ser utilizado en el frontend

    public function obtenerAlumnosConfirmados()
    {
        $sql = "SELECT alumno.numCuenta, alumno.nombre, alumno.apellido, alumno.carrera, alumno.turno, alumno.cantInvitado 
                FROM alumno 
                JOIN asistencia ON alumno.numCuenta = asistencia.numCuenta 
                WHERE asistencia.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
