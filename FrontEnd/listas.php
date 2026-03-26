<?php
include 'config/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alumnos Pendientes</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<header>
    <h1>Lista de Alumnos Confirmados</h1>
    <nav>
        <a href="admin.php">← Volver al Panel</a>
    </nav>
</header>

<div class="container">

    <h2>Alumnos clasificados por grupo</h2>

    <?php
    
    $sqlGrupos = "SELECT * FROM grupos ORDER BY nombre_grupo ASC";
    $resGrupos = mysqli_query($conn, $sqlGrupos);

    while ($grupo = mysqli_fetch_assoc($resGrupos)):
        $idGrupo = $grupo['id_grupo'];
        $nombreGrupo = $grupo['nombre_grupo'];

        
        $sqlPendientes = "
            SELECT p.id_pendiente, p.nombre, p.correo
            FROM pendientes p
            WHERE p.id_grupo = $idGrupo
            ORDER BY 
                SUBSTRING_INDEX(p.nombre, ' ', -1) ASC,  -- último apellido
                p.nombre ASC
        ";
        $resPendientes = mysqli_query($conn, $sqlPendientes);
        ?>

        <h3 style="margin-top:30px; color:#0B3C5D;"><?php echo htmlspecialchars($nombreGrupo); ?></h3>

        <?php if(mysqli_num_rows($resPendientes) > 0): ?>
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $contador = 1; ?>
                    <?php while($alum = mysqli_fetch_assoc($resPendientes)): ?>
                        <tr>
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo htmlspecialchars($alum['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($alum['correo'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay alumnos pendientes en este grupo.</p>
        <?php endif; ?>

    <?php endwhile; ?>

</div>

</body>
</html>