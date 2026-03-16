<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Alumnos - C.E. Candelario Cuellar</title>
    
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
        <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold" style="color: var(--color-azul-oscuro);">Nómina de Estudiantes Matriculados</h2>
            <p style="color: var(--color-azul-claro);">Gestión y consulta de expedientes escolares.</p>
        </div>
    </div>

    <div class="row filtro-barra shadow-sm align-items-center">
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" class="form-control" placeholder="Buscar por Nombre o NIE...">
        </div>
        <div class="col-md-4 mb-2 mb-md-0">
            <select class="form-select">
                <option selected>Todos los grados...</option>
                <?php 
                    $grados = ["1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°", "9°"];
                    foreach($grados as $g) {
                        echo "<option value='$g'>$g Grado</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-candelario w-100">Aplicar Filtros</button>
        </div>
    </div>

    <div class="tabla-nomina">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th class="ps-4">NIE</th>
                        <th>Nombre del Estudiante</th>
                        <th>Grado / Sección</th>
                        <th>DUI Padre</th>
                        <th>Teléfono</th>
                        <th class="text-center">Expediente</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                    <!--Datos momentaneos-->
                <tbody>
                    <tr>
                        <td class="ps-4 fw-bold">4587122</td>
                        <td>Carlos Manuel Ramos Zepeda</td>
                        <td><span class="badge badge-grado">9° Grado - A</span></td>
                        <td>04851223-5</td>
                        <td>7122-4455</td>
                        <td class="text-center">
                            <a href="#" class="text-decoration-none" style="color: var(--color-azul-claro);">
                                📂 Ver Partida
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-accion">Editar</button>
                                <button class="btn btn-outline-danger btn-accion">Baja</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="ps-4 fw-bold">8854122</td>
                        <td>Ana Lucía Méndez Portillo</td>
                        <td><span class="badge badge-grado">1° Grado - B</span></td>
                        <td>01223344-9</td>
                        <td>7008-1122</td>
                        <td class="text-center">
                            <a href="#" class="text-decoration-none" style="color: var(--color-azul-claro);">
                                📂 Ver Partida
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-accion">Editar</button>
                                <button class="btn btn-outline-danger btn-accion">Baja</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3 text-end">
        <p class="small text-muted">Mostrando 2 registros encontrados en la base de datos.</p>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>