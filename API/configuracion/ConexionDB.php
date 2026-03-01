<?php


class conexion {
    
    public static $conn = null;

    public static function conectar(){
        if(self::$conn !== null){
            return self::$conn;
        }

        require_once(__DIR__ . './API/configuracion/variables.php');

        if (!isset($DB_HOST) || !isset($DB_USER) ){
            die("Error: Archivo de configuración incompleto.");
        }

        $dsn = "mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME . ";charset=utf8mb4";

        try {
            self::$conn = new PDO($dsn, $DB_USER, $DB_PASS);
            
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return self::$conn;

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            die();
        }
        // Método estático para cerrar
        }
        public static function cerrar() {
            self::$conn = null;
        }

}
