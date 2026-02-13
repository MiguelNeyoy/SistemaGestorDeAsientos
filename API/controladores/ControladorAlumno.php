<?php

require_once(__DIR__ . './API/servicios/ServicioAlumno.php');

class AlumnoController{

   public function obtenerTodosAlumno(){
    //Llamamos el servicio de alumnos (logica)   
   $servicioAlumno = new AlumnoServicio;

    $lista = $servicioAlumno->obtenerListaDeGraduados();

    //Se prepara la respuesta para el frontEnd 

    header('Content-Type: application/json');
    echo json_encode($lista);

   }

    public function crearAlumno(){
        $datos = json_decode(file_get_contents('php://input'),true);

        //se manda a llamar a la funcion de guardar 
        //se responde si fue exitoso o no
    }

}