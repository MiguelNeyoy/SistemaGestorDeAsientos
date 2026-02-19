<?php

require_once(__DIR__ . './API/servicios/ServicioAlumno.php');

class AlumnoController{
    private $servicioAlumno;

   public function obtenerTodosAlumno(){
    //Llamamos el servicio de alumnos (logica)   
   $this -> servicioAlumno = new AlumnoServicio;

    $lista = $$this -> servicioAlumno->obtenerListaDeGraduados();

    //Se prepara la respuesta para el frontEnd 

    header('Content-Type: application/json');
    echo json_encode($lista);

   }

   //Funcion regresa a un solo alumno 
   public function obtenerAlumno($numero_cuenta){
            $Alumno = $this -> servicioAlumno -> obtenerAlumno($numero_cuenta);

        header('Content-Type: application/json');
        echo json_encode($Alumno);
            
    }

    //Listado de alumnos por carrera
    public function obtenerAlumnoPorCarrera($carrera){

    }

    public function agregarInvitadosAlumno(){
        
    }

}