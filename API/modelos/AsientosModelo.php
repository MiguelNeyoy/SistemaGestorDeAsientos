<?php



//
class AsientoModel{


    private $db;



    public function __construct(){

        require_once(__DIR__ . '/../configuracion/ConexionDB.php');

        $this-> db = Conexion::Conectar();

    }//fin-__construct


    //Consulta todos los asientos y muestra el estado del mismo
    public function mostrarAsientos(){

        $sql = 'SELECT letra, numero, estado 
                FROM asiento';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientos


    //Consulta el asiento al que pertenece un solo alumno
    public function mostrarAsientoAlumno( $numCuenta ){
        
        $sql = 'SELECT alumno.numCuenta, alumno.apellido, asiento.letra, asiento.numero 
                FROM alumno
                INNER JOIN asiento 
                ON alumno.numCuenta = asiento.numCuenta
                WHERE asiento.numCuenta = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientoAlumno


}//fin-AsientoModel