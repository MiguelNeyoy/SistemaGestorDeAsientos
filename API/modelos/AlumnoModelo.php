<?php
// API/modelos/AlumnoModelo.php

require_once __DIR__ . '/../configuration/ConexionDB.php';

class AlumnoModel {

    private $db;

    public function __construct() {
        $this->db = Conexion::Conectar();

        if (!$this->db) {
            throw new Exception("Error de conexión a la base de datos.");
        }
    }

    public function obtenerAlumnos() {
        try {
            $sql = "SELECT * FROM alumnos";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerAlumnos: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorNumeroCuenta($numero_cuenta) {
        try {
            $query = "SELECT id_alumno, numero_cuenta, nombre, apellido_paterno, 
                             apellido_materno, carrera, semestre 
                      FROM alumnos 
                      WHERE numero_cuenta = :numero_cuenta 
                      LIMIT 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":numero_cuenta", $numero_cuenta, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en buscarPorNumeroCuenta: " . $e->getMessage());
            return null;
        }
    }

    public function actualizarConfirmacion($id_alumno, $asistira, $num_invitados, $correo) {
        try {
            $query = "UPDATE alumnos 
                      SET asistira = :asistira,
                          num_invitados = :num_invitados,
                          correo = :correo,
                          fecha_confirmacion = NOW()
                      WHERE id_alumno = :id_alumno";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":asistira", $asistira, PDO::PARAM_BOOL);
            $stmt->bindParam(":num_invitados", $num_invitados, PDO::PARAM_INT);
            $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
            $stmt->bindParam(":id_alumno", $id_alumno, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en actualizarConfirmacion: " . $e->getMessage());
            return false;
        }
    }

    public function verificarConfirmacion($id_alumno) {
        try {
            $query = "SELECT id_alumno 
                      FROM alumnos 
                      WHERE id_alumno = :id_alumno 
                      AND asistira IS NOT NULL 
                      AND fecha_confirmacion IS NOT NULL
                      LIMIT 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_alumno", $id_alumno, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? true : false;

        } catch (PDOException $e) {
            error_log("Error en verificarConfirmacion: " . $e->getMessage());
            return false;
        }
    }
}
?>
