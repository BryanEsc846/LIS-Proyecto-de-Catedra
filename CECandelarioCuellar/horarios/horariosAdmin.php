<?php
session_start();
require_once '../config/conexion.php';

/*if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}*/

$anio_actual = date('Y');

// Colores por materia
function colorMateria($id) {
    $mapa = [
        'MAT' => ['bg'=>'#264792','text'=>'#fff','label'=>'MAT'],
        'LYL' => ['bg'=>'#1a7c3e','text'=>'#fff','label'=>'LYL'],
        'CYV' => ['bg'=>'#b87100','text'=>'#fff','label'=>'CYV'],
        'CYT' => ['bg'=>'#0d6b6b','text'=>'#fff','label'=>'CYT'],
        'ING' => ['bg'=>'#5c35a0','text'=>'#fff','label'=>'ING'],
        'EDF' => ['bg'=>'#a0191f','text'=>'#fff','label'=>'EDF'],
    ];
    return $mapa[$id] ?? ['bg'=>'#6c757d','text'=>'#fff','label'=>$id];
}

// Lista de todos los grados
$todos_grados = [
    '1° "A"','1° "B"','2° "A"','2° "B"','3° "A"','3° "B"',
    '4° "A"','4° "B"','5° "A"','5° "B"','6° "A"','6° "B"',
    '7° "A"','7° "B"','8° "A"','8° "B"','9° "A"','9° "B"',
];

$grado_sel = $_GET['grado'] ?? '1° "A"';
if (!in_array($grado_sel, $todos_grados)) {
    $grado_sel = '1° "A"';
}

// Bloques de 2 horas
$TIME_SLOTS_LABELS = [
    '07:10:00' => '7:10 – 8:30',
    '08:50:00' => '8:50 – 10:10',
    '10:30:00' => '10:30 – 11:50',
];

$DIAS = ['Lunes','Martes','Miercoles','Jueves','Viernes'];
$DIAS_LABEL = [
    'Lunes'=>'Lunes','Martes'=>'Martes','Miercoles'=>'Miércoles',
    'Jueves'=>'Jueves','Viernes'=>'Viernes'
];

// Verificar si el grado tiene horario generado
$stmt_check = $conexion->prepare("
    SELECT id_horario FROM horario
    WHERE id_grado = ? AND año_lectivo = ?
    ORDER BY fecha_generacion DESC LIMIT 1
");
$stmt_check->execute([$grado_sel, $anio_actual]);
$horario_actual = $stmt_check->fetch();
$id_horario_sel = $horario_actual['id_horario'] ?? null;

// Obtener detalle del horario del grado seleccionado
$schedule = []; // [hora_inicio][dia_semana] = {materia, docente, id_materia}
if ($id_horario_sel) {
    $stmt = $conexion->prepare("
        SELECT hd.dia_semana, hd.hora_inicio, hd.hora_fin,
               hd.id_materia, m.nombre_materia,
               CONCAT(u.nombre,' ',u.apellido) AS docente
        FROM horario_detalle hd
        JOIN materia m ON hd.id_materia = m.id_materia
        JOIN usuario u ON hd.id_usuario = u.id_usuario
        WHERE hd.id_horario = ?
        ORDER BY hd.hora_inicio,
                 FIELD(hd.dia_semana,'Lunes','Martes','Miercoles','Jueves','Viernes')
    ");
    $stmt->execute([$id_horario_sel]);
    foreach ($stmt->fetchAll() as $row) {
        $schedule[$row['hora_inicio']][$row['dia_semana']] = [
            'id_materia'    => $row['id_materia'],
            'nombre_materia'=> $row['nombre_materia'],
            'docente'       => $row['docente'],
        ];
    }
}

// Resumen de grados con/sin horario para la barra de estado
$stmt_res = $conexion->prepare("
    SELECT g.id_grado,
           CASE WHEN h.id_horario IS NOT NULL THEN 1 ELSE 0 END AS tiene_horario
    FROM grado g
    LEFT JOIN horario h ON g.id_grado = h.id_grado AND h.año_lectivo = ?
    ORDER BY g.id_grado
");
$stmt_res->execute([$anio_actual]);
$resumen_grados = $stmt_res->fetchAll(PDO::FETCH_KEY_PAIR);
// fetchAll con FETCH_KEY_PAIR devuelve [id_grado => tiene_horario]
// Redirigir si el PDO no admite FETCH_KEY_PAIR (fallback)
if (empty($resumen_grados)) {
    $stmt_res->execute([$anio_actual]);
    $filas = $stmt_res->fetchAll();
    foreach ($filas as $f) $resumen_grados[$f['id_grado']] = $f['tiene_horario'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios por Grado - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/horarios.css">
    <style>
        .celda-clase {
            min-height: 60px;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 0.8rem;
            line-height: 1.3;
        }
        .celda-clase .mat-nombre { font-weight: 700; font-size: 0.85rem; }
        .celda-clase .mat-docente { font-size: 0.72rem; opacity: 0.9; }
        .tr-receso td { background-color: #f0f4ff !important; color: #6c757d; font-style: italic; }
        .grado-btn { font-size: 0.78rem; padding: 4px 8px; margin: 2px; }
        .grado-btn.activo { color: #fff; background-color: var(--color-azul-oscuro); border-color: var(--color-azul-oscuro); }
        .grado-btn.sin-horario { opacity: 0.5; }
        .leyenda-dot { display:inline-block; width:14px; height:14px; border-radius:3px; vertical-align:middle; }
        th.dia-col { width: 16%; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 fw-bold">Centro Escolar Candelario Cuellar</span>
        <div class="d-flex gap-2">
            <a href="generarHorarios.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-lightning-fill"></i> Generar Horarios
            </a>
            <a href="../dashboard/dashboardAdmin.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Panel Admin
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">

    <div class="row mb-3 text-center">
        <div class="col">
            <h2 class="fw-bold">Horarios de Alumnos por Grado</h2>
            <p class="text-muted">Año lectivo <?= $anio_actual ?> &mdash; Selecciona un grado para ver su horario</p>
        </div>
    </div>

    <!-- Selector de grado por ciclos -->
    <div class="card shadow-sm mb-4 px-3 py-3">
        <?php
        $ciclos = [
            'Primer Ciclo (1° – 3°)' => ['1° "A"','1° "B"','2° "A"','2° "B"','3° "A"','3° "B"'],
            'Segundo Ciclo (4° – 6°)'=> ['4° "A"','4° "B"','5° "A"','5° "B"','6° "A"','6° "B"'],
            'Tercer Ciclo (7° – 9°)' => ['7° "A"','7° "B"','8° "A"','8° "B"','9° "A"','9° "B"'],
        ];
        foreach ($ciclos as $nombre_ciclo => $grados_ciclo):
        ?>
        <div class="mb-2">
            <span class="fw-bold small me-2" style="color:var(--color-azul-oscuro)"><?= $nombre_ciclo ?>:</span>
            <?php foreach ($grados_ciclo as $g):
                $tiene = ($resumen_grados[$g] ?? 0);
                $activo = ($g === $grado_sel) ? 'activo' : '';
                $sinH   = !$tiene ? 'sin-horario' : '';
            ?>
                <a href="?grado=<?= urlencode($g) ?>"
                   class="btn btn-outline-secondary grado-btn <?= $activo ?> <?= $sinH ?>"
                   title="<?= !$tiene ? 'Sin horario generado' : '' ?>">
                    <?= htmlspecialchars($g) ?>
                    <?php if (!$tiene): ?><i class="bi bi-dash-circle" title="Sin horario"></i><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabla de horario del grado seleccionado -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background-color: var(--color-azul-oscuro); color:white;">
            <span>
                <i class="bi bi-calendar3 me-2"></i>
                Horario: <strong><?= htmlspecialchars($grado_sel) ?></strong>
            </span>
            <?php if (!$id_horario_sel): ?>
                <span class="badge bg-warning text-dark">Sin horario generado</span>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
        <?php if (!$id_horario_sel): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-4 text-muted"></i>
                <p class="mt-3 text-muted">No hay horario generado para <strong><?= htmlspecialchars($grado_sel) ?></strong> en <?= $anio_actual ?>.</p>
                <a href="generarHorarios.php" class="btn btn-principal">
                    <i class="bi bi-lightning-fill me-1"></i> Generar Horarios
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead style="background-color: var(--color-azul-oscuro); color:white;">
                        <tr>
                            <th style="width:12%">Hora</th>
                            <?php foreach ($DIAS as $dia): ?>
                                <th class="dia-col"><?= $DIAS_LABEL[$dia] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $filas_horario = [
                            '07:10:00',
                            'receso1',
                            '08:50:00',
                            'receso2',
                            '10:30:00',
                        ];
                        foreach ($filas_horario as $fila):
                            if ($fila === 'receso1'):
                        ?>
                        <tr class="tr-receso">
                            <td><small>8:30 – 8:50</small></td>
                            <td colspan="5"><i class="bi bi-cup-hot me-1"></i>Receso</td>
                        </tr>
                        <?php elseif ($fila === 'receso2'): ?>
                        <tr class="tr-receso">
                            <td><small>10:10 – 10:30</small></td>
                            <td colspan="5"><i class="bi bi-cup-hot me-1"></i>Receso</td>
                        </tr>
                        <?php else:
                            $hora_label = $TIME_SLOTS_LABELS[$fila];
                        ?>
                        <tr>
                            <td class="fw-bold small"><?= $hora_label ?></td>
                            <?php foreach ($DIAS as $dia): ?>
                                <td>
                                <?php if (isset($schedule[$fila][$dia])):
                                    $cel = $schedule[$fila][$dia];
                                    $color = colorMateria($cel['id_materia']);
                                ?>
                                    <div class="celda-clase"
                                         style="background-color:<?= $color['bg'] ?>;color:<?= $color['text'] ?>">
                                        <div class="mat-nombre"><?= htmlspecialchars($cel['nombre_materia']) ?></div>
                                        <div class="mat-docente"><?= htmlspecialchars($cel['docente']) ?></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Leyenda de materias -->
            <div class="p-3 border-top bg-light">
                <small class="fw-bold me-3">Leyenda:</small>
                <?php
                $leyenda = [
                    'MAT'=>'Matemática y Datos',
                    'LYL'=>'Lenguaje y Literatura',
                    'CYV'=>'Ciudadanía y Valores',
                    'CYT'=>'Ciencia y Tecnología',
                    'ING'=>'Inglés',
                    'EDF'=>'Educación Física',
                ];
                foreach ($leyenda as $id => $nombre):
                    $c = colorMateria($id);
                ?>
                <span class="me-3 small">
                    <span class="leyenda-dot" style="background:<?= $c['bg'] ?>"></span>
                    <?= $nombre ?>
                </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>

</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
