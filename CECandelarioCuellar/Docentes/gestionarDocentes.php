<?php
session_start();
require_once '../config/conexion.php';

/*if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}*/

$ciclos = [
    'Primer Ciclo'  => ['1° "A"','1° "B"','2° "A"','2° "B"','3° "A"','3° "B"'],
    'Segundo Ciclo' => ['4° "A"','4° "B"','5° "A"','5° "B"','6° "A"','6° "B"'],
    'Tercer Ciclo'  => ['7° "A"','7° "B"','8° "A"','8° "B"','9° "A"','9° "B"'],
];

function getCicloDeGrado($grado, $ciclos) {
    foreach ($ciclos as $nombre => $grados) {
        if (in_array($grado, $grados)) return $nombre;
    }
    return null;
}

$errores  = [];
$exito    = null;

// -------------------------------------------------------
// PROCESAMIENTO POST
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Registrar nuevo docente ---
    if ($action === 'crear_docente') {
        $nombre     = trim($_POST['nombre']    ?? '');
        $apellido   = trim($_POST['apellido']  ?? '');
        $email      = trim($_POST['email']     ?? '');
        $password   = $_POST['password']       ?? '';
        $password2  = $_POST['password2']      ?? '';
        $ciclo_new  = $_POST['ciclo_nuevo']    ?? '';
        $materia_new= $_POST['materia_nuevo']  ?? '';

        if (!$nombre || !$apellido || !$email || !$password) {
            $errores[] = 'Nombre, apellido, correo y contraseña son obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        } elseif (strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif ($password !== $password2) {
            $errores[] = 'Las contraseñas no coinciden.';
        } else {
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errores[] = 'Ya existe un usuario con ese correo electrónico.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $conexion->prepare(
                    "INSERT INTO usuario (nombre, apellido, email, password_hash, rol, activo)
                     VALUES (?, ?, ?, ?, 'docente', 1)"
                )->execute([$nombre, $apellido, $email, $hash]);
                $new_id = $conexion->lastInsertId();

                // Asignar ciclo + materia si se seleccionaron
                if ($ciclo_new && $materia_new && $new_id) {
                    if ($ciclo_new === 'TODOS' && $materia_new === 'EDF') {
                        $todos = array_merge(...array_values($ciclos));
                        $stmtA = $conexion->prepare(
                            "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                             VALUES (?, ?, 'EDF', YEAR(CURDATE()))"
                        );
                        foreach ($todos as $g) $stmtA->execute([$new_id, $g]);
                    } elseif (isset($ciclos[$ciclo_new])) {
                        $stmtA = $conexion->prepare(
                            "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                             VALUES (?, ?, ?, YEAR(CURDATE()))"
                        );
                        foreach ($ciclos[$ciclo_new] as $g) $stmtA->execute([$new_id, $g, $materia_new]);
                    }
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado y asignado a <strong>{$ciclo_new}</strong>.";
                } else {
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado. Usa el lápiz para asignarle ciclo y materia.";
                }
            }
        }
    }

    // --- Editar asignación (ciclo + materia + activo) ---
    if ($action === 'editar_asignacion') {
        $id_docente = (int)($_POST['id_docente'] ?? 0);
        $ciclo_sel  = $_POST['ciclo']   ?? '';
        $id_materia = $_POST['materia'] ?? '';
        $activo     = isset($_POST['activo']) ? 1 : 0;

        if (!$id_docente) {
            $errores[] = 'Docente no válido.';
        } elseif ($ciclo_sel === 'TODOS' && $id_materia === 'EDF') {
            // Caso especial: EDF → todos los grados
            $todos_grados = array_merge(...array_values($ciclos));
            $conexion->prepare(
                "DELETE FROM asignacion_docente WHERE id_usuario = ? AND año_lectivo = YEAR(CURDATE())"
            )->execute([$id_docente]);
            $stmt = $conexion->prepare(
                "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                 VALUES (?, ?, 'EDF', YEAR(CURDATE()))"
            );
            foreach ($todos_grados as $g) $stmt->execute([$id_docente, $g]);
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_docente]);
        } elseif (isset($ciclos[$ciclo_sel]) && $id_materia) {
            $grados_ciclo = $ciclos[$ciclo_sel];
            $conexion->prepare(
                "DELETE FROM asignacion_docente WHERE id_usuario = ? AND año_lectivo = YEAR(CURDATE())"
            )->execute([$id_docente]);
            $stmt = $conexion->prepare(
                "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                 VALUES (?, ?, ?, YEAR(CURDATE()))"
            );
            foreach ($grados_ciclo as $g) $stmt->execute([$id_docente, $g, $id_materia]);
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_docente]);
        } elseif ($ciclo_sel === '') {
            // Solo actualizar estado activo sin tocar asignaciones
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_docente]);
        } else {
            $errores[] = 'Ciclo o materia no válidos.';
        }
    }

    // --- Eliminar docente (desactivar, no borrar) ---
    if ($action === 'eliminar_docente') {
        $id_docente = (int)($_POST['id_docente'] ?? 0);
        if ($id_docente) {
            $conexion->prepare("UPDATE usuario SET activo = 0 WHERE id_usuario = ? AND rol = 'docente'")
                     ->execute([$id_docente]);
            $exito = 'Docente desactivado correctamente.';
        }
    }
}

// -------------------------------------------------------
// CARGAR DATOS
// -------------------------------------------------------
$docentes = $conexion->query("
    SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.activo,
           MIN(a.id_grado)   AS un_grado,
           a.id_materia,
           m.nombre_materia,
           COUNT(a.id_grado) AS total_grados
    FROM usuario u
    LEFT JOIN asignacion_docente a
           ON u.id_usuario = a.id_usuario AND a.año_lectivo = YEAR(CURDATE())
    LEFT JOIN materia m ON a.id_materia = m.id_materia
    WHERE u.rol = 'docente'
    GROUP BY u.id_usuario, u.nombre, u.apellido, u.email, u.activo,
             a.id_materia, m.nombre_materia
    ORDER BY u.activo DESC, u.apellido, u.nombre
")->fetchAll();

$materias = $conexion->query(
    "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia"
)->fetchAll();

// Detectar materias ya asignadas por ciclo para el año actual
$asignaciones_ciclo = [];
foreach ($ciclos as $nombre_ciclo => $grados_ciclo) {
    $primer_grado = $grados_ciclo[0];
    $stmt = $conexion->prepare("
        SELECT id_materia
        FROM asignacion_docente
        WHERE id_grado = ? AND año_lectivo = YEAR(CURDATE())
    ");
    $stmt->execute([$primer_grado]);
    foreach ($stmt->fetchAll() as $row) {
        $asignaciones_ciclo[$nombre_ciclo][] = $row['id_materia'];
    }
}
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
    <style>
        .tabla-nomina { background:#fff; border-radius:15px; overflow:hidden; }
        .badge-ciclo  { font-size:0.78rem; }
        .row-inactivo { opacity: 0.55; }
        .asig-badge   { font-size:0.75rem; padding:3px 8px; border-radius:4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">Centro Escolar Candelario Cuellar</span>
        <a href="../dashboard/dashboardAdmin.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</nav>

<div class="container mt-5">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-0">Gestión de Docentes</h2>
            <p class="text-muted mb-0">Registra docentes y asigna ciclo y materia</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-principal" data-bs-toggle="modal" data-bs-target="#modalNuevoDocente">
                <i class="bi bi-person-plus-fill me-1"></i> Registrar Nuevo Docente
            </button>
        </div>
    </div>

    <?php if (!empty($errores)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= implode('<br>', array_map('htmlspecialchars', $errores)) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($exito): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i> <?= $exito ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Resumen de asignaciones por ciclo -->
    <div class="row g-3 mb-4">
    <?php foreach ($ciclos as $nombre_ciclo => $grados_ciclo):
        $asig = $asignaciones_ciclo[$nombre_ciclo] ?? [];
        $todas = array_column($materias, 'id_materia');
        $faltantes = array_diff($todas, $asig, ['EDF']); // EDF es global
    ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2" style="background:var(--color-azul-oscuro);color:#fff">
                    <small class="fw-bold"><?= $nombre_ciclo ?></small>
                </div>
                <div class="card-body py-2">
                    <?php foreach ($materias as $m):
                        $cubierta = in_array($m['id_materia'], $asig);
                        $esEDF    = $m['id_materia'] === 'EDF';
                    ?>
                    <span class="badge asig-badge me-1 mb-1"
                          style="background:<?= $cubierta||$esEDF ? '#1a7c3e' : '#c0392b' ?>;color:#fff">
                        <?= $cubierta||$esEDF ? '✓' : '✗' ?> <?= htmlspecialchars($m['id_materia']) ?>
                    </span>
                    <?php endforeach; ?>
                    <?php if (!empty($faltantes)): ?>
                    <div class="mt-1">
                        <small class="text-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Faltan: <?= implode(', ', $faltantes) ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Tabla de docentes -->
    <div class="tabla-nomina shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead style="background:var(--color-azul-oscuro);color:#fff">
                    <tr>
                        <th class="ps-4 text-start">Docente</th>
                        <th>Correo</th>
                        <th>Ciclo</th>
                        <th>Materia</th>
                        <th>Grados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($docentes as $doc):
                    $ciclo_actual  = $doc['un_grado'] ? getCicloDeGrado($doc['un_grado'], $ciclos) : null;
                    $es_edf_global = ($doc['id_materia'] === 'EDF' && $doc['total_grados'] == 18);
                    $grados_desc   = null;
                    if ($es_edf_global) {
                        $grados_desc = 'Todos los grados';
                    } elseif ($ciclo_actual) {
                        $grados_desc = implode(', ', $ciclos[$ciclo_actual]);
                    }
                ?>
                <tr class="<?= !$doc['activo'] ? 'row-inactivo' : '' ?>">
                    <td class="ps-4 text-start fw-bold">
                        <?= htmlspecialchars($doc['nombre'].' '.$doc['apellido']) ?>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($doc['email']) ?></small></td>
                    <td>
                        <?php if ($es_edf_global): ?>
                            <span class="badge badge-ciclo" style="background:#a0191f;color:#fff">Todos los ciclos</span>
                        <?php elseif ($ciclo_actual): ?>
                            <span class="badge badge-ciclo" style="background:var(--color-azul-claro);color:#fff">
                                <?= $ciclo_actual ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $doc['nombre_materia']
                            ? htmlspecialchars($doc['nombre_materia'])
                            : '<span class="text-muted small">—</span>' ?>
                    </td>
                    <td>
                        <?php if ($grados_desc): ?>
                            <small class="text-muted"><?= htmlspecialchars($grados_desc) ?></small>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $doc['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $doc['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit<?= $doc['id_usuario'] ?>">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </td>
                </tr>

                <!-- ===== MODAL EDITAR ASIGNACIÓN ===== -->
                <div class="modal fade" id="modalEdit<?= $doc['id_usuario'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background:var(--color-azul-oscuro)">
                                <h5 class="modal-title text-white">
                                    <i class="bi bi-person-fill me-2"></i>
                                    <?= htmlspecialchars($doc['nombre'].' '.$doc['apellido']) ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action"    value="editar_asignacion">
                                <input type="hidden" name="id_docente" value="<?= $doc['id_usuario'] ?>">
                                <div class="modal-body">

                                    <!-- Ciclo -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ciclo escolar</label>
                                        <select class="form-select" name="ciclo"
                                                id="selectCiclo<?= $doc['id_usuario'] ?>"
                                                onchange="actualizarPreview(<?= $doc['id_usuario'] ?>)">
                                            <option value="">— Sin asignar (solo cambiar estado) —</option>
                                            <?php foreach ($ciclos as $nc => $gc): ?>
                                            <option value="<?= htmlspecialchars($nc) ?>"
                                                <?= ($ciclo_actual === $nc && !$es_edf_global) ? 'selected' : '' ?>>
                                                <?= $nc ?>
                                                (<?= implode(', ', array_filter($gc, fn($g)=>str_contains($g,'"A"'))) ?>…)
                                            </option>
                                            <?php endforeach; ?>
                                            <option value="TODOS" <?= $es_edf_global ? 'selected' : '' ?>>
                                                Todos los grados (solo EDF)
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Preview grados -->
                                    <div id="preview<?= $doc['id_usuario'] ?>" class="mb-3">
                                        <?php if ($grados_desc): ?>
                                        <div class="alert alert-info py-2 mb-0">
                                            <small><i class="bi bi-info-circle me-1"></i>
                                            <strong>Grados incluidos:</strong> <?= htmlspecialchars($grados_desc) ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Materia -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Materia</label>
                                        <select class="form-select" name="materia"
                                                id="selectMateria<?= $doc['id_usuario'] ?>">
                                            <option value="">— Sin materia —</option>
                                            <?php foreach ($materias as $m): ?>
                                            <option value="<?= $m['id_materia'] ?>"
                                                <?= ($doc['id_materia'] === $m['id_materia']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($m['nombre_materia']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text text-muted">
                                            Si seleccionas "Todos los grados", solo se permitirá Educación Física.
                                        </div>
                                    </div>

                                    <!-- Activo -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="activo"
                                               id="activo<?= $doc['id_usuario'] ?>"
                                               <?= $doc['activo'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="activo<?= $doc['id_usuario'] ?>">
                                            Docente activo
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
                <!-- ===== FIN MODAL EDITAR ===== -->

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-end">
        <small class="text-muted">Total docentes registrados: <strong><?= count($docentes) ?></strong></small>
    </div>

</div>

<!-- ===== MODAL NUEVO DOCENTE ===== -->
<div class="modal fade" id="modalNuevoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--color-azul-oscuro)">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-plus-fill me-2"></i> Registrar Nuevo Docente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="crear_docente">
                <div class="modal-body">

                    <!-- Datos personales -->
                    <p class="fw-bold mb-2" style="color:var(--color-azul-oscuro)">
                        <i class="bi bi-person me-1"></i> Datos del docente
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre"
                                   placeholder="Ej: María" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellido"
                                   placeholder="Ej: González" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email"
                                   placeholder="docente@cecc.edu.sv" required>
                            <div class="form-text">Con este correo el docente iniciará sesión.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password"
                                       id="pwdNuevo" placeholder="Mín. 6 caracteres" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('pwdNuevo','eyeNuevo')">
                                    <i class="bi bi-eye" id="eyeNuevo"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password2"
                                       id="pwdNuevo2" placeholder="Repetir contraseña" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('pwdNuevo2','eyeNuevo2')">
                                    <i class="bi bi-eye" id="eyeNuevo2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Asignación de ciclo y materia -->
                    <p class="fw-bold mb-2" style="color:var(--color-azul-oscuro)">
                        <i class="bi bi-book me-1"></i> Asignación de ciclo y materia
                        <span class="text-muted fw-normal small">(opcional, se puede hacer después)</span>
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Ciclo escolar</label>
                            <select class="form-select" name="ciclo_nuevo" id="cicloNuevo"
                                    onchange="actualizarMateriaDisponible()">
                                <option value="">— Sin asignar por ahora —</option>
                                <?php foreach ($ciclos as $nc => $gc): ?>
                                <option value="<?= htmlspecialchars($nc) ?>">
                                    <?= $nc ?>
                                    (<?= implode(', ', array_filter($gc, fn($g) => str_contains($g,'"A"'))) ?>…)
                                </option>
                                <?php endforeach; ?>
                                <option value="TODOS">Todos los grados (solo EDF)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Materia que impartirá</label>
                            <select class="form-select" name="materia_nuevo" id="materiaNuevo">
                                <option value="">— Sin asignar por ahora —</option>
                                <?php foreach ($materias as $m): ?>
                                <option value="<?= $m['id_materia'] ?>">
                                    <?= htmlspecialchars($m['nombre_materia']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12" id="previewNuevo"></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-check-fill me-1"></i> Registrar Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ===== FIN MODAL NUEVO DOCENTE ===== -->

<footer class="text-center mt-5 p-4">
    <small style="color:var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<!-- Datos para JS -->
<script>
const ciclosData = <?= json_encode($ciclos) ?>;

function actualizarPreview(idDocente) {
    const select  = document.getElementById('selectCiclo'  + idDocente);
    const selMat  = document.getElementById('selectMateria'+ idDocente);
    const preview = document.getElementById('preview'      + idDocente);
    const ciclo   = select.value;

    if (ciclo === 'TODOS') {
        preview.innerHTML = `
            <div class="alert alert-warning py-2 mb-0">
                <small><i class="bi bi-info-circle me-1"></i>
                <strong>Grados incluidos:</strong> Los 18 grados (1° al 9°).
                Solo aplica para Educación Física.</small>
            </div>`;
        // Forzar selección de EDF
        for (let opt of selMat.options) {
            if (opt.value === 'EDF') { opt.selected = true; break; }
        }
    } else if (ciclo && ciclosData[ciclo]) {
        const grados = ciclosData[ciclo].join(', ');
        preview.innerHTML = `
            <div class="alert alert-info py-2 mb-0">
                <small><i class="bi bi-info-circle me-1"></i>
                <strong>Grados incluidos:</strong> ${grados}</small>
            </div>`;
    } else {
        preview.innerHTML = '';
    }
}

function actualizarMateriaDisponible() {
    const ciclo   = document.getElementById('cicloNuevo').value;
    const selMat  = document.getElementById('materiaNuevo');
    const preview = document.getElementById('previewNuevo');

    if (ciclo === 'TODOS') {
        // Forzar EDF
        for (let opt of selMat.options) {
            opt.selected = (opt.value === 'EDF');
        }
        preview.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            Al seleccionar <strong>Todos los grados</strong> solo se puede asignar <strong>Educación Física</strong>.
        </div>`;
    } else if (ciclo && ciclosData[ciclo]) {
        const grados = ciclosData[ciclo].join(', ');
        preview.innerHTML = `<div class="alert alert-info py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Grados que atenderá:</strong> ${grados}
        </div>`;
    } else {
        preview.innerHTML = '';
    }
}

function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Abrir modal de nuevo docente si hubo error al crearlo
<?php if (!empty($errores) && ($_POST['action'] ?? '') === 'crear_docente'): ?>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('modalNuevoDocente'));
    modal.show();
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
