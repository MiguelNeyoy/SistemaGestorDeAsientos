<?php

/**
 *  
 * En la carpeta de servicio es donde va la logia de la toda la aplicacion
 * Reglas de la app si no pueden pasar mas de 2 invitados 
 * creacion de qr. ect.
*/
    require_once(__DIR__ . './API/modelos/AlumnoModelo.php');
    class AlumnoServicio{
        private $alumnoModelo;

        public function __construct() {
            //al crear el objeto AlumnoServicio se crea el objeto AlumnoModel
            $this -> alumnoModelo = new AlumnoModel;
        }

        public function obtenerListaDeGraduados(){
            $todosAlumnos = $this->alumnoModelo->obtenerAlumnos();
            //limpiar la lista para evitar mandar datos sensibles
            $listaLimpia = [];

            foreach ($listaLimpia as $alum) {
            $listaLimpia[] = [
                'cuenta' => $alum['numero_cuenta'],
                'nombre_completo' => $alum['nombre'] . ' ' . $alum['apellidos'],
                'carrera' => $alum['carrera'],
                'estado_texto' => ($alum['estado'] == 1) ? 'Confirmado' : 'Pendiente'
            ];
        }
        return $listaLimpia;

        
        }
        public function obtenerAlumno($numero_cuenta){
            $alumno = $this -> alumnoModelo -> obtenerAlumnosPorNumeroDeCuenta($numero_cuenta);
            return $alumno;
        }
    }

?>