<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
    </div>
</nav>

<div class="container dashboard-container">
    <div class="row mb-5 text-center">
        <div class="col">
            <h2 class="fw-bold">Panel Administrativo</h2>
            <p class="text-muted">Gestión académica y administrativa</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📅</div>
                <h4>Horarios</h4>
                <p class="text-secondary small">Gestion de horarios de clases.</p>
                <a href="#" class="btn btn-principal w-100 mt-3">Gestionar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">✍️</div>
                <h4>Matricular Alumnos</h4>
                <p class="text-secondary small">Proceso de inscripción.</p>
                <a href="../Alumnos/inscribirAlumno.php" class="btn btn-principal w-100 mt-3">Matricular</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-menu shadow-sm p-4 text-center">
                <div class="icon-circle">📊</div>
                <h4>Ver Listados</h4>
                <p class="text-secondary small">Visualización de asistencia y notas.</p>
                <a href="../Alumnos/verAlumnos.php" class="btn btn-principal w-100 mt-3">Ver Reportes</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

</body>
</html>