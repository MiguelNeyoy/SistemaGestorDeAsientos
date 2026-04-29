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

    }//fin-mostrarAsientosEventoLi


    //Consulta todos los asientos y muestra el estado del mismo
    public function mostrarAsientosEventoLiSi(){

        $sql = 'SELECT letra, numero, estado 
                FROM asiento_evento_lisi';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientosEventoLiSi


    //Consulta el asiento al que pertenece un solo alumno del grupo Li
    public function mostrarAsientoAlumnoLi( $numCuenta ){
        
        $sql = 'SELECT alumno.numCuenta, alumno.apellido, asiento_evento_li.letra, asiento_evento_li.numero 
                FROM alumno
                INNER JOIN asiento_evento_li 
                ON alumno.numCuenta = asiento_evento_li.numCuenta
                WHERE asiento_evento_li.numCuenta = ?';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientoAlumnoLi


    //Consulta el asiento al que pertenece un solo alumno del grupo LiSi
    public function mostrarAsientoAlumnoLiSi( $numCuenta ){
        
        $sql = 'SELECT alumno.numCuenta, alumno.apellido, asiento_evento_lisi.letra, asiento_evento_lisi.numero 
                FROM alumno
                INNER JOIN asiento_evento_lisi 
                ON alumno.numCuenta = asiento_evento_lisi.numCuenta
                WHERE asiento_evento_lisi.numCuenta = ?';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-mostrarAsientoAlumnoLiSi


    //Actualiza el estado del asiento cuando el alumno del grupo Li confirma la asistencia
    public function actualizarAsientoEventoLi( $numCuenta ) {

        $sql = 'UPDATE asiento_evento_li
                JOIN asistencia 
                ON asiento_evento_li.numCuenta = asistencia.numCuenta
                SET asiento_evento_li.estado = 1
                WHERE asistencia.numCuenta = ? AND asistencia.estado = 1';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta ] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-actualizarAsientoEventoLi


    //Actualiza el estado del asiento cuando el alumno del grupo LiSi confirma la asistencia
    public function actualizarAsientoEventoLiSi( $numCuenta ) {

        $sql = 'UPDATE asiento_evento_lisi
                JOIN asistencia 
                ON asiento_evento_lisi.numCuenta = asistencia.numCuenta
                SET asiento_evento_lisi.estado = 1
                WHERE asistencia.numCuenta = ? AND asistencia.estado = 1';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$numCuenta ] );

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-actualizarAsientoEventoLiSi


    //Consulta todos los asientos que pertenezcan al mismo turno
    public function grupoAsientosAlumnosEventoLi(){

        $sql = 'SELECT alumno.carrera, alumno.turno, alumno.numCuenta, alumno.apellido, alumno.nombre, asiento_evento_li.letra, asiento_evento_li.numero, asiento_evento_li.estado
                FROM alumno 
                JOIN asiento_evento_li
                ON alumno.numCuenta = asiento_evento_li.numCuenta
                WHERE alumno.turno = ? ';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-grupoAsientosAlumnosEventosLi


    //Consulta todos los asientos que pertenezcan al mismo turno
    public function grupoAsientosAlumnosEventoLiSi(){

        $sql = 'SELECT alumno.carrera, alumno.turno, alumno.numCuenta, alumno.apellido, alumno.nombre, asiento_evento_lisi.letra, asiento_evento_lisi.numero, asiento_evento_lisi.estado
                FROM alumno 
                JOIN asiento_evento_lisi
                ON alumno.numCuenta = asiento_evento_lisi.numCuenta
                WHERE alumno.turno = ? ';

        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-grupoAsientosAlumnosEventosLiSi


    //Muestra el total de asientos que pertenezcan al mismo turno
    public function totalAsientosPorGrupoEventoLi() {

        $sql = 'SELECT COUNT(*) as total_asientos_grupo
                FROM alumno
                JOIN asiento_evento_li
                ON alumno.numCuenta = asiento_evento_li.numCuenta
                WHERE alumno.turno = ?';
                
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-totalAsientosPorGrupoEventoLi


    //Muestra el total de asientos que pertenezcan a la misma carrera y tuno
    public function totalAsientosPorGrupoEventoLiSi() {

        $sql = 'SELECT COUNT(*) as total_asientos_grupo
                FROM alumno
                JOIN asiento_evento_lisi
                ON alumno.numCuenta = asiento_evento_lisi.numCuenta
                WHERE alumno.turno = ?';
                
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }//fin-totalAsientosPorGrupoEventoLisi


}//fin-AsientoModel