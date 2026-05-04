<?php
session_start();
// Validar que exista sesión y que sea docente
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'docente') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/conexion.php';

$id_usuario  = $_SESSION['id_usuario'] ?? null;
$anio_actual = date('Y');

// Info del docente
$docente_info = null;
if ($id_usuario) {
    $stmt = $conexion->prepare("SELECT nombre, apellido FROM usuario WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $docente_info = $stmt->fetch();
}

// Materia y grados asignados al docente
$asignacion = null;
if ($id_usuario) {
    $stmt = $conexion->prepare("
        SELECT m.id_materia, m.nombre_materia,
               GROUP_CONCAT(a.id_grado ORDER BY a.id_grado SEPARATOR ', ') AS grados_asignados
        FROM asignacion_docente a
        JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_usuario = ? AND a.año_lectivo = ?
        GROUP BY m.id_materia, m.nombre_materia
        LIMIT 1
    ");
    $stmt->execute([$id_usuario, $anio_actual]);
    $asignacion = $stmt->fetch();
}

// Horario personal: cada franja muestra qué grado(s) tiene el docente
// [hora_inicio][dia_semana] => [ grados[] ]
$schedule    = [];
$tiene_horario = false;

if ($id_usuario) {
    $stmt = $conexion->prepare("
        SELECT hd.dia_semana,
               hd.hora_inicio,
               hd.hora_fin,
               hd.id_materia,
               m.nombre_materia,
               h.id_grado
        FROM horario_detalle hd
        JOIN horario h  ON hd.id_horario = h.id_horario
        JOIN materia m  ON hd.id_materia  = m.id_materia
        WHERE hd.id_usuario = ?
          AND h.año_lectivo  = ?
        ORDER BY hd.hora_inicio,
                 FIELD(hd.dia_semana,'Lunes','Martes','Miercoles','Jueves','Viernes')
    ");
    $stmt->execute([$id_usuario, $anio_actual]);

    foreach ($stmt->fetchAll() as $row) {
        $h = $row['hora_inicio'];
        $d = $row['dia_semana'];
        $schedule[$h][$d][] = $row['id_grado'];
        $tiene_horario = true;
    }
}

// Bloques de 2 horas
$TIME_SLOTS = [
    '07:10:00' => '7:10 – 8:30',
    '08:50:00' => '8:50 – 10:10',
    '10:30:00' => '10:30 – 11:50',
];

$DIAS = ['Lunes','Martes','Miercoles','Jueves','Viernes'];
$DIAS_LABEL = [
    'Lunes'=>'Lunes','Martes'=>'Martes','Miercoles'=>'Miércoles',
    'Jueves'=>'Jueves','Viernes'=>'Viernes'
];

// Color por ciclo del grado
function colorGrado($id_grado) {
    if (preg_match('/^([1-3])°/', $id_grado))   return ['bg'=>'#1a7c3e','text'=>'#fff']; // verde
    if (preg_match('/^([4-6])°/', $id_grado))   return ['bg'=>'#264792','text'=>'#fff']; // azul
    if (preg_match('/^([7-9])°/', $id_grado))   return ['bg'=>'#7a1800','text'=>'#fff']; // rojo
    return ['bg'=>'#6c757d','text'=>'#fff'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Horario - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/horarios.css">
    <style>
        .celda-docente {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-height: 55px;
            justify-content: center;
            align-items: center;
        }
        .badge-grado {
            font-size: 0.82rem;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .tr-receso td {
            background-color: #f0f4ff !important;
            color: #6c757d;
            font-style: italic;
        }
        .info-card {
            border-left: 5px solid var(--color-verde);
            background: white;
            border-radius: 10px;
            padding: 18px 24px;
        }
        .leyenda-dot { display:inline-block; width:14px; height:14px; border-radius:3px; vertical-align:middle; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">Centro Escolar Candelario Cuellar</span>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white d-none d-md-inline small">
                <?= htmlspecialchars($docente_info['nombre'].' '.$docente_info['apellido'] ?? 'Docente') ?>
            </span>
            <a href="../dashboard/dashboardProfesor.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <div class="row mb-4 text-center">
        <div class="col">
            <h2 class="fw-bold">Mi Horario Personal</h2>
            <p class="text-muted">Año lectivo <?= $anio_actual ?></p>
        </div>
    </div>

    <?php if (!$id_usuario): ?>
    <!-- Sin sesión activa -->
    <div class="alert alert-warning text-center">
        <i class="bi bi-person-lock fs-3 d-block mb-2"></i>
        No hay sesión activa. Por favor <a href="../auth/login.php">inicia sesión</a>.
    </div>

    <?php elseif (!$asignacion): ?>
    <!-- Docente sin asignación -->
    <div class="alert alert-info text-center">
        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
        <strong>Sin asignación registrada.</strong><br>
        Aún no tienes ciclo ni materia asignada para <?= $anio_actual ?>.
        Contacta al administrador.
    </div>

    <?php elseif (!$tiene_horario): ?>
    <!-- Asignado pero sin horario generado -->
    <div class="info-card mb-4 shadow-sm">
        <p class="mb-1">
            <i class="bi bi-book me-1"></i>
            <strong>Materia asignada:</strong> <?= htmlspecialchars($asignacion['nombre_materia']) ?>
        </p>
        <p class="mb-0">
            <i class="bi bi-grid-1x2 me-1"></i>
            <strong>Grados:</strong> <?= htmlspecialchars($asignacion['grados_asignados']) ?>
        </p>
    </div>
    <div class="alert alert-warning text-center">
        <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
        El horario aún no ha sido generado por el administrador.
    </div>

    <?php else: ?>
    <!-- Horario disponible -->

    <!-- Info resumen -->
    <div class="info-card mb-4 shadow-sm">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1">
                    <i class="bi bi-person-badge me-1"></i>
                    <strong>Docente:</strong>
                    <?= htmlspecialchars($docente_info['nombre'].' '.$docente_info['apellido']) ?>
                </p>
                <p class="mb-0">
                    <i class="bi bi-book me-1"></i>
                    <strong>Materia:</strong> <?= htmlspecialchars($asignacion['nombre_materia']) ?>
                </p>
            </div>
            <div class="col-md-6">
                <p class="mb-1">
                    <i class="bi bi-grid-1x2 me-1"></i>
                    <strong>Grados a cargo:</strong><br>
                    <small class="text-muted"><?= htmlspecialchars($asignacion['grados_asignados']) ?></small>
                </p>
            </div>
        </div>
    </div>

    <!-- Tabla de horario personal -->
    <div class="card shadow-sm mb-4">
        <div class="card-header" style="background-color: var(--color-azul-oscuro); color:white;">
            <i class="bi bi-calendar-week me-2"></i>
            Distribución semanal &mdash; cada celda indica el grado que se atiende en esa franja
        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead style="background-color: var(--color-azul-oscuro); color:white;">
                    <tr>
                        <th style="width:12%">Hora</th>
                        <?php foreach ($DIAS as $dia): ?>
                            <th><?= $DIAS_LABEL[$dia] ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                $filas = [
                    '07:10:00',
                    'receso1',
                    '08:50:00',
                    'receso2',
                    '10:30:00',
                ];
                foreach ($filas as $fila):
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
                <?php else: ?>
                    <tr>
                        <td class="fw-bold small"><?= $TIME_SLOTS[$fila] ?></td>
                        <?php foreach ($DIAS as $dia): ?>
                            <td>
                            <?php if (!empty($schedule[$fila][$dia])): ?>
                                <div class="celda-docente">
                                <?php foreach ($schedule[$fila][$dia] as $grado):
                                    $c = colorGrado($grado);
                                ?>
                                    <span class="badge-grado"
                                          style="background-color:<?= $c['bg'] ?>;color:<?= $c['text'] ?>">
                                        <?= htmlspecialchars($grado) ?>
                                    </span>
                                <?php endforeach; ?>
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

        <!-- Leyenda por ciclo -->
        <div class="p-3 border-top bg-light">
            <small class="fw-bold me-3">Ciclos:</small>
            <span class="me-3 small">
                <span class="leyenda-dot" style="background:#1a7c3e"></span> Primer Ciclo (1° – 3°)
            </span>
            <span class="me-3 small">
                <span class="leyenda-dot" style="background:#264792"></span> Segundo Ciclo (4° – 6°)
            </span>
            <span class="me-3 small">
                <span class="leyenda-dot" style="background:#7a1800"></span> Tercer Ciclo (7° – 9°)
            </span>
        </div>
    </div>

    <!-- Conteo de horas por día -->
    <div class="card shadow-sm">
        <div class="card-header small fw-bold" style="background-color: var(--color-azul-oscuro); color:white;">
            <i class="bi bi-clock me-2"></i>Resumen de clases por día
        </div>
        <div class="card-body py-3">
            <div class="row text-center">
            <?php
            $dias_label_arr = ['Lunes'=>'Lunes','Martes'=>'Martes','Miercoles'=>'Miércoles','Jueves'=>'Jueves','Viernes'=>'Viernes'];
            foreach ($DIAS as $dia):
                $total_dia = 0;
                foreach (array_keys($TIME_SLOTS) as $hora) {
                    if (!empty($schedule[$hora][$dia])) {
                        $total_dia += count($schedule[$hora][$dia]);
                    }
                }
            ?>
                <div class="col">
                    <div class="fw-bold" style="color:var(--color-azul-oscuro)"><?= $dias_label_arr[$dia] ?></div>
                    <div class="fs-4"><?= $total_dia ?></div>
                    <small class="text-muted"><?= $total_dia === 1 ? 'clase' : 'clases' ?></small>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
