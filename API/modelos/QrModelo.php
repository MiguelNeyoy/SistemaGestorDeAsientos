<?php

class QrModelo
{
    private $db;
    private $table = "qr";
    private $alumnoModelo;

    public function __construct()
    {
        require_once(__DIR__ . '/../configuracion/ConexionDB.php');
        require_once(__DIR__ . '/AlumnoModelo.php');
        $this->db = Conexion::conectar();
        $this->alumnoModelo = new AlumnoModel();
    }

    public function obtenerPorToken($token)
    {
        $query = "SELECT q.*, a.nombre, a.apellido, a.carrera, a.turno, a.cantInvitado 
                  FROM " . $this->table . " q
                  JOIN alumno a ON q.numCuenta = a.numCuenta
                  WHERE q.token = :token LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerPorNumCuenta($numCuenta)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE numCuenta = :numCuenta LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numCuenta', $numCuenta);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerPorTokenConGrupo($token)
    {
        $query = "SELECT q.*, a.nombre, a.apellido, a.carrera, a.turno, a.cantInvitado, g.qr_habilitado
                  FROM " . $this->table . " q
                  JOIN alumno a ON q.numCuenta = a.numCuenta
                  JOIN grupos g ON (g.carrera = a.carrera AND g.turno = a.turno)
                  WHERE q.token = :token LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorNumCuentaConGrupo($numCuenta)
    {
        $query = "SELECT q.*, a.nombre, a.apellido, a.carrera, a.turno, a.cantInvitado, g.qr_habilitado
                  FROM " . $this->table . " q
                  JOIN alumno a ON q.numCuenta = a.numCuenta
                  JOIN grupos g ON (g.carrera = a.carrera AND g.turno = a.turno)
                  WHERE q.numCuenta = :numCuenta LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numCuenta', $numCuenta);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function crear($numCuenta, $token)
    {
        $query = "INSERT INTO " . $this->table . " (numCuenta, token, fecha_creacion, enviado, escaneado) 
                  VALUES (:numCuenta, :token, NOW(), 0, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numCuenta', $numCuenta);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    public function marcarEscaneado($token)
    {
        $query = "UPDATE " . $this->table . " SET escaneado = 1, fecha_escaneado = NOW() WHERE token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    public function habilitarGrupo($alumnos)
    {
        // Alumnos is an array of numCuenta
        if (empty($alumnos))
            return true;

        $placeholders = implode(',', array_fill(0, count($alumnos), '?'));

        // Use a transaction or multiple inserts
        $this->db->beginTransaction();
        try {
            foreach ($alumnos as $numCuenta) {
                // Check if already exists in QR table
                $existing = $this->obtenerPorNumCuenta($numCuenta);
                if (!$existing) {
                    $token = bin2hex(random_bytes(16)); // 32 chars hex
                    $this->crear($numCuenta, $token);
                }
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deshabilitarGrupo($alumnos)
    {
        return true;
    }

    public function marcarEnviado($numCuenta)
    {
        $query = "UPDATE " . $this->table . " SET enviado = 1 WHERE numCuenta = :numCuenta";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numCuenta', $numCuenta);
        return $stmt->execute();
    }

    public function resetearPorEvento($evento)
    {
        $tablaAsientos = "asiento_evento_" . strtolower($evento);
        $query = "UPDATE " . $this->table . " q
                  JOIN {$tablaAsientos} a ON q.numCuenta = a.numCuenta
                  SET q.escaneado = 0, q.fecha_escaneado = NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute();
    }
}
