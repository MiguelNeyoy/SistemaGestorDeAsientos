<?php

require_once(__DIR__ . './API/servicios/ServicioAlumno.php');

class AlumnoController{
    private $servicioAlumno;

   public function validarAlumno() {
    /* recibe el numero de cuenta y llama al servicio que busca al alumno por numero de cuenta 
        responde 200 ok si todo bien sino 404 no se encontro
    */ 
   }
   public function confirmarAsistencia($numero_de_cuenta){
    /** 
     *  Recibe el numero de cuenta llama al servicio registrar asistencia
     *  Ese servicio valida que sean los invitados correspondietes
     *  responde 200 ok o 400 
    */
   }
   public function obtenerEstado($numero_de_cuenta){
    /**
     *  recibe el numero de cuenta, manda a llamer el detalle alumno
     *  y responde informacion del alumno
     * 
     */
   }

}