<?php

$host = "127.0.0.1";    
$user = "root";         
$pass = "";             
$db   = "graduacion";   


$conn = mysqli_connect($host, $user, $pass, $db);


if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}


mysqli_set_charset($conn, "utf8");

?>
