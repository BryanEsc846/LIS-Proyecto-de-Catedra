<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}

// -------------------------------------------------------
// Plantillas Semanales Optimizadas (Matriz Anti-Colisiones)
// Diseñadas para que los offsets 0 al 4 encajen perfectamente
// sin que un mismo docente choque, soportando hasta 5 secciones.
// -------------------------------------------------------
$WEEKLY_TEMPLATES = [
    0 => [ // Rotación 0
        ['MAT', 'LYL', 'CYV'], // Lunes
        ['CYT', 'MAT', 'LYL'], // Martes
        ['CYV', 'ING', 'MAT'], // Miércoles
        ['LYL', 'CYT', 'ING'], // Jueves
        ['EDF', 'CYV', 'CYT']  // Viernes
    ],
    1 => [ // Rotación 1
        ['ING', 'MAT', 'LYL'], // Lunes
        ['CYV', 'ING', 'MAT'], // Martes
        ['LYL', 'CYT', 'EDF'], // Miércoles
        ['MAT', 'CYV', 'CYT'], // Jueves
        ['CYT', 'LYL', 'CYV']  // Viernes
    ],
    2 => [ // Rotación 2
        ['CYT', 'ING', 'MAT'], // Lunes
        ['LYL', 'CYT', 'ING'], // Martes
        ['MAT', 'CYV', 'CYT'], // Miércoles
        ['EDF', 'LYL', 'CYV'], // Jueves
        ['CYV', 'MAT', 'LYL']  // Viernes
    ],
    3 => [ // Rotación 3
        ['CYV', 'CYT', 'ING'], // Lunes
        ['MAT', 'CYV', 'CYT'], // Martes
        ['ING', 'LYL', 'CYV'], // Miércoles
        ['CYT', 'MAT', 'LYL'], // Jueves
        ['LYL', 'EDF', 'MAT']  // Viernes
    ],
    4 => [ // Rotación 4
        ['LYL', 'CYV', 'CYT'], // Lunes
        ['ING', 'LYL', 'CYV'], // Martes
        ['CYT', 'MAT', 'LYL'], // Miércoles
        ['CYV', 'ING', 'MAT'], // Jueves
        ['MAT', 'CYT', 'EDF']  // Viernes
    ],
    5 => [ // Rotación 5 (¡NUEVA! Perfectamente encajada con la 3 y 4)
        ['ING', 'MAT', 'CYV'], // Lunes
        ['CYV', 'MAT', 'LYL'], // Martes
        ['MAT', 'CYT', 'ING'], // Miércoles
        ['LYL', 'CYV', 'CYT'], // Jueves
        ['EDF', 'LYL', 'CYT']  // Viernes
    ]
];

// Cada bloque ocupa 2 períodos consecutivos (2 horas)
$TIME_SLOTS = [
    ['07:10:00','08:30:00'], // Bloque 1
    ['08:50:00','10:10:00'], // Bloque 2
    ['10:30:00','11:50:00'], // Bloque 3
];

$DIAS_SEMANA = ['Lunes','Martes','Miercoles','Jueves','Viernes'];

// Offset de rotación por grado dentro del ciclo (0-5)
$GRADE_OFFSETS = [
    '1° "A"'=>0,'1° "B"'=>1,'2° "A"'=>2,'2° "B"'=>3,'3° "A"'=>4,'3° "B"'=>5,
    '4° "A"'=>0,'4° "B"'=>1,'5° "A"'=>2,'5° "B"'=>3,'6° "A"'=>4,'6° "B"'=>5,
    '7° "A"'=>0,'7° "B"'=>1,'8° "A"'=>2,'8° "B"'=>3,'9° "A"'=>4,'9° "B"'=>5,
];

// -------------------------------------------------------
// Función de generación ultrarrápida
// -------------------------------------------------------
function generarTodosLosHorarios($conexion, $WEEKLY_TEMPLATES, $TIME_SLOTS, $DIAS_SEMANA, $GRADE_OFFSETS) {
    $anio = date('Y');

    // Eliminar horarios existentes del año
    $conexion->prepare("DELETE FROM horario WHERE año_lectivo = ?")->execute([$anio]);

    $stmtHorario  = $conexion->prepare("INSERT INTO horario (id_grado, año_lectivo, generado_auto) VALUES (?, ?, 1)");
    $stmtDetalle  = $conexion->prepare(
        "INSERT INTO horario_detalle (id_horario, id_materia, id_usuario, dia_semana, hora_inicio, hora_fin)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmtDocente  = $conexion->prepare(
        "SELECT id_usuario FROM asignacion_docente
         WHERE id_grado = ? AND id_materia = ? AND año_lectivo = ?
         LIMIT 1"
    );

    // Red de seguridad (Validador de cruces)
    $stmtVerificarGlobal = $conexion->prepare(
        "SELECT COUNT(*) as count FROM horario_detalle hd
         JOIN horario h ON hd.id_horario = h.id_horario
         WHERE hd.dia_semana = ? 
           AND hd.hora_inicio = ?
           AND hd.id_usuario = ?
           AND h.año_lectivo = ?"
    );

    $errores = [];
    $exitosos = 0;

    foreach ($GRADE_OFFSETS as $id_grado => $offset) {
        $stmtHorario->execute([$id_grado, $anio]);
        $id_horario = $conexion->lastInsertId();
        $insertados  = 0;

        for ($d = 0; $d < count($DIAS_SEMANA); $d++) {
            $materias = $WEEKLY_TEMPLATES[$offset][$d];

            for ($p = 0; $p < count($TIME_SLOTS); $p++) {
                $id_materia = $materias[$p];

                $stmtDocente->execute([$id_grado, $id_materia, $anio]);
                $docente = $stmtDocente->fetch();

                if (!$docente) {
                    $errores[] = "Falta asignar docente a la materia <strong>{$id_materia}</strong> en <strong>{$id_grado}</strong>.";
                    continue;
                }

                $stmtVerificarGlobal->execute([
                    $DIAS_SEMANA[$d],
                    $TIME_SLOTS[$p][0],
                    $docente['id_usuario'],
                    $anio
                ]);
                
                if ($stmtVerificarGlobal->fetchColumn() > 0) {
                    $errores[] = "Cruce evitado: El docente de {$id_materia} ya da clases el {$DIAS_SEMANA[$d]} de {$TIME_SLOTS[$p][0]}.";
                    continue;
                }

                // Insertar el horario final
                $stmtDetalle->execute([
                    $id_horario,
                    $id_materia,
                    $docente['id_usuario'],
                    $DIAS_SEMANA[$d],
                    $TIME_SLOTS[$p][0],
                    $TIME_SLOTS[$p][1],
                ]);
                $insertados++;
            }
        }
        if ($insertados == 15) $exitosos++;
    }

    return ['exitosos' => $exitosos, 'errores' => array_unique($errores)];
}

// -------------------------------------------------------
// Procesamiento POST
// -------------------------------------------------------
$resultado = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar'])) {
    $resultado = generarTodosLosHorarios(
        $conexion, $WEEKLY_TEMPLATES, $TIME_SLOTS, $DIAS_SEMANA, $GRADE_OFFSETS
    );
}

// -------------------------------------------------------
// Estado actual de horarios por grado
// -------------------------------------------------------
$anio_actual = date('Y');
$estado_grados = $conexion->prepare("
    SELECT g.id_grado,
           h.id_horario,
           h.fecha_generacion,
           COUNT(hd.id_detalle) AS total_clases
    FROM grado g
    LEFT JOIN horario h ON g.id_grado = h.id_grado AND h.año_lectivo = ?
    LEFT JOIN horario_detalle hd ON h.id_horario = hd.id_horario
    GROUP BY g.id_grado, h.id_horario, h.fecha_generacion
    ORDER BY g.id_grado
");
$estado_grados->execute([$anio_actual]);
$grados_estado = $estado_grados->fetchAll();

$con_horario = count(array_filter($grados_estado, fn($r) => $r['id_horario'] && $r['total_clases'] == 15));
$sin_horario = count($grados_estado) - $con_horario;

$docentes_sin_asig = $conexion->query("
    SELECT u.nombre, u.apellido FROM usuario u
    WHERE u.rol = 'docente' AND u.activo = 1
    AND u.id_usuario NOT IN (SELECT DISTINCT id_usuario FROM asignacion_docente WHERE año_lectivo = YEAR(CURDATE()))
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Horarios - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .badge-grado { font-size: 0.8rem; }
        .card-stat   { border-left: 5px solid var(--color-azul-claro); }
        .card-ok     { border-left: 5px solid #198754; }
        .card-warn   { border-left: 5px solid #ffc107; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">Centro Escolar Candelario Cuellar</span>
        <a href="horariosAdmin.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Volver a Horarios
        </a>
    </div>
</nav>

<div class="container mt-5">

    <div class="row mb-4 text-center">
        <div class="col">
            <h2 class="fw-bold">Generación Automática de Horarios</h2>
            <p class="text-muted">Año lectivo <?= $anio_actual ?></p>
        </div>
    </div>

    <?php if ($resultado): ?>
    <div class="alert alert-<?= empty($resultado['errores']) ? 'success' : 'warning' ?> alert-dismissible fade show" role="alert">
        <i class="bi bi-<?= empty($resultado['errores']) ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>
        <strong><?= $resultado['exitosos'] ?> grados</strong> procesados correctamente.
        <?php if (!empty($resultado['errores'])): ?>
            <hr>
            <p class="mb-1"><strong>Advertencias:</strong></p>
            <ul class="mb-0 small">
                <?php foreach ($resultado['errores'] as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-grid-3x3-gap-fill fs-2 me-3" style="color:var(--color-azul-claro)"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= count($grados_estado) ?></div>
                        <div class="text-muted small">Total de grados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-ok shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-2 me-3 text-success"></i>
                    <div>
                        <div class="fs-4 fw-bold text-success"><?= $con_horario ?></div>
                        <div class="text-muted small">Con horario generado</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-warn shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill fs-2 me-3 text-warning"></i>
                    <div>
                        <div class="fs-4 fw-bold text-warning"><?= $sin_horario ?></div>
                        <div class="text-muted small">Sin horario</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($docentes_sin_asig)): ?>
    <div class="alert alert-warning mb-4">
        <i class="bi bi-person-exclamation me-2"></i>
        <strong>Docentes sin asignación de ciclo/materia:</strong>
        <?= implode(', ', array_map(fn($d) => htmlspecialchars($d['nombre'].' '.$d['apellido']), $docentes_sin_asig)) ?>.
        El horario de los grados cubiertos por estos docentes quedará incompleto.
    </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body text-center py-4">
            <h5 class="fw-bold mb-2">
                <i class="bi bi-magic me-2"></i>Generar / Regenerar todos los horarios
            </h5>
            <p class="text-muted small mb-3">
                Se eliminarán los horarios actuales del año <?= $anio_actual ?> y se generarán nuevos
                basados en las asignaciones de docentes registradas.<br>
                <strong>Estructura:</strong> 3 bloques de 2 horas por día. Motor Ultra-Rápido activado.
            </p>
            
            <button type="button" class="btn btn-principal btn-lg px-5" data-bs-toggle="modal" data-bs-target="#modalConfirmarGenerar">
                <i class="bi bi-lightning-fill me-2"></i>Generar Horarios Ahora
            </button>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmarGenerar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Acción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-calendar-x text-warning" style="font-size: 3.5rem;"></i>
                    <p class="mt-3 fs-5 fw-bold" style="color: var(--color-azul-oscuro);">¿Generar nuevos horarios?</p>
                    <p class="text-muted small px-3">
                        Esta acción borrará los horarios actuales del año <strong><?= $anio_actual ?></strong> y los volverá a calcular desde cero.
                    </p>
                </div>
                <div class="modal-footer justify-content-center border-0 mb-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" class="m-0">
                        <button type="submit" name="generar" class="btn btn-principal px-4">
                            <i class="bi bi-check-circle-fill me-1"></i> Sí, Generar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header" style="background-color: var(--color-azul-oscuro); color:white;">
            <i class="bi bi-table me-2"></i>Estado por grado — Año <?= $anio_actual ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>Grado</th>
                        <th>Estado</th>
                        <th>Clases generadas</th>
                        <th>Fecha generación</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($grados_estado as $row): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row['id_grado']) ?></td>
                        <td>
                            <?php if ($row['id_horario']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Generado</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin horario</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $row['total_clases'] ?? 0 ?> <span class="text-muted small">/ 15 esperadas</span>
                        </td>
                        <td>
                            <?= $row['fecha_generacion']
                                ? date('d/m/Y H:i', strtotime($row['fecha_generacion']))
                                : '—' ?>
                        </td>
                        <td>
                            <?php if ($row['id_horario']): ?>
                                <a href="horariosAdmin.php?grado=<?= urlencode($row['id_grado']) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>