<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$mensaje_ok = "";
$mensaje_error = "";

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

// Obtener asignación del docente
$asignacion = null;
try {
    $stmt = $conexion->prepare("
        SELECT a.id_grado, a.id_materia, m.nombre_materia
        FROM asignacion_docente a
        INNER JOIN materia m ON a.id_materia = m.id_materia
        WHERE a.id_usuario = ? AND a.año_lectivo = YEAR(CURDATE())
        LIMIT 1
    ");
    $stmt->execute([$id_usuario]);
    $asignacion = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje_error = "Error al cargar asignación: " . $e->getMessage();
}

$id_materia      = $asignacion['id_materia'] ?? null;
$nombre_materia  = $asignacion['nombre_materia'] ?? '—';
$ciclo_docente   = $asignacion ? getCiclo($asignacion['id_grado'], $ciclos) : null;
$grados_del_ciclo = $ciclo_docente ? $ciclos[$ciclo_docente] : [];

// Cargar alumnos del ciclo del docente
$alumnos = [];
if (!empty($grados_del_ciclo)) {
    try {
        $placeholders = implode(',', array_fill(0, count($grados_del_ciclo), '?'));
        $stmt = $conexion->prepare("
            SELECT e.id_estudiante, e.nombre, e.apellido, m.id_matricula, m.id_grado
            FROM estudiante e
            INNER JOIN matricula m ON e.id_estudiante = m.id_estudiante
            WHERE e.activo = 1
              AND m.id_grado IN ($placeholders)
              AND m.año_lectivo = YEAR(CURDATE())
            ORDER BY m.id_grado, e.apellido, e.nombre ASC
        ");
        $stmt->execute($grados_del_ciclo);
        $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje_error = "Error al cargar alumnos: " . $e->getMessage();
    }
}

// Cargar notas existentes indexadas por id_matricula y periodo
$notas_existentes = [];
if (!empty($alumnos) && $id_materia) {
    try {
        $ids_matricula = array_column($alumnos, 'id_matricula');
        $placeholders  = implode(',', array_fill(0, count($ids_matricula), '?'));
        $params        = array_merge($ids_matricula, [$id_materia, $id_usuario]);

        $stmt = $conexion->prepare("
            SELECT * FROM detalle_calificacion
            WHERE id_matricula IN ($placeholders)
              AND id_materia = ?
              AND id_usuario = ?
        ");
        $stmt->execute($params);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $nota) {
            $notas_existentes[$nota['id_matricula']][$nota['periodo']] = $nota;
        }
    } catch (PDOException $e) {
        $mensaje_error = "Error al cargar notas: " . $e->getMessage();
    }
}

// --- GUARDAR NOTAS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_notas') {
    try {
        $notas_post = $_POST['notas'] ?? [];

        foreach ($notas_post as $id_matricula => $periodos) {
            foreach ($periodos as $periodo => $campos) {
                $act1 = floatval($campos['actividad1'] ?? 0);
                $ex1  = floatval($campos['examen1']    ?? 0);
                $act2 = floatval($campos['actividad2'] ?? 0);
                $ex2  = floatval($campos['examen2']    ?? 0);
                $act3 = floatval($campos['actividad3'] ?? 0);
                $ex3  = floatval($campos['examen3']    ?? 0);

                // Calcular nota final automáticamente
                $nota_final = round(($act1 + $ex1 + $act2 + $ex2 + $act3 + $ex3) / 6, 2);

                // INSERT o UPDATE
                $stmtCheck = $conexion->prepare("
                    SELECT id FROM detalle_calificacion
                    WHERE id_matricula = ? AND id_materia = ? AND id_usuario = ? AND periodo = ?
                ");
                $stmtCheck->execute([$id_matricula, $id_materia, $id_usuario, $periodo]);
                $existe = $stmtCheck->fetch();

                if ($existe) {
                    $conexion->prepare("
                        UPDATE detalle_calificacion
                        SET actividad1 = ?, examen1 = ?, actividad2 = ?, examen2 = ?,
                            actividad3 = ?, examen3 = ?, nota_final = ?, fecha_registro = CURDATE()
                        WHERE id_matricula = ? AND id_materia = ? AND id_usuario = ? AND periodo = ?
                    ")->execute([$act1, $ex1, $act2, $ex2, $act3, $ex3, $nota_final,
                                 $id_matricula, $id_materia, $id_usuario, $periodo]);
                } else {
                    $conexion->prepare("
                        INSERT INTO detalle_calificacion
                            (id_matricula, id_materia, id_usuario, periodo,
                             actividad1, examen1, actividad2, examen2,
                             actividad3, examen3, nota_final, fecha_registro)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
                    ")->execute([$id_matricula, $id_materia, $id_usuario, $periodo,
                                 $act1, $ex1, $act2, $ex2, $act3, $ex3, $nota_final]);
                }
            }
        }

        $mensaje_ok = "✅ Notas guardadas correctamente.";

        // Recargar notas actualizadas
        $ids_matricula = array_column($alumnos, 'id_matricula');
        $placeholders  = implode(',', array_fill(0, count($ids_matricula), '?'));
        $params        = array_merge($ids_matricula, [$id_materia, $id_usuario]);
        $stmt = $conexion->prepare("
            SELECT * FROM detalle_calificacion
            WHERE id_matricula IN ($placeholders)
              AND id_materia = ? AND id_usuario = ?
        ");
        $stmt->execute($params);
        $notas_existentes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $nota) {
            $notas_existentes[$nota['id_matricula']][$nota['periodo']] = $nota;
        }

    } catch (PDOException $e) {
        $mensaje_error = "❌ Error al guardar notas: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/horarios.css">
    <style>
        .tabla-notas th { font-size: 0.78rem; text-align: center; vertical-align: middle; }
        .tabla-notas td { vertical-align: middle; }
        .input-nota {
            width: 65px;
            text-align: center;
            font-size: 0.85rem;
            padding: 3px 5px;
        }
        .nota-final-badge {
            font-size: 0.9rem;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 8px;
        }
        .aprobado  { background-color: #d4edda; color: #155724; }
        .reprobado { background-color: #f8d7da; color: #721c24; }
        .periodo-header {
            background-color: var(--color-azul-claro);
            color: white;
            text-align: center;
            font-weight: bold;
        }
        .tab-periodo.active {
            background-color: var(--color-azul-oscuro) !important;
            color: white !important;
            border-color: var(--color-azul-oscuro) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-block small">
                <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Docente') ?>
            </span>
            <a href="../dashboard/dashboardProfesor.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-3 mt-4">
        <div class="col">
            <h2 class="fw-bold" style="color: var(--color-azul-oscuro);">
                <i class="bi bi-journal-text me-2"></i>Registro de Notas
            </h2>
            <p style="color: var(--color-azul-claro);">
                Materia: <strong><?= htmlspecialchars($nombre_materia) ?></strong>
                &nbsp;|&nbsp;
                Ciclo: <strong><?= htmlspecialchars($ciclo_docente ?? '—') ?></strong>
            </p>
        </div>
    </div>

    <?php if ($mensaje_ok): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <?= $mensaje_ok ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($mensaje_error): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <?= $mensaje_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($alumnos)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No tienes alumnos asignados o no tienes ciclo asignado.
        </div>
    <?php else: ?>

    <!-- Filtro por grado -->
    <div class="row filtro-barra shadow-sm align-items-center bg-white p-3 rounded mb-4">
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" id="buscador" class="form-control"
                   placeholder="Buscar alumno..." onkeyup="filtrarAlumnos()">
        </div>
        <div class="col-md-4 mb-2 mb-md-0">
            <select class="form-select" id="filtroGrado" onchange="filtrarAlumnos()">
                <option value="">Todos los grados del ciclo...</option>
                <?php foreach ($grados_del_ciclo as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn w-100 btn-principal" onclick="filtrarAlumnos()">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
        </div>
    </div>

    <form method="POST">
        <input type="hidden" name="accion" value="guardar_notas">

        <!-- Tabs de periodos -->
        <ul class="nav nav-tabs mb-0" id="tabsPeriodos">
            <li class="nav-item">
                <button class="nav-link tab-periodo active" type="button"
                        onclick="mostrarPeriodo(1, this)">
                    📘 Primer Periodo
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-periodo" type="button"
                        onclick="mostrarPeriodo(2, this)">
                    📗 Segundo Periodo
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-periodo" type="button"
                        onclick="mostrarPeriodo(3, this)">
                    📕 Tercer Periodo
                </button>
            </li>
        </ul>

        <?php for ($periodo = 1; $periodo <= 3; $periodo++): ?>
        <div id="periodo<?= $periodo ?>" class="periodo-content"
             style="<?= $periodo > 1 ? 'display:none;' : '' ?>">

            <div class="tabla-nomina shadow-sm rounded-bottom bg-white">
                <div class="table-responsive">
                    <table class="table tabla-notas table-hover mb-0">
                        <thead style="background-color: var(--color-azul-oscuro); color: white;">
                            <tr>
                                <th class="ps-3" style="min-width:180px;">Alumno</th>
                                <th>Grado</th>
                                <th>Actividad 1</th>
                                <th>Examen 1</th>
                                <th>Actividad 2</th>
                                <th>Examen 2</th>
                                <th>Actividad 3</th>
                                <th>Examen 3</th>
                                <th>Nota Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos as $alumno):
                                $id_mat  = $alumno['id_matricula'];
                                $n       = $notas_existentes[$id_mat][$periodo] ?? null;
                                $nf      = $n ? floatval($n['nota_final']) : 0;
                                $clase   = $nf >= 5 ? 'aprobado' : ($nf > 0 ? 'reprobado' : '');
                            ?>
                            <tr class="fila-alumno"
                                data-nombre="<?= strtolower($alumno['nombre'] . ' ' . $alumno['apellido']) ?>"
                                data-grado="<?= htmlspecialchars($alumno['id_grado']) ?>">
                                <td class="ps-3 fw-bold">
                                    <?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-grado">
                                        <?= htmlspecialchars($alumno['id_grado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][actividad1]"
                                           value="<?= $n ? $n['actividad1'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][examen1]"
                                           value="<?= $n ? $n['examen1'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][actividad2]"
                                           value="<?= $n ? $n['actividad2'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][examen2]"
                                           value="<?= $n ? $n['examen2'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][actividad3]"
                                           value="<?= $n ? $n['actividad3'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="10"
                                           class="form-control input-nota nota-input"
                                           name="notas[<?= $id_mat ?>][<?= $periodo ?>][examen3]"
                                           value="<?= $n ? $n['examen3'] : '' ?>"
                                           data-matricula="<?= $id_mat ?>"
                                           data-periodo="<?= $periodo ?>"
                                           onchange="calcularFinal(<?= $id_mat ?>, <?= $periodo ?>)">
                                </td>
                                <td>
                                    <span class="nota-final-badge <?= $clase ?>"
                                          id="final_<?= $id_mat ?>_<?= $periodo ?>">
                                        <?= $nf > 0 ? number_format($nf, 2) : '—' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endfor; ?>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-principal btn-lg px-5">
                <i class="bi bi-save-fill me-2"></i> Guardar todas las notas
            </button>
        </div>
    </form>

    <?php endif; ?>
</div>

<footer class="text-center mt-5 p-4 bg-light">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script>
// Cambiar tab de periodo
function mostrarPeriodo(num, btn) {
    document.querySelectorAll('.periodo-content').forEach(d => d.style.display = 'none');
    document.querySelectorAll('.tab-periodo').forEach(t => t.classList.remove('active'));
    document.getElementById('periodo' + num).style.display = 'block';
    btn.classList.add('active');
}

// Calcular nota final en tiempo real al cambiar inputs
function calcularFinal(idMatricula, periodo) {
    const campos = ['actividad1','examen1','actividad2','examen2','actividad3','examen3'];
    let suma = 0;
    let count = 0;

    campos.forEach(campo => {
        const inputs = document.querySelectorAll(
            `input[name="notas[${idMatricula}][${periodo}][${campo}]"]`
        );
        inputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) {
                suma += val;
                count++;
            }
        });
    });

    const badge = document.getElementById(`final_${idMatricula}_${periodo}`);
    if (badge) {
        if (count === 6) {
            const final = (suma / 6).toFixed(2);
            badge.textContent = final;
            badge.className = 'nota-final-badge ' + (parseFloat(final) >= 5 ? 'aprobado' : 'reprobado');
        } else {
            badge.textContent = '—';
            badge.className = 'nota-final-badge';
        }
    }
}

// Filtrar alumnos en la tabla visible
function filtrarAlumnos() {
    const texto = document.getElementById('buscador').value.toLowerCase();
    const grado = document.getElementById('filtroGrado').value;

    document.querySelectorAll('.fila-alumno').forEach(tr => {
        const nombre    = tr.getAttribute('data-nombre') ?? '';
        const gradoFila = tr.getAttribute('data-grado') ?? '';

        const matchNombre = nombre.includes(texto);
        const matchGrado  = grado === '' || gradoFila === grado;

        tr.style.display = (matchNombre && matchGrado) ? '' : 'none';
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>