<?php



//AsientoModel
class AsientoModel{


    private $db;



    public function __construct(){

        require_once(__DIR__ . '/../configuracion/ConexionDB.php');

        $this-> db = Conexion::Conectar();

    }//fin-__construct


    //Consulta todos los asientos y muestra el estado del mismo
    public function mostrarAsientosEventoLi(){

        $sql = 'SELECT letra, numero, estado 
                FROM asiento_evento_li';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientos

    //Consulta todos los asientos y muestra el estado del mismo
    public function mostrarAsientosEventoLiSi(){

        $sql = 'SELECT letra, numero, estado 
                FROM asiento_evento_li';

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


    //Actualiza el estado del asiento cuando el alumno confirma la asistencia
    public function actualizarAsiento( $numCuenta ) {

        $sql = 'UPDATE asiento
                JOIN asistencia 
                ON asiento.numCuenta = asistencia.numCuenta
                SET asiento.estado = 1
                WHERE asistencia.numCuenta = ? AND asistencia.estado = 1';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta ] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-actualizarAsiento


    //Consulta todos los asientos que pertenezcan a la misma carrera y turno
    public function grupoAsientosAlumnos(){

        $sql = 'SELECT alumno.carrera, alumno.turno, alumno.numCuenta, alumno.apellido, alumno.nombre, asiento.letra, asiento.numero, asiento.estado
                FROM alumno 
                JOIN asiento
                ON alumno.numCuenta = asiento.numCuenta
                WHERE alumno.carrera = ? AND alumno.turno = ? ';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-grupoAsientosAlumnos


    //Muestra el total de asientos que pertenezcan a la misma carrera y tuno
    public function totalAsientosPorGrupo() {

        $sql = 'SELECT COUNT(*) as total_asientos_grupo
                FROM alumno
                JOIN asiento
                ON alumno.numCuenta = asiento.numCuenta
                WHERE alumno.carrera = ? AND alumno.turno = ?';
                
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-totalAsientosPorGrupo


}//fin-AsientoModel