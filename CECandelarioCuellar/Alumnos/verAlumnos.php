<?php
session_start();
require_once '../config/conexion.php';

/*if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}*/

$estudiantes = [];
$grados = [];
$mensaje_error = "";
$mensaje_ok = "";
$id_usuario_logueado = $_SESSION['id_usuario'] ?? 0;
$rol_usuario = $_SESSION['rol'] ?? 'docente';
$ciclo_docente = null;
$materia_docente = null;

$ciclos = [
    'Primer Ciclo'  => ['1° "A"', '1° "B"', '2° "A"', '2° "B"', '3° "A"', '3° "B"'],
    'Segundo Ciclo' => ['4° "A"', '4° "B"', '5° "A"', '5° "B"', '6° "A"', '6° "B"'],
    'Tercer Ciclo'  => ['7° "A"', '7° "B"', '8° "A"', '8° "B"', '9° "A"', '9° "B"'],
];

function getCiclo($grado, $ciclos) {
    foreach ($ciclos as $nombre => $grados) {
        if (in_array($grado, $grados)) return $nombre;
    }
    return null;
}

// --- ACCIÓN: EDITAR ESTUDIANTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    try {
        $id         = $_POST['id_estudiante'];
        $nombre     = htmlspecialchars(trim($_POST['nombre']));
        $apellido   = htmlspecialchars(trim($_POST['apellido']));
        $fecha      = $_POST['fecha_nacimiento'];
        $nombre_padre = htmlspecialchars(trim($_POST['nombre_padre_madre']));
        $telefono   = trim($_POST['telefono_padre_madre']);
        $dui        = trim($_POST['dui_padre_madre']);
        $id_grado   = $_POST['id_grado'];

        // Actualizar datos del estudiante
        $conexion->prepare("
            UPDATE estudiante
            SET nombre = ?, apellido = ?, fecha_nacimiento = ?,
                nombre_padre_madre = ?, telefono_padre_madre = ?, dui_padre_madre = ?
            WHERE id_estudiante = ?
        ")->execute([$nombre, $apellido, $fecha, $nombre_padre, $telefono, $dui, $id]);

        // Actualizar grado en matrícula
        $conexion->prepare("
            UPDATE matricula SET id_grado = ?
            WHERE id_estudiante = ? AND año_lectivo = YEAR(CURDATE())
        ")->execute([$id_grado, $id]);

        $mensaje_ok = "✅ Estudiante actualizado correctamente.";

    } catch (PDOException $e) {
        $mensaje_error = "❌ Error al actualizar: " . $e->getMessage();
    }
}

// --- ACCIÓN: DAR DE BAJA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'baja') {
    try {
        $id = $_POST['id_estudiante'];

        // Baja lógica: desactivar estudiante
        $conexion->prepare("UPDATE estudiante SET activo = 0 WHERE id_estudiante = ?")
                 ->execute([$id]);

        // Cambiar estado matrícula a retirada
        $conexion->prepare("
            UPDATE matricula SET estado = 'retirada'
            WHERE id_estudiante = ? AND año_lectivo = YEAR(CURDATE())
        ")->execute([$id]);

        $mensaje_ok = "✅ Estudiante dado de baja correctamente.";

    } catch (PDOException $e) {
        $mensaje_error = "❌ Error al dar de baja: " . $e->getMessage();
    }
}

// --- CARGAR DATOS ---
try {
    if ($rol_usuario === 'administrador') {
        $stmt = $conexion->prepare("
            SELECT e.*, m.id_grado, m.estado AS estado_matricula
            FROM estudiante e
            INNER JOIN matricula m ON e.id_estudiante = m.id_estudiante
            WHERE e.activo = 1 AND m.año_lectivo = YEAR(CURDATE())
            ORDER BY m.id_grado, e.apellido, e.nombre ASC
        ");
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grados = $conexion->query("SELECT id_grado FROM grado ORDER BY id_grado ASC")
                           ->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $stmtAsig = $conexion->prepare("
            SELECT a.id_grado, m.nombre_materia
            FROM asignacion_docente a
            INNER JOIN materia m ON a.id_materia = m.id_materia
            WHERE a.id_usuario = ? AND a.año_lectivo = YEAR(CURDATE())
            LIMIT 1
        ");
        $stmtAsig->execute([$id_usuario_logueado]);
        $asignacion = $stmtAsig->fetch(PDO::FETCH_ASSOC);

        if ($asignacion) {
            $materia_docente = $asignacion['nombre_materia'];
            $ciclo_docente   = getCiclo($asignacion['id_grado'], $ciclos);

            if ($ciclo_docente) {
                $grados_del_ciclo = $ciclos[$ciclo_docente];
                $placeholders     = implode(',', array_fill(0, count($grados_del_ciclo), '?'));

                $stmtEst = $conexion->prepare("
                    SELECT e.*, m.id_grado, m.estado AS estado_matricula
                    FROM estudiante e
                    INNER JOIN matricula m ON e.id_estudiante = m.id_estudiante
                    WHERE e.activo = 1
                      AND m.id_grado IN ($placeholders)
                      AND m.año_lectivo = YEAR(CURDATE())
                    ORDER BY m.id_grado, e.apellido, e.nombre ASC
                ");
                $stmtEst->execute($grados_del_ciclo);
                $estudiantes = $stmtEst->fetchAll(PDO::FETCH_ASSOC);
                $grados      = $grados_del_ciclo;
            } else {
                $mensaje_error = "⚠️ No se pudo determinar el ciclo asignado.";
            }
        } else {
            $mensaje_error = "⚠️ No tienes un ciclo asignado para este año lectivo.";
        }
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
                <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?>
            </span>
            <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-3 mt-4">
        <div class="col">
            <?php if ($rol_usuario === 'administrador'): ?>
                <h2 class="fw-bold" style="color: var(--color-azul-oscuro);">Nómina General de Estudiantes</h2>
                <p style="color: var(--color-azul-claro);">Todos los estudiantes matriculados en <?= date('Y') ?>.</p>
            <?php else: ?>
                <h2 class="fw-bold" style="color: var(--color-azul-oscuro);">
                    Mis Alumnos —
                    <span class="badge" style="background-color: var(--color-verde); color: var(--color-azul-oscuro); font-size: 1rem;">
                        <?= htmlspecialchars($ciclo_docente ?? 'Sin asignar') ?>
                    </span>
                </h2>
                <p style="color: var(--color-azul-claro);">
                    Materia: <strong><?= htmlspecialchars($materia_docente ?? '—') ?></strong>
                    <?php if ($ciclo_docente && isset($ciclos[$ciclo_docente])): ?>
                        &nbsp;|&nbsp; Grados: <strong><?= implode(', ', $ciclos[$ciclo_docente]) ?></strong>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php if ($rol_usuario === 'administrador'): ?>
        <div class="col-auto align-self-center">
            <a href="inscribirAlumno.php" class="btn btn-principal">
                <i class="bi bi-person-plus-fill me-1"></i> Inscribir Alumno
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($mensaje_ok): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <?= $mensaje_ok ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($mensaje_error): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensaje_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!$mensaje_error || $mensaje_ok): ?>

    <div class="row filtro-barra shadow-sm align-items-center bg-white p-3 rounded mb-4">
        <div class="col-md-6 mb-2 mb-md-0">
            <input type="text" id="buscador" class="form-control"
                   placeholder="Buscar por nombre o apellido..."
                   onkeyup="filtrarTabla()">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <select class="form-select" id="filtroGrado" onchange="filtrarTabla()">
                <option value="">
                    <?= $rol_usuario === 'administrador' ? 'Todos los grados...' : 'Todos los grados del ciclo...' ?>
                </option>
                <?php foreach ($grados as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn w-100 btn-principal" onclick="filtrarTabla()">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
        </div>
    </div>

    <div class="tabla-nomina shadow-sm rounded bg-white">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaAlumnos">
                <thead style="background-color: var(--color-azul-oscuro); color: white;">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nombre del Estudiante</th>
                        <th>Grado</th>
                        <th>Fecha Nac.</th>
                        <th>DUI Encargado</th>
                        <th>Teléfono</th>
                        <th class="text-center">Expediente</th>
                        <?php if ($rol_usuario === 'administrador'): ?>
                        <th class="text-center">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($estudiantes) > 0): ?>
                        <?php foreach ($estudiantes as $estu): ?>
                            <tr data-grado="<?= htmlspecialchars($estu['id_grado']) ?>">
                                <td class="ps-4 fw-bold"><?= htmlspecialchars($estu['id_estudiante']) ?></td>
                                <td><?= htmlspecialchars($estu['nombre'] . ' ' . $estu['apellido']) ?></td>
                                <td>
                                    <span class="badge badge-grado">
                                        <?= htmlspecialchars($estu['id_grado']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($estu['fecha_nacimiento'])) ?></td>
                                <td><?= htmlspecialchars($estu['dui_padre_madre']) ?></td>
                                <td><?= htmlspecialchars($estu['telefono_padre_madre']) ?></td>
                                <td class="text-center">
                                    <?php if (!empty($estu['ruta_partida_nacimiento']) && file_exists('../' . $estu['ruta_partida_nacimiento'])): ?>
                                        <a href="../<?= htmlspecialchars($estu['ruta_partida_nacimiento']) ?>"
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-folder2-open"></i> Ver
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($rol_usuario === 'administrador'): ?>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <!-- Botón Editar -->
                                        <button class="btn btn-outline-secondary btn-accion"
                                                title="Editar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar"
                                                onclick="cargarModal(
                                                    '<?= $estu['id_estudiante'] ?>',
                                                    '<?= htmlspecialchars($estu['nombre'], ENT_QUOTES) ?>',
                                                    '<?= htmlspecialchars($estu['apellido'], ENT_QUOTES) ?>',
                                                    '<?= $estu['fecha_nacimiento'] ?>',
                                                    '<?= htmlspecialchars($estu['nombre_padre_madre'], ENT_QUOTES) ?>',
                                                    '<?= htmlspecialchars($estu['telefono_padre_madre'], ENT_QUOTES) ?>',
                                                    '<?= htmlspecialchars($estu['dui_padre_madre'], ENT_QUOTES) ?>',
                                                    '<?= htmlspecialchars($estu['id_grado'], ENT_QUOTES) ?>'
                                                )">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Botón Baja -->
                                        <button class="btn btn-outline-danger btn-accion"
                                                title="Dar de baja"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalBaja"
                                                onclick="cargarBaja(
                                                    '<?= $estu['id_estudiante'] ?>',
                                                    '<?= htmlspecialchars($estu['nombre'] . ' ' . $estu['apellido'], ENT_QUOTES) ?>'
                                                )">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay estudiantes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-end">
        <p class="small text-muted">
            Mostrando <strong id="contadorVisible"><?= count($estudiantes) ?></strong> estudiantes.
        </p>
    </div>

    <?php endif; ?>
</div>

<!-- ===== MODAL EDITAR ===== -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--color-azul-oscuro);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-pencil-fill me-2"></i>Editar Estudiante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="editar">
                <div class="modal-body">
                    <input type="hidden" name="id_estudiante" id="edit_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombres</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Apellidos</label>
                            <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="edit_fecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Grado</label>
                            <select name="id_grado" id="edit_grado" class="form-select" required>
                                <?php foreach ($grados as $g): ?>
                                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Padre/Madre/Tutor</label>
                        <input type="text" name="nombre_padre_madre" id="edit_padre" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DUI del Encargado</label>
                            <input type="text" name="dui_padre_madre" id="edit_dui" class="form-control" maxlength="10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono_padre_madre" id="edit_telefono" class="form-control" maxlength="9" required>
                        </div>
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

<!-- ===== MODAL BAJA ===== -->
<div class="modal fade" id="modalBaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Baja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="baja">
                <div class="modal-body text-center py-4">
                    <input type="hidden" name="id_estudiante" id="baja_id">
                    <i class="bi bi-person-x-fill text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-3 fs-5">¿Está seguro que desea dar de baja a:</p>
                    <p class="fw-bold fs-4" id="baja_nombre"></p>
                    <p class="text-muted small">El estudiante será desactivado y su matrícula cambiará a estado "retirada".</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-person-x-fill me-1"></i> Confirmar Baja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="text-center mt-5 p-4 bg-light">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script>
// Cargar datos en modal de edición
function cargarModal(id, nombre, apellido, fecha, padre, telefono, dui, grado) {
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_nombre').value   = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_fecha').value    = fecha;
    document.getElementById('edit_padre').value    = padre;
    document.getElementById('edit_telefono').value = telefono;
    document.getElementById('edit_dui').value      = dui;
    document.getElementById('edit_grado').value    = grado;
}

// Cargar datos en modal de baja
function cargarBaja(id, nombre) {
    document.getElementById('baja_id').value    = id;
    document.getElementById('baja_nombre').textContent = nombre;
}

// Filtrar tabla
function filtrarTabla() {
    const input = document.getElementById("buscador").value.toLowerCase();
    const select = document.getElementById("filtroGrado");
    const grado  = select ? select.value : "";
    const filas  = document.querySelectorAll("#tablaAlumnos tbody tr");
    let visibles = 0;

    filas.forEach(tr => {
        const nombre    = tr.cells[1]?.textContent.toLowerCase() ?? "";
        const gradoFila = tr.getAttribute("data-grado") ?? "";
        const matchNombre = nombre.includes(input);
        const matchGrado  = grado === "" || gradoFila === grado;

        if (matchNombre && matchGrado) {
            tr.style.display = "";
            visibles++;
        } else {
            tr.style.display = "none";
        }
    });

    const contador = document.getElementById("contadorVisible");
    if (contador) contador.textContent = visibles;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>