<?php

class Conexion {

    private static $conn = null;

    public static function Conectar() {

        if (self::$conn !== null) {
            return self::$conn;
        }

        require_once __DIR__ . '/variables.php';

        $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";


        try {
            self::$conn = new PDO($dsn, $DB_USER, $DB_PASS);

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return self::$conn;

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function cerrar() {
        self::$conn = null;
    }
}
