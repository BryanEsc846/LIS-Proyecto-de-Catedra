<?php
session_start();
require_once '../config/conexion.php';

/*if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}*/

// -------------------------------------------------------
// ESTRUCTURA DE CICLOS Y SECCIONES
// -------------------------------------------------------
$ciclos = [
    'Primer Ciclo'  => ['1° "A"','1° "B"','2° "A"','2° "B"','3° "A"','3° "B"'],
    'Segundo Ciclo' => ['4° "A"','4° "B"','5° "A"','5° "B"','6° "A"','6° "B"'],
    'Tercer Ciclo'  => ['7° "A"','7° "B"','8° "A"','8° "B"','9° "A"','9° "B"'],
];

// Sección A = grados con "A", Sección B = grados con "B"
$secciones = [
    'Primer Ciclo'  => [
        'A' => ['1° "A"','2° "A"','3° "A"'],
        'B' => ['1° "B"','2° "B"','3° "B"'],
    ],
    'Segundo Ciclo' => [
        'A' => ['4° "A"','5° "A"','6° "A"'],
        'B' => ['4° "B"','5° "B"','6° "B"'],
    ],
    'Tercer Ciclo'  => [
        'A' => ['7° "A"','8° "A"','9° "A"'],
        'B' => ['7° "B"','8° "B"','9° "B"'],
    ],
];

function getCicloDeGrado($grado, $ciclos) {
    foreach ($ciclos as $nombre => $grados) {
        if (in_array($grado, $grados)) return $nombre;
    }
    return null;
}

/**
 * Detecta la sección (A/B/TODOS/completo) de un docente según sus grados asignados.
 * $grados_csv = cadena separada por comas de los grados asignados
 * $total      = cantidad de filas en asignacion_docente para ese docente
 */
function getSeccionDocente($grados_csv, $total) {
    if (!$grados_csv) return null;
    if ($total == 18) return 'TODOS';       // EDF global
    if ($total >= 6) return 'completo';    // todo el ciclo (6 grados)
    if ($total == 3) {
        $tieneA = str_contains($grados_csv, '"A"');
        $tieneB = str_contains($grados_csv, '"B"');
        if ($tieneA && !$tieneB) return 'A';
        if ($tieneB && !$tieneA) return 'B';
    }
    return 'parcial';
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
        $nombre      = trim($_POST['nombre']    ?? '');
        $apellido    = trim($_POST['apellido']  ?? '');
        $email       = trim($_POST['email']     ?? '');
        $password    = $_POST['password']       ?? '';
        $password2   = $_POST['password2']      ?? '';
        $ciclo_new   = $_POST['ciclo_nuevo']    ?? '';
        $seccion_new = $_POST['seccion_nuevo']  ?? 'A';
        $materia_new = $_POST['materia_nuevo']  ?? '';

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

                if ($ciclo_new && $materia_new && $new_id) {
                    $stmtA = $conexion->prepare(
                        "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                         VALUES (?, ?, ?, YEAR(CURDATE()))"
                    );

                    if ($ciclo_new === 'TODOS') {
                        // EDF: todos los grados
                        $todos = array_merge(...array_values($ciclos));
                        foreach ($todos as $g) $stmtA->execute([$new_id, $g, 'EDF']);
                        $desc_asig = 'todos los grados (EDF)';
                    } elseif (isset($secciones[$ciclo_new])) {
                        if ($seccion_new === 'TODOS') {
                            // Todo el ciclo (6 grados) — solo recomendado para EDF del ciclo
                            $grados_asig = $ciclos[$ciclo_new];
                        } else {
                            // Sección A o B (3 grados) — sin conflictos para materias de 3 bloques
                            $grados_asig = $secciones[$ciclo_new][$seccion_new] ?? $secciones[$ciclo_new]['A'];
                        }
                        foreach ($grados_asig as $g) $stmtA->execute([$new_id, $g, $materia_new]);
                        $seccion_label = ($seccion_new === 'TODOS') ? '(ciclo completo)' : "Sección {$seccion_new}";
                        $desc_asig = "{$ciclo_new} – {$seccion_label}";
                    }
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado y asignado a <strong>{$desc_asig}</strong>.";
                } else {
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado. Usa el lápiz para asignarle ciclo, sección y materia.";
                }
            }
        }
    }

    // --- Editar asignación ---
    if ($action === 'editar_asignacion') {
        $id_docente = (int)($_POST['id_docente'] ?? 0);
        $ciclo_sel  = $_POST['ciclo']    ?? '';
        $seccion_sel= $_POST['seccion']  ?? 'A';
        $id_materia = $_POST['materia']  ?? '';
        $activo     = isset($_POST['activo']) ? 1 : 0;

        if (!$id_docente) {
            $errores[] = 'Docente no válido.';
        } elseif ($ciclo_sel === 'TODOS') {
            // EDF global (todos los grados)
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
            $conexion->prepare(
                "DELETE FROM asignacion_docente WHERE id_usuario = ? AND año_lectivo = YEAR(CURDATE())"
            )->execute([$id_docente]);
            $stmt = $conexion->prepare(
                "INSERT INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo)
                 VALUES (?, ?, ?, YEAR(CURDATE()))"
            );
            if ($seccion_sel === 'TODOS') {
                $grados_asig = $ciclos[$ciclo_sel]; // 6 grados
            } else {
                $grados_asig = $secciones[$ciclo_sel][$seccion_sel] ?? $secciones[$ciclo_sel]['A'];
            }
            foreach ($grados_asig as $g) $stmt->execute([$id_docente, $g, $id_materia]);
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_docente]);
        } elseif ($ciclo_sel === '') {
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_docente]);
        } else {
            $errores[] = 'Ciclo o materia no válidos.';
        }
    }

    // --- Desactivar docente ---
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
           COUNT(a.id_grado) AS total_grados,
           GROUP_CONCAT(a.id_grado ORDER BY a.id_grado SEPARATOR ',') AS grados_lista
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

// Detectar cobertura por ciclo y sección para las tarjetas de resumen
$cobertura_seccion = [];
foreach ($secciones as $nombre_ciclo => $secs) {
    foreach ($secs as $sec => $grades_sec) {
        $primer_grado = $grades_sec[0];
        $stmt = $conexion->prepare("
            SELECT DISTINCT id_materia
            FROM asignacion_docente
            WHERE id_grado = ? AND año_lectivo = YEAR(CURDATE())
        ");
        $stmt->execute([$primer_grado]);
        $cobertura_seccion[$nombre_ciclo][$sec] = array_column($stmt->fetchAll(), 'id_materia');
    }
    // EDF: verificar si hay EDF en el ciclo (cualquier sección sirve)
    $stmt = $conexion->prepare("
        SELECT DISTINCT id_materia FROM asignacion_docente
        WHERE id_grado = ? AND id_materia = 'EDF' AND año_lectivo = YEAR(CURDATE())
    ");
    $stmt->execute([$secs['A'][0]]);
    if (!$stmt->fetch()) {
        // Revisar en sección B también
        $stmt->execute([$secs['B'][0]]);
    }
    // EDF se marca cubierto si cualquier grado del ciclo lo tiene
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
        .sec-a        { background:#264792; color:#fff; }
        .sec-b        { background:#0d6b6b; color:#fff; }
        .sec-todos    { background:#a0191f; color:#fff; }
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
            <p class="text-muted mb-0">
                Registra docentes y asigna ciclo, <strong>sección (A/B)</strong> y materia.
                Cada docente atiende <strong>3 grados</strong> para evitar conflictos de horario.
            </p>
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

    <!-- =====================================================
         Tarjetas de resumen: cobertura por ciclo y sección
         ===================================================== -->
    <div class="row g-3 mb-4">
    <?php
    $ids_materia = array_column($materias, 'id_materia');
    $materias_no_edf = array_filter($ids_materia, fn($m) => $m !== 'EDF');

    foreach ($secciones as $nombre_ciclo => $secs):
        // Check EDF coverage for any grade in the cycle
        $edf_cubierta_ciclo = false;
        foreach (array_merge($secs['A'], $secs['B']) as $g) {
            if (in_array('EDF', $cobertura_seccion[$nombre_ciclo]['A'] ?? []) ||
                in_array('EDF', $cobertura_seccion[$nombre_ciclo]['B'] ?? [])) {
                $edf_cubierta_ciclo = true; break;
            }
        }
    ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2" style="background:var(--color-azul-oscuro);color:#fff">
                    <small class="fw-bold"><?= $nombre_ciclo ?></small>
                </div>
                <div class="card-body py-2">
                <?php foreach ($secs as $sec => $grades_sec):
                    $cubiertas = $cobertura_seccion[$nombre_ciclo][$sec] ?? [];
                ?>
                    <div class="mb-2">
                        <span class="badge sec-<?= strtolower($sec) ?> me-1">Secc. <?= $sec ?></span>
                        <small class="text-muted"><?= implode(', ',$grades_sec) ?></small>
                        <div class="mt-1">
                        <?php foreach ($materias_no_edf as $mat_id):
                            $ok = in_array($mat_id, $cubiertas);
                        ?>
                            <span class="badge asig-badge me-1 mb-1"
                                  style="background:<?= $ok ? '#1a7c3e' : '#c0392b' ?>;color:#fff">
                                <?= $ok ? '✓' : '✗' ?> <?= htmlspecialchars($mat_id) ?>
                            </span>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                    <!-- EDF es compartido por todo el ciclo -->
                    <hr class="my-1">
                    <small>
                        <span class="badge asig-badge"
                              style="background:<?= $edf_cubierta_ciclo ? '#1a7c3e' : '#c0392b' ?>;color:#fff">
                            <?= $edf_cubierta_ciclo ? '✓' : '✗' ?> EDF (ciclo completo)
                        </span>
                    </small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- =====================================================
         Tabla de docentes
         ===================================================== -->
    <div class="tabla-nomina shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead style="background:var(--color-azul-oscuro);color:#fff">
                    <tr>
                        <th class="ps-4 text-start">Docente</th>
                        <th>Correo</th>
                        <th>Ciclo</th>
                        <th>Sección</th>
                        <th>Materia</th>
                        <th>Grados asignados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($docentes as $doc):
                    $ciclo_actual = $doc['un_grado'] ? getCicloDeGrado($doc['un_grado'], $ciclos) : null;
                    $seccion_det  = getSeccionDocente($doc['grados_lista'] ?? '', (int)$doc['total_grados']);
                    $es_edf_global = ($doc['id_materia'] === 'EDF' && $doc['total_grados'] == 18);

                    // Determine section for edit modal pre-selection
                    $modal_ciclo  = $es_edf_global ? 'TODOS' : ($ciclo_actual ?? '');
                    $modal_seccion= ($seccion_det === 'A' || $seccion_det === 'B') ? $seccion_det : 'A';
                ?>
                <tr class="<?= !$doc['activo'] ? 'row-inactivo' : '' ?>">
                    <td class="ps-4 text-start fw-bold">
                        <?= htmlspecialchars($doc['nombre'].' '.$doc['apellido']) ?>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($doc['email']) ?></small></td>
                    <td>
                        <?php if ($es_edf_global): ?>
                            <span class="badge badge-ciclo" style="background:#a0191f;color:#fff">Todos</span>
                        <?php elseif ($ciclo_actual): ?>
                            <span class="badge badge-ciclo" style="background:var(--color-azul-claro);color:#fff">
                                <?= $ciclo_actual ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($seccion_det === 'A'): ?>
                            <span class="badge sec-a">Secc. A</span>
                        <?php elseif ($seccion_det === 'B'): ?>
                            <span class="badge sec-b">Secc. B</span>
                        <?php elseif ($seccion_det === 'TODOS'): ?>
                            <span class="badge sec-todos">Todos</span>
                        <?php elseif ($seccion_det === 'completo'): ?>
                            <span class="badge bg-secondary">Ciclo completo</span>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $doc['nombre_materia']
                            ? htmlspecialchars($doc['nombre_materia'])
                            : '<span class="text-muted small">—</span>' ?>
                    </td>
                    <td>
                        <small class="text-muted">
                        <?php if ($doc['grados_lista']): ?>
                            <?= htmlspecialchars(str_replace(',', ', ', $doc['grados_lista'])) ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                        </small>
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

                <!-- ===== MODAL EDITAR ===== -->
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
                                <input type="hidden" name="action"     value="editar_asignacion">
                                <input type="hidden" name="id_docente" value="<?= $doc['id_usuario'] ?>">
                                <div class="modal-body">

                                    <!-- Fila: Ciclo + Sección -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Ciclo escolar</label>
                                            <select class="form-select" name="ciclo"
                                                    id="editCiclo<?= $doc['id_usuario'] ?>"
                                                    onchange="editActualizar(<?= $doc['id_usuario'] ?>)">
                                                <option value="">— Sin asignar —</option>
                                                <?php foreach ($ciclos as $nc => $gc): ?>
                                                <option value="<?= htmlspecialchars($nc) ?>"
                                                    <?= ($modal_ciclo === $nc) ? 'selected' : '' ?>>
                                                    <?= $nc ?>
                                                </option>
                                                <?php endforeach; ?>
                                                <option value="TODOS" <?= ($modal_ciclo === 'TODOS') ? 'selected' : '' ?>>
                                                    Todos los grados (solo EDF)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="wrapSeccionEdit<?= $doc['id_usuario'] ?>">
                                            <label class="form-label fw-bold">Sección</label>
                                            <select class="form-select" name="seccion"
                                                    id="editSeccion<?= $doc['id_usuario'] ?>"
                                                    onchange="editActualizar(<?= $doc['id_usuario'] ?>)">
                                                <option value="A"    <?= ($modal_seccion==='A')     ?'selected':'' ?>>
                                                    Sección A (1°A, 2°A, 3°A… del ciclo)
                                                </option>
                                                <option value="B"    <?= ($modal_seccion==='B')     ?'selected':'' ?>>
                                                    Sección B (1°B, 2°B, 3°B… del ciclo)
                                                </option>
                                                <option value="TODOS" <?= ($seccion_det==='completo')?'selected':'' ?>>
                                                    Ciclo completo (6 grados — solo EDF del ciclo)
                                                </option>
                                            </select>
                                            <div class="form-text">Para materias con 3 bloques semanales, usa Sección A o B.</div>
                                        </div>
                                    </div>

                                    <!-- Preview grados -->
                                    <div id="editPreview<?= $doc['id_usuario'] ?>" class="mb-3">
                                        <?php if ($doc['grados_lista']): ?>
                                        <div class="alert alert-info py-2 mb-0">
                                            <small><i class="bi bi-info-circle me-1"></i>
                                            <strong>Grados actuales:</strong>
                                            <?= htmlspecialchars(str_replace(',', ', ', $doc['grados_lista'])) ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Materia -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Materia</label>
                                        <select class="form-select" name="materia"
                                                id="editMateria<?= $doc['id_usuario'] ?>">
                                            <option value="">— Sin materia —</option>
                                            <?php foreach ($materias as $m): ?>
                                            <option value="<?= $m['id_materia'] ?>"
                                                <?= ($doc['id_materia'] === $m['id_materia']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($m['nombre_materia']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text text-muted">
                                            "Todos los grados" o "Ciclo completo" → solo permite Educación Física.
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

<!-- =====================================================
     MODAL NUEVO DOCENTE
     ===================================================== -->
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

                    <!-- Asignación -->
                    <p class="fw-bold mb-2" style="color:var(--color-azul-oscuro)">
                        <i class="bi bi-book me-1"></i> Asignación de ciclo, sección y materia
                        <span class="text-muted fw-normal small">(opcional)</span>
                    </p>
                    <div class="alert alert-info py-2 mb-3">
                        <small>
                            <i class="bi bi-lightbulb me-1"></i>
                            <strong>Tip sin conflictos:</strong> asigna <strong>Sección A</strong> o
                            <strong>Sección B</strong> (3 grados) para cualquier materia de 6h/semana.
                            Solo EDF puede cubrir el ciclo completo (6 grados) sin conflicto.
                        </small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ciclo escolar</label>
                            <select class="form-select" name="ciclo_nuevo" id="cicloNuevo"
                                    onchange="nuevoActualizar()">
                                <option value="">— Sin asignar por ahora —</option>
                                <?php foreach ($ciclos as $nc => $gc): ?>
                                <option value="<?= htmlspecialchars($nc) ?>"><?= $nc ?></option>
                                <?php endforeach; ?>
                                <option value="TODOS">Todos los grados (solo EDF)</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="wrapSeccionNuevo">
                            <label class="form-label fw-bold">Sección</label>
                            <select class="form-select" name="seccion_nuevo" id="seccionNuevo"
                                    onchange="nuevoActualizar()">
                                <option value="A">Sección A (1°A, 2°A, 3°A…)</option>
                                <option value="B">Sección B (1°B, 2°B, 3°B…)</option>
                                <option value="TODOS">Ciclo completo (solo EDF)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Materia</label>
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
const ciclosData   = <?= json_encode($ciclos) ?>;
const seccionesData= <?= json_encode($secciones) ?>;

/* ---- Formulario NUEVO DOCENTE ---- */
function nuevoActualizar() {
    const ciclo   = document.getElementById('cicloNuevo').value;
    const seccion = document.getElementById('seccionNuevo').value;
    const selMat  = document.getElementById('materiaNuevo');
    const preview = document.getElementById('previewNuevo');
    const wrapSec = document.getElementById('wrapSeccionNuevo');

    if (ciclo === 'TODOS') {
        wrapSec.style.display = 'none';
        // Forzar EDF
        for (let opt of selMat.options) opt.selected = (opt.value === 'EDF');
        preview.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Grados:</strong> Los 18 grados (1° – 9°). Solo EDF.
        </div>`;
        return;
    }

    wrapSec.style.display = '';

    if (!ciclo) { preview.innerHTML = ''; return; }

    let grados = [];
    if (seccion === 'TODOS') {
        grados = ciclosData[ciclo] || [];
    } else {
        grados = (seccionesData[ciclo] && seccionesData[ciclo][seccion]) || [];
    }

    if (seccion === 'TODOS') {
        for (let opt of selMat.options) opt.selected = (opt.value === 'EDF');
        preview.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Grados (ciclo completo):</strong> ${grados.join(', ')} — solo EDF.
        </div>`;
    } else {
        preview.innerHTML = `<div class="alert alert-info py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Grados asignados:</strong> ${grados.join(', ')}
            &nbsp;|&nbsp; <span class="text-success fw-bold">Sin conflictos de horario ✓</span>
        </div>`;
    }
}

/* ---- Formulario EDITAR (por id) ---- */
function editActualizar(id) {
    const ciclo   = document.getElementById('editCiclo'   + id).value;
    const seccion = document.getElementById('editSeccion' + id).value;
    const selMat  = document.getElementById('editMateria' + id);
    const preview = document.getElementById('editPreview' + id);
    const wrapSec = document.getElementById('wrapSeccionEdit' + id);

    if (ciclo === 'TODOS') {
        wrapSec.style.display = 'none';
        for (let opt of selMat.options) opt.selected = (opt.value === 'EDF');
        preview.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>Los 18 grados. Solo EDF.</div>`;
        return;
    }

    wrapSec.style.display = '';
    if (!ciclo) { preview.innerHTML = ''; return; }

    let grados = [];
    if (seccion === 'TODOS') {
        grados = ciclosData[ciclo] || [];
        for (let opt of selMat.options) opt.selected = (opt.value === 'EDF');
        preview.innerHTML = `<div class="alert alert-warning py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Ciclo completo:</strong> ${grados.join(', ')} — solo EDF.</div>`;
    } else {
        grados = (seccionesData[ciclo] && seccionesData[ciclo][seccion]) || [];
        preview.innerHTML = `<div class="alert alert-info py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Grados:</strong> ${grados.join(', ')}
            &nbsp;|&nbsp;<span class="text-success fw-bold">Sin conflictos ✓</span></div>`;
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

// Reabrir modal nuevo si hubo error
<?php if (!empty($errores) && ($_POST['action'] ?? '') === 'crear_docente'): ?>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('modalNuevoDocente')).show();
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
