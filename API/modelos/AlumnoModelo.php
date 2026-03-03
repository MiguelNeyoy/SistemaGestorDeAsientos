<?php


class AlumnoModel
{
    private $db;


    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db  = Conexion::Conectar();
    }

    public function obtenerAlumnos()
    {
        $sql = 'SELECT * FROM alumnos';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAlumnosPorNumeroDeCuenta($NumeroCuenta)
    {
        $sql = 'SELECT * FROM alumno WHERE $NumeroCuenta = ?';
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
        $sql = 'SELECT * FROM asistencia WHERE id_alumno = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idAlumno]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function actualizarConfirmacion($idAlumno, $asistira, $numInvitados, $correo)
    {
        try {
            $sql = 'INSERT INTO Confirmaciones (id_alumno, asistira, num_invitados, correo) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    asistira = VALUES(asistira), 
                    num_invitados = VALUES(num_invitados), 
                    correo = VALUES(correo)';

            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([$idAlumno, $asistira, $numInvitados, $correo]);

            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }
}
