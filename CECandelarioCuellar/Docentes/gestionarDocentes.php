<?php
require_once '../config/conexion.php';

// Definir los ciclos y sus grados
$ciclos = [
    'Primer Ciclo'   => ['1° "A"', '1° "B"', '2° "A"', '2° "B"', '3° "A"', '3° "B"'],
    'Segundo Ciclo'  => ['4° "A"', '4° "B"', '5° "A"', '5° "B"', '6° "A"', '6° "B"'],
    'Tercer Ciclo'   => ['7° "A"', '7° "B"', '8° "A"', '8° "B"', '9° "A"', '9° "B"'],
];

// Función auxiliar: dado un id_grado, retorna a qué ciclo pertenece
function getCicloDeGrado($grado, $ciclos) {
    foreach ($ciclos as $nombre_ciclo => $grados) {
        if (in_array($grado, $grados)) {
            return $nombre_ciclo;
        }
    }
    return null;
}

// --- PROCESAMIENTO DEL FORMULARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_docente  = $_POST['id_docente'];
    $ciclo_sel   = $_POST['ciclo'];       // ej: "Primer Ciclo"
    $id_materia  = $_POST['materia'];
    $activo      = isset($_POST['activo']) ? 1 : 0;

    // Validar que el ciclo seleccionado existe
    if (!isset($ciclos[$ciclo_sel])) {
        die("Ciclo inválido.");
    }

    $grados_del_ciclo = $ciclos[$ciclo_sel];

    // Eliminar todas las asignaciones anteriores del docente en este año
    $conexion->prepare("
        DELETE FROM asignacion_docente 
        WHERE id_usuario = ? AND año_lectivo = YEAR(CURDATE())
    ")->execute([$id_docente]);

    // Insertar una asignación por cada grado del ciclo
    $stmtInsert = $conexion->prepare("
        INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
        VALUES (?, ?, ?, YEAR(CURDATE()))
    ");
    foreach ($grados_del_ciclo as $grado) {
        $stmtInsert->execute([$id_docente, $grado, $id_materia]);
    }

    // Actualizar estado activo del docente
    $conexion->prepare("UPDATE usuario SET activo = ? WHERE id_usuario = ?")
             ->execute([$activo, $id_docente]);
}

// --- CARGAR DOCENTES ---
// Para cada docente, detectamos su ciclo actual mirando cualquiera de sus grados asignados
$docentes = $conexion->query("
    SELECT u.id_usuario, u.nombre, u.apellido, u.activo,
           MIN(a.id_grado)      AS un_grado,
           a.id_materia,
           m.nombre_materia,
           COUNT(a.id_grado)    AS total_grados
    FROM usuario u
    LEFT JOIN asignacion_docente a 
           ON u.id_usuario = a.id_usuario AND a.año_lectivo = YEAR(CURDATE())
    LEFT JOIN materia m ON a.id_materia = m.id_materia
    WHERE u.rol = 'docente'
    GROUP BY u.id_usuario, u.nombre, u.apellido, u.activo, a.id_materia, m.nombre_materia
    ORDER BY u.apellido, u.nombre
")->fetchAll();

// --- CARGAR MATERIAS ---
$materias = $conexion->query("
    SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <a href="../dashboard/dashboardAdmin.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4 text-center">
        <div class="col">
            <h2 class="fw-bold">Gestión de Docentes</h2>
            <p class="text-muted">Asigna ciclo, materia y estado a los docentes</p>
        </div>
    </div>

    <div class="tabla-nomina shadow-sm rounded bg-white">
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle mb-0">
                <thead style="background-color: var(--color-azul-oscuro); color: white;">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Ciclo Asignado</th>
                        <th>Grados</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($docentes as $docente): 
                        // Detectar ciclo actual del docente
                        $ciclo_actual = $docente['un_grado'] 
                            ? getCicloDeGrado($docente['un_grado'], $ciclos) 
                            : null;
                        
                        // Descripción de grados para mostrar en tabla
                        $grados_desc = $ciclo_actual 
                            ? implode(', ', $ciclos[$ciclo_actual]) 
                            : null;
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-start">
                            <?= htmlspecialchars($docente['nombre'] . ' ' . $docente['apellido']) ?>
                        </td>
                        <td>
                            <?php if ($ciclo_actual): ?>
                                <span class="badge" style="background-color: var(--color-azul-claro); font-size:0.85rem;">
                                    <?= $ciclo_actual ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($grados_desc): ?>
                                <small class="text-muted"><?= htmlspecialchars($grados_desc) ?></small>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $docente['nombre_materia'] 
                                ? htmlspecialchars($docente['nombre_materia']) 
                                : '<span class="text-muted small">Sin asignar</span>' ?>
                        </td>
                        <td>
                            <?php if ($docente['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDocente<?= $docente['id_usuario'] ?>">
                                <i class="bi bi-pencil-fill"></i> Editar
                            </button>
                        </td>
                    </tr>

                    <!-- ===== MODAL ===== -->
                    <div class="modal fade" id="modalDocente<?= $docente['id_usuario'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: var(--color-azul-oscuro);">
                                    <h5 class="modal-title text-white">
                                        <i class="bi bi-person-fill me-2"></i>
                                        <?= htmlspecialchars($docente['nombre'] . ' ' . $docente['apellido']) ?>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_docente" value="<?= $docente['id_usuario'] ?>">

                                        <!-- COMBO BOX CICLO -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Ciclo a asignar</label>
                                            <select class="form-select" name="ciclo" id="selectCiclo<?= $docente['id_usuario'] ?>" 
                                                    onchange="mostrarGrados(<?= $docente['id_usuario'] ?>)" required>
                                                <option value="">-- Seleccione un ciclo --</option>
                                                <?php foreach ($ciclos as $nombre_ciclo => $grados_ciclo): ?>
                                                    <option value="<?= htmlspecialchars($nombre_ciclo) ?>"
                                                        <?= ($ciclo_actual === $nombre_ciclo) ? 'selected' : '' ?>>
                                                        <?= $nombre_ciclo ?>
                                                        (<?= implode(', ', array_filter($grados_ciclo, fn($g) => str_contains($g, '"A"'))) ?>...)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- PREVIEW DE GRADOS QUE SE ASIGNARÁN -->
                                        <div class="mb-3" id="previewGrados<?= $docente['id_usuario'] ?>">
                                            <?php if ($ciclo_actual): ?>
                                                <div class="alert alert-info py-2 mb-0">
                                                    <small><i class="bi bi-info-circle me-1"></i>
                                                    <strong>Grados incluidos:</strong> 
                                                    <?= htmlspecialchars($grados_desc) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- COMBO BOX MATERIA -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Materia</label>
                                            <select class="form-select" name="materia" required>
                                                <option value="">-- Seleccione una materia --</option>
                                                <?php foreach ($materias as $m): ?>
                                                    <option value="<?= htmlspecialchars($m['id_materia']) ?>"
                                                        <?= ($docente['id_materia'] === $m['id_materia']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($m['nombre_materia']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- CHECKBOX ACTIVO -->
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   name="activo"
                                                   id="activo<?= $docente['id_usuario'] ?>"
                                                   <?= $docente['activo'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="activo<?= $docente['id_usuario'] ?>">
                                                Docente Activo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle-fill me-1"></i> Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- ===== FIN MODAL ===== -->

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-end">
        <p class="small text-muted">Total docentes: <strong><?= count($docentes) ?></strong></p>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<!-- Datos de ciclos para el JS -->
<script>
const ciclosData = <?= json_encode($ciclos) ?>;

function mostrarGrados(idDocente) {
    const select  = document.getElementById('selectCiclo' + idDocente);
    const preview = document.getElementById('previewGrados' + idDocente);
    const ciclo   = select.value;

    if (ciclo && ciclosData[ciclo]) {
        const grados = ciclosData[ciclo].join(', ');
        preview.innerHTML = `
            <div class="alert alert-info py-2 mb-0">
                <small><i class="bi bi-info-circle me-1"></i>
                <strong>Grados incluidos:</strong> ${grados}
                </small>
            </div>`;
    } else {
        preview.innerHTML = '';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>