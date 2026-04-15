<?php

class QrModelo
{
    private $db;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        $this->db = Conexion::Conectar();
    }

    /**
     * Obtiene el asiento asignado a un alumno
     */
    public function obtenerAsientoAlumno($numCuenta)
    {
        try {
            $sql = 'SELECT letra, numero FROM asiento WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                return null;
            }
            
            return $resultado;
        } catch (PDOException $e) {
            throw new Exception("Error en Modelo [Qr]: Fallo al obtener asiento - " . $e->getMessage());
        }
    }

    /* 
    TODO: Descomentar y utilizar estos métodos cuando la tabla 'qr_codigo' exista en la base de datos.
    
    public function guardarQrGenerado($numCuenta, $tokenJwt, $rutaImagen)
    {
        try {
            $sql = 'INSERT INTO qr_codigo (numCuenta, token_jwt, ruta_imagen) VALUES (?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$numCuenta, $tokenJwt, $rutaImagen]);
        } catch (PDOException $e) {
            throw new Exception("Error en Modelo [Qr]: Fallo al guardar QR - " . $e->getMessage());
        }
    }

    public function obtenerQrPorAlumno($numCuenta)
    {
        try {
            $sql = 'SELECT * FROM qr_codigo WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numCuenta]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error en Modelo [Qr]: Fallo al buscar QR por alumno - " . $e->getMessage());
        }
    }

    public function verificarQrUsado($tokenJwt)
    {
        try {
            $sql = 'SELECT usado FROM qr_codigo WHERE token_jwt = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$tokenJwt]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado !== false ? (bool)$resultado['usado'] : false;
        } catch (PDOException $e) {
            throw new Exception("Error en Modelo [Qr]: Fallo al verificar uso del QR - " . $e->getMessage());
        }
    }

    public function marcarQrComoUsado($numCuenta)
    {
        try {
            $sql = 'UPDATE qr_codigo SET usado = 1, fecha_uso = NOW() WHERE numCuenta = ?';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$numCuenta]);
        } catch (PDOException $e) {
            throw new Exception("Error en Modelo [Qr]: Fallo al marcar QR como usado - " . $e->getMessage());
        }
    }
    */
}
