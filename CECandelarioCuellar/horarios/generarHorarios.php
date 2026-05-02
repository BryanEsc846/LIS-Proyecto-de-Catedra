<?php
session_start();
require_once '../config/conexion.php';

/*if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}*/

// -------------------------------------------------------
// Bloques de 2 horas clase por franja
// Distribución semanal: MAT=6h, LYL=6h, CYV=6h, CYT=6h → 3 bloques c/u
//                        ING=4h → 2 bloques, EDF=2h → 1 bloque
// 3 bloques/día × 5 días = 15 bloques = (3+3+3+3+2+1) ✓
//
// Plantillas de día (5 rotaciones, 3 materias c/u).
// Para el grado con offset k, en el día d se usa template[(d+k)%5].
// -------------------------------------------------------
$DAY_TEMPLATES = [
    0 => ['MAT','LYL','CYV'], // base Lunes
    1 => ['CYT','ING','MAT'], // base Martes
    2 => ['LYL','CYV','CYT'], // base Miércoles
    3 => ['ING','MAT','LYL'], // base Jueves
    4 => ['CYV','CYT','EDF'], // base Viernes
];

// Cada bloque ocupa 2 períodos consecutivos (2 horas)
$TIME_SLOTS = [
    ['07:10:00','08:30:00'], // Bloque 1 (7:10 – 8:30)
    ['08:50:00','10:10:00'], // Bloque 2 (8:50 – 10:10)
    ['10:30:00','11:50:00'], // Bloque 3 (10:30 – 11:50)
];

$DIAS_SEMANA = ['Lunes','Martes','Miercoles','Jueves','Viernes'];

// Offset de rotación por grado dentro del ciclo (0-4)
$GRADE_OFFSETS = [
    '1° "A"'=>0,'1° "B"'=>1,'2° "A"'=>2,'2° "B"'=>3,'3° "A"'=>4,'3° "B"'=>1,
    '4° "A"'=>0,'4° "B"'=>1,'5° "A"'=>2,'5° "B"'=>3,'6° "A"'=>4,'6° "B"'=>2,
    '7° "A"'=>0,'7° "B"'=>1,'8° "A"'=>2,'8° "B"'=>3,'9° "A"'=>4,'9° "B"'=>3,
];

// -------------------------------------------------------
// Función de generación
// -------------------------------------------------------
function generarTodosLosHorarios($conexion, $DAY_TEMPLATES, $TIME_SLOTS, $DIAS_SEMANA, $GRADE_OFFSETS) {
    $anio = date('Y');

    // Eliminar horarios existentes del año (CASCADE borra horario_detalle)
    $conexion->prepare("DELETE FROM horario WHERE año_lectivo = ?")->execute([$anio]);

    $stmtHorario  = $conexion->prepare(
        "INSERT INTO horario (id_grado, año_lectivo, generado_auto) VALUES (?, ?, 1)"
    );
    $stmtDetalle  = $conexion->prepare(
        "INSERT INTO horario_detalle (id_horario, id_materia, id_usuario, dia_semana, hora_inicio, hora_fin)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmtDocente  = $conexion->prepare(
        "SELECT id_usuario FROM asignacion_docente
         WHERE id_grado = ? AND id_materia = ? AND año_lectivo = ?
         LIMIT 1"
    );

    $errores = [];
    $exitosos = 0;

    foreach ($GRADE_OFFSETS as $id_grado => $offset) {
        $stmtHorario->execute([$id_grado, $anio]);
        $id_horario = $conexion->lastInsertId();
        $insertados  = 0;

        for ($d = 0; $d < 5; $d++) {
            $tpl_idx  = ($d + $offset) % 5;
            $materias = $DAY_TEMPLATES[$tpl_idx];

            for ($p = 0; $p < 3; $p++) {
                $id_materia = $materias[$p];

                $stmtDocente->execute([$id_grado, $id_materia, $anio]);
                $docente = $stmtDocente->fetch();

                if (!$docente) {
                    $errores[] = "Sin docente: <strong>{$id_grado}</strong> — {$id_materia}";
                    continue;
                }

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
        if ($insertados > 0) $exitosos++;
    }

    return ['exitosos' => $exitosos, 'errores' => array_unique($errores)];
}

// -------------------------------------------------------
// Procesamiento POST
// -------------------------------------------------------
$resultado = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar'])) {
    $resultado = generarTodosLosHorarios(
        $conexion, $DAY_TEMPLATES, $TIME_SLOTS, $DIAS_SEMANA, $GRADE_OFFSETS
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

// Contadores de resumen
$con_horario    = count(array_filter($grados_estado, fn($r) => $r['id_horario']));
$sin_horario    = count($grados_estado) - $con_horario;

// Docentes sin asignación
$docentes_sin_asig = $conexion->query("
    SELECT u.nombre, u.apellido
    FROM usuario u
    WHERE u.rol = 'docente' AND u.activo = 1
      AND u.id_usuario NOT IN (
          SELECT DISTINCT id_usuario FROM asignacion_docente WHERE año_lectivo = YEAR(CURDATE())
      )
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
            <p class="mb-1"><strong>Advertencias (docentes no asignados):</strong></p>
            <ul class="mb-0 small">
                <?php foreach ($resultado['errores'] as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen -->
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
        <a href="../Docentes/gestionarDocentes.php" class="alert-link ms-2">Ir a Gestión de Docentes →</a>
    </div>
    <?php endif; ?>

    <!-- Botón de generación -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center py-4">
            <h5 class="fw-bold mb-2">
                <i class="bi bi-magic me-2"></i>Generar / Regenerar todos los horarios
            </h5>
            <p class="text-muted small mb-3">
                Se eliminarán los horarios actuales del año <?= $anio_actual ?> y se generarán nuevos
                basados en las asignaciones de docentes registradas.<br>
                <strong>Estructura:</strong> 3 bloques de 2 horas por día &nbsp;|&nbsp;
                MAT, LYL, CYV, CYT → 6 h/semana &nbsp;|&nbsp; ING → 4 h/semana &nbsp;|&nbsp; EDF → 2 h/semana.
            </p>
            <form method="POST" onsubmit="return confirm('¿Confirmar generación de horarios? Se borrarán los horarios actuales del año <?= $anio_actual ?>.');">
                <button type="submit" name="generar" class="btn btn-principal btn-lg px-5">
                    <i class="bi bi-lightning-fill me-2"></i>Generar Horarios Ahora
                </button>
            </form>
        </div>
    </div>

    <!-- Estado por grado -->
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
