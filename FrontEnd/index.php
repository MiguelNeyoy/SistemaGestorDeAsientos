<?php 
include 'config/conexion.php'; 

$grupo = isset($_GET['grupo']) ? $_GET['grupo'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Teatros de Graduación</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<header>
    <h1>Distribución de Asientos - Graduación</h1>
    <p>Universidad Autónoma de Sinaloa</p>
</header>

<div class="container">

<?php if(!$grupo): ?>

    <h2 style="text-align:center; margin-bottom:30px;">Selecciona un Grupo</h2>

    <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
        <?php
        $consultaGrupos = "
            SELECT g.nombre_grupo
            FROM grupos g
            INNER JOIN teatros t ON g.id_grupo = t.id_grupo
        ";

        $resultadoGrupos = mysqli_query($conn, $consultaGrupos);

        while($rowGrupo = mysqli_fetch_assoc($resultadoGrupos)) {
            $nombreGrupo = urlencode($rowGrupo['nombre_grupo']);
            echo "
                <a href='?grupo=$nombreGrupo' 
                   style='
                        padding:20px 30px;
                        background:#0B3C5D;
                        color:white;
                        text-decoration:none;
                        border-radius:10px;
                        border:2px solid #D4AF37;
                        font-weight:bold;
                        transition:0.3s;
                   '
                   onmouseover=\"this.style.background='#1E5F8A'\"
                   onmouseout=\"this.style.background='#0B3C5D'\"
                >
                    {$rowGrupo['nombre_grupo']}
                </a>
            ";
        }
        ?>
    </div>

<?php else: ?>

    <div style="text-align:center; margin-bottom:20px;">
        <a href="index.php" style="color:#D4AF37; text-decoration:none;">← Volver a grupos</a>
    </div>

    <h2 style="text-align:center; margin-bottom:20px;">
        Lugares para el grupo: <?php echo htmlspecialchars($grupo); ?>
    </h2>

    <div class="mesa">Mesa Directiva</div>

    <div class="teatro">
        <?php
        $grupoSeguro = mysqli_real_escape_string($conn, $grupo);

        $sql = "
            SELECT s.id_asiento, s.numero_asiento,
                   a.nombre
            FROM asientos s
            INNER JOIN teatros t ON s.id_teatro = t.id_teatro
            INNER JOIN grupos g ON t.id_grupo = g.id_grupo
            LEFT JOIN alumnos a 
                ON s.id_asiento = a.id_asiento
            WHERE g.nombre_grupo = '$grupoSeguro'
            ORDER BY s.numero_asiento ASC
        ";

        $resultado = mysqli_query($conn, $sql);

        $asientos = [];
        while($row = mysqli_fetch_assoc($resultado)) {
            $asientos[$row['numero_asiento']] = $row['nombre'];
        }

        $filas = range('A','J'); // Filas A-J

        foreach($filas as $fila) {
            echo "<div class='fila'>";

            $secciones = [
                [1,7],
                [8,23],
                [24,30]
            ];

            foreach($secciones as $sec) {
                echo "<div class='seccion'>";
                for($i=$sec[0]; $i<=$sec[1]; $i++) {
                    $numero = $fila.$i;
                    $nombreAlumno = isset($asientos[$i]) && $asientos[$i] 
                        ? htmlspecialchars($asientos[$i], ENT_QUOTES, 'UTF-8') 
                        : 'Disponible';

                    // Fila A siempre no disponible
                    if($fila == 'A') {
                        $estadoClass = 'no-disponible';
                    } else {
                        $estadoClass = (isset($asientos[$i]) && $asientos[$i]) ? 'ocupado' : 'libre';
                    }

                    echo "
                        <div class='asiento $estadoClass'
                            data-nombre=\"{$nombreAlumno}\"
                            data-numero=\"{$numero}\">
                            {$numero}
                        </div>
                    ";
                }
                echo "</div>";
            }

            echo "</div>"; // cierre fila
        }
        ?>
    </div>

    <div id="info-asiento">
        <p>Selecciona un asiento para ver información.</p>
    </div>

<?php endif; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const asientos = document.querySelectorAll(".asiento");
    const info = document.getElementById("info-asiento");

    asientos.forEach(asiento => {
        asiento.addEventListener("click", function() {
            const nombre = this.dataset.nombre;
            const numero = this.dataset.numero;

            if (this.classList.contains("no-disponible")) {
                info.innerHTML = `
                    <h3>Asiento ${numero}</h3>
                    <p style="color:red;">Este asiento no está disponible.</p>
                `;
            } else if (this.classList.contains("ocupado")) {
                info.innerHTML = `
                    <h3>Asiento ${numero}</h3>
                    <p><strong>Alumno:</strong> ${nombre}</p>
                    <p style="color:green;">Este asiento está confirmado.</p>
                `;
            } else {
                info.innerHTML = `
                    <h3>Asiento ${numero}</h3>
                    <p style="color:#0B3C5D;">
                        Este lugar está disponible actualmente.
                    </p>
                `;
            }
        });
    });
});
</script>

</body>
</html>
