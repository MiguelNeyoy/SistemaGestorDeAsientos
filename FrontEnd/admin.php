<?php 

include 'config/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Panel de Administración - Graduación</h1>
    <nav>
        <a href="asientos.html">Ver Mapa de Teatro</a>
    </nav>
</header>

<div class="container">
    <h2 style="text-align:center; margin-bottom:30px;">Opciones del Administrador</h2>

    <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
        <a href="pendientes.php" class="card-grupo">
            Asignar Asientos
        </a>
        <a href="listas.php" class="card-grupo">
            Ver Listas de Alumnos
        </a>
    </div>
</div>

</body>
</html>