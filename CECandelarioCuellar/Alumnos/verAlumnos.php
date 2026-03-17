<?php
session_start();
require_once '../config/conexion.php';

// Seguridad: Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$estudiantes = [];
$grados = [];
$mensaje_error = "";

try {
    // Obtener lista de estudiantes (Ordenados por apellido)
    $stmt = $conexion->query("SELECT * FROM estudiante WHERE activo = 1 ORDER BY apellido, nombre ASC");
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lista de grados para el select
    $stmt_grados = $conexion->query("SELECT id_grado FROM grado ORDER BY id_grado ASC");
    $grados = $stmt_grados->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    $mensaje_error = "Error al cargar datos: " . $e->getMessage();
}
?>
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
        <div class="d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-block small">
                <?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?>
            </span>
            <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-3 mt-4">
        <div class="col">
            <h2 class="fw-bold" style="color: var(--color-azul-oscuro, #0d6efd);">Nómina de Estudiantes Matriculados</h2>
            <p style="color: var(--color-azul-claro, #6c757d);">Gestión y consulta de expedientes escolares.</p>
        </div>
    </div>

    <?php if ($mensaje_error): ?>
        <div class="alert alert-danger"><?= $mensaje_error ?></div>
    <?php endif; ?>

    <div class="row filtro-barra shadow-sm align-items-center bg-white p-3 rounded mb-4">
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar por Nombre..." onkeyup="filtrarTabla()">
        </div>
        <div class="col-md-4 mb-2 mb-md-0">
            <select class="form-select" id="filtroGrado" onchange="filtrarTabla()">
                <option value="">Todos los grados...</option>
                <?php foreach($grados as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-candelario w-100" style="background-color: var(--color-azul-oscuro, #0d6efd); color: white;" onclick="filtrarTabla()">Aplicar Filtros</button>
        </div>
    </div>

    <div class="tabla-nomina shadow-sm rounded bg-white">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaAlumnos">
                <thead class="thead-dark" style="background-color: var(--color-azul-oscuro, #212529); color: white;">
                    <tr>
                        <th class="ps-4">NIE / ID</th>
                        <th>Nombre del Estudiante</th>
                        <th>Fecha Nacimiento</th>
                        <th>DUI Padre/Encargado</th>
                        <th>Teléfono</th>
                        <th class="text-center">Expediente</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($estudiantes) > 0): ?>
                        <?php foreach ($estudiantes as $estu): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= htmlspecialchars($estu['id_estudiante']) ?></td>
                                <td>
                                    <?= htmlspecialchars($estu['nombre'] . ' ' . $estu['apellido']) ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($estu['fecha_nacimiento'])) ?></td>
                                <td><?= htmlspecialchars($estu['dui_padre_madre']) ?></td>
                                <td><?= htmlspecialchars($estu['telefono_padre_madre']) ?></td>
                                <td class="text-center">
                                    <?php if (!empty($estu['ruta_partida_nacimiento']) && file_exists('../' . $estu['ruta_partida_nacimiento'])): ?>
                                        <a href="../<?= htmlspecialchars($estu['ruta_partida_nacimiento']) ?>" target="_blank" class="text-decoration-none fw-bold" style="color: var(--color-azul-claro, #0d6efd);">
                                            📂 Ver Partida
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <!-- Botón Editar (Pendiente de crear el archivo) -->
                                        <button class="btn btn-outline-secondary btn-accion" title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <!-- Botón Baja (Lógica pendiente) -->
                                        <button class="btn btn-outline-danger btn-accion" title="Dar de baja">
                                            <i class="bi bi-trash"></i> Baja
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No hay estudiantes registrados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3 text-end">
        <p class="small text-muted">Mostrando <strong><?= count($estudiantes) ?></strong> registros encontrados.</p>
    </div>
</div>

<footer class="text-center mt-5 p-4 bg-light">
    <small style="color: var(--color-azul-claro, #6c757d);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<!-- Script para filtrado en el navegador (Frontend) -->
<script>
    function filtrarTabla() {
        let input = document.getElementById("buscador").value.toLowerCase();
        let select = document.getElementById("filtroGrado").value;
        let table = document.getElementById("tablaAlumnos");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let tdNombre = tr[i].getElementsByTagName("td")[1]; // Columna Nombre
            let tdGrado = tr[i].getElementsByTagName("td")[2]; // Columna Grado
            
            if (tdNombre) {
                let txtValue = tdNombre.textContent || tdName.innerText;
                let matchNombre = txtValue.toLowerCase().indexOf(input) > -1;
 
                if (matchNombre) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>