<?php 
/*if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}*/


//Reglas para la asignacion de horarios a cada docente
//1. Los docentes solo podran tener asignados un grado y una materia (lenguaje, matematica, sociales, ciencias, ingles, valores)
//pero solo podran dar esa materia en a los grados de ese ciclo escolar, es decir, cada 3 grados 1o,2o,3o es primer ciclo
//2. La unica excepcion es el profesor de educacion fisica, el debe atender a toda la escuela, los 18 grados, 2 horas clases a la semana
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/horarios.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <a href="../dashboard/dashboardProfesor.php" class="btn btn-outline-light btn-sm">Volver</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4 text-center">
        <div class="col">
            <h2 class="fw-bold">Horario de Clases</h2>
            <p class="text-muted">Consulta tu horario semanal</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Hora</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>7:10 - 7:50</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>7:50 - 8:30</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="table-secondary">
                    <td>8:30 - 8:50</td>
                    <td colspan="5">Receso</td>
                </tr>
                <tr>
                    <td>8:50 - 9:30</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>9:30 - 10:10</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="table-secondary">
                    <td>10:10 - 10:30</td>
                    <td colspan="5">Receso</td>
                </tr>
                <tr>
                    <td>10:30 - 11:10</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>11:10 - 11:50</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

</body>
</html>

