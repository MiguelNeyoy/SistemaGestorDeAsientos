<?php

class Conexion
{
    private static $instance = null;
    private $conexion;

    private $dbhost;
    private $dbport;
    private $database;
    private $dbusername;
    private $dbpassword;

    private function __construct()
    {
        require('./API/config/variables.php');

        $this->dbhost = $DB_HOST;
        $this->dbport = $DB_PORT;
        $this->database = $DB_DATABASE;
        $this->dbusername = $DB_USERNAME;
        $this->dbpassword = $DB_PASSWORD;

        try {
            $dsn = "mysql:host={$this->dbhost};port={$this->dbport};dbname={$this->database};charset=utf8mb4";
            $this->conexion = new PDO(
                $dsn,
                $this->dbusername,
                $this->dbpassword,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obtener la conexión PDO
    public function getConexion()
    {
        return $this->conexion;
    }

    // Ejecutar consulta preparada
    public function ejecutar($sql, $parametros = [])
    {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($parametros);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }


    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup() {}
}
