<?php 
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Docente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-block small">
                <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Docente') ?>
            </span>
            <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container dashboard-container">
    <div class="row mb-5 text-center">
        <div class="col">
            <h2 class="fw-bold">Panel Docente</h2>
            <p class="text-muted">Gestión académica</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📅</div>
                <h4>Horario de Clases</h4>
                <p class="text-secondary small">Consulta tu horario personal de clases.</p>
                <a href="../horarios/verHorarios.php" class="btn btn-principal w-100 mt-3">Ver Horario</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📋</div>
                <h4>Lista de Alumnos</h4>
                <p class="text-secondary small">Consulta el listado de tus alumnos por ciclo.</p>
                <a href="../Alumnos/verAlumnos.php" class="btn btn-principal w-100 mt-3">Ver Listado</a>
            </div>
        </div>

        <!-- NUEVO: Notas -->
        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📊</div>
                <h4>Notas</h4>
                <p class="text-secondary small">Registra y consulta las notas de tus alumnos por periodo.</p>
                <a href="../Alumnos/notasAlumnos.php" class="btn btn-principal w-100 mt-3">Gestionar Notas</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📝</div>
                <h4>Matrícula</h4>
                <p class="text-secondary small">Gestiona la matrícula de tus alumnos.</p>
                <a href="../Alumnos/inscribirAlumno.php" class="btn btn-principal w-100 mt-3">Gestionar</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

</body>
</html>