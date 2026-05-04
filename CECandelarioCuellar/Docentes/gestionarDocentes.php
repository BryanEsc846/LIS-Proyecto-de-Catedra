<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../auth/login.php");
    exit;
}

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

$errores = [];
$exito   = null;

// -------------------------------------------------------
// POST
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Crear docente ---
    if ($action === 'crear_docente') {
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']       ?? '';
        $password2 = $_POST['password2']      ?? '';
        $ciclo_new = $_POST['ciclo_nuevo']    ?? '';
        $mat_new   = $_POST['materia_nuevo']  ?? '';

        if (!$nombre || !$apellido || !$email || !$password) {
            $errores[] = 'Nombre, apellido, correo y contraseña son obligatorios.';
        } elseif (preg_match('/[0-9]/', $nombre) || preg_match('/[0-9]/', $apellido)) {
            $errores[] = 'El nombre y apellido no pueden contener números.';
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
                $errores[] = 'Ya existe un usuario con ese correo.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $conexion->prepare(
                    "INSERT INTO usuario (nombre,apellido,email,password_hash,rol,activo)
                     VALUES (?,?,?,?,'docente',1)"
                )->execute([$nombre,$apellido,$email,$hash]);
                $new_id = $conexion->lastInsertId();

                if ($ciclo_new && $mat_new && isset($ciclos[$ciclo_new])) {
                    $stmtA = $conexion->prepare(
                        "INSERT INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo)
                         VALUES (?,?,?,YEAR(CURDATE()))"
                    );
                    foreach ($ciclos[$ciclo_new] as $g) $stmtA->execute([$new_id,$g,$mat_new]);
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado y asignado a <strong>{$ciclo_new}</strong>.";
                } else {
                    $exito = "Docente <strong>{$nombre} {$apellido}</strong> registrado. Asígnale ciclo y materia con el lápiz.";
                }
            }
        }
    }

    // --- Editar asignación ---
    if ($action === 'editar_asignacion') {
        $id_doc    = (int)($_POST['id_docente'] ?? 0);
        $ciclo_sel = $_POST['ciclo']   ?? '';
        $id_mat    = $_POST['materia'] ?? '';
        $activo    = isset($_POST['activo']) ? 1 : 0;

        if (!$id_doc) {
            $errores[] = 'Docente no válido.';
        } elseif (isset($ciclos[$ciclo_sel]) && $id_mat) {
            $conexion->prepare(
                "DELETE FROM asignacion_docente WHERE id_usuario=? AND año_lectivo=YEAR(CURDATE())"
            )->execute([$id_doc]);
            $stmt = $conexion->prepare(
                "INSERT INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo)
                 VALUES (?,?,?,YEAR(CURDATE()))"
            );
            foreach ($ciclos[$ciclo_sel] as $g) $stmt->execute([$id_doc,$g,$id_mat]);
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_doc]);
        } elseif ($ciclo_sel === '') {
            $conexion->prepare("UPDATE usuario SET activo=? WHERE id_usuario=?")->execute([$activo,$id_doc]);
        } else {
            $errores[] = 'Ciclo o materia no válidos.';
        }
    }
}

// -------------------------------------------------------
// CARGAR DATOS Y ORDENAR
// -------------------------------------------------------
$docentes_raw = $conexion->query("
    SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.activo,
           MIN(a.id_grado) AS un_grado,
           a.id_materia, m.nombre_materia,
           COUNT(a.id_grado) AS total_grados
    FROM usuario u
    LEFT JOIN asignacion_docente a ON u.id_usuario=a.id_usuario AND a.año_lectivo=YEAR(CURDATE())
    LEFT JOIN materia m ON a.id_materia=m.id_materia
    WHERE u.rol='docente'
    GROUP BY u.id_usuario,u.nombre,u.apellido,u.email,u.activo,a.id_materia,m.nombre_materia
")->fetchAll();

$materias = $conexion->query("SELECT id_materia,nombre_materia FROM materia ORDER BY nombre_materia")->fetchAll();

// Asignar el ciclo y ordenar en PHP
$docentes = [];
foreach ($docentes_raw as $doc) {
    $doc['ciclo_actual'] = $doc['un_grado'] ? getCicloDeGrado($doc['un_grado'], $ciclos) : 'Sin asignar';
    
    // Pesos para el orden (1 es el más alto)
    $orden_ciclo = [
        'Primer Ciclo'  => 1,
        'Segundo Ciclo' => 2,
        'Tercer Ciclo'  => 3,
        'Sin asignar'   => 4
    ];
    $doc['peso_ciclo'] = $orden_ciclo[$doc['ciclo_actual']] ?? 5;
    $docentes[] = $doc;
}

// Ordenamos: Primero los activos, luego por el peso del ciclo, y alfabéticamente por apellido
usort($docentes, function($a, $b) {
    if ($a['activo'] != $b['activo']) return $b['activo'] <=> $a['activo'];
    if ($a['peso_ciclo'] != $b['peso_ciclo']) return $a['peso_ciclo'] <=> $b['peso_ciclo'];
    return strcmp($a['apellido'], $b['apellido']);
});

// Cobertura por ciclo
$cobertura = [];
foreach ($ciclos as $nc => $gc) {
    $stmt = $conexion->prepare(
        "SELECT DISTINCT id_materia FROM asignacion_docente WHERE id_grado=? AND año_lectivo=YEAR(CURDATE())"
    );
    $stmt->execute([$gc[0]]);
    $cobertura[$nc] = array_column($stmt->fetchAll(),'id_materia');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gestión de Docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .tabla-nomina{background:#fff;border-radius:15px;overflow:hidden}
        .row-inactivo{opacity:.55}
        .asig-badge{font-size:.75rem;padding:3px 8px;border-radius:4px}
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
        <div class="col-md-5">
            <h2 class="fw-bold mb-0">Gestión de Docentes</h2>
            <p class="text-muted mb-0">Listado general ordenado por ciclos</p>
        </div>
        <div class="col-md-3">
            <select id="filtroCicloDocente" class="form-select shadow-sm" onchange="filtrarDocentes()">
                <option value="">Mostrar Todos los Ciclos</option>
                <option value="Primer Ciclo">Primer Ciclo (1° - 3°)</option>
                <option value="Segundo Ciclo">Segundo Ciclo (4° - 6°)</option>
                <option value="Tercer Ciclo">Tercer Ciclo (7° - 9°)</option>
                <option value="Sin asignar">Sin asignar</option>
            </select>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-principal shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo">
                <i class="bi bi-person-plus-fill me-1"></i> Registrar Nuevo Docente
            </button>
        </div>
    </div>

    <?php if (!empty($errores)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= implode('<br>', array_map('htmlspecialchars',$errores)) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($exito): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i> <?= $exito ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
    <?php foreach ($ciclos as $nc => $gc):
        $cub = $cobertura[$nc] ?? [];
        $ids = array_column($materias,'id_materia');
        $faltantes = array_diff($ids, $cub);
    ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2" style="background:var(--color-azul-oscuro);color:#fff">
                    <small class="fw-bold"><?= $nc ?></small>
                    <small class="ms-2 text-white-50"><?= implode(', ', array_filter($gc, fn($g)=>str_contains($g,'"A"'))) ?>…</small>
                </div>
                <div class="card-body py-2">
                    <?php foreach ($materias as $m):
                        $ok = in_array($m['id_materia'], $cub);
                    ?>
                    <span class="badge asig-badge me-1 mb-1"
                          style="background:<?= $ok ? '#1a7c3e' : '#c0392b' ?>;color:#fff">
                        <?= $ok ? '✓' : '✗' ?> <?= htmlspecialchars($m['id_materia']) ?>
                    </span>
                    <?php endforeach; ?>
                    <?php if (!empty($faltantes)): ?>
                    <div class="mt-1">
                        <small class="text-danger"><i class="bi bi-exclamation-circle"></i>
                            Faltan: <?= implode(', ', $faltantes) ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="tabla-nomina shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead style="background:var(--color-azul-oscuro);color:#fff">
                    <tr>
                        <th class="ps-4 text-start">Docente</th>
                        <th>Correo</th>
                        <th>Ciclo Asignado</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($docentes as $doc): ?>
                <tr class="fila-docente <?= !$doc['activo'] ? 'row-inactivo' : '' ?>" data-ciclo="<?= htmlspecialchars($doc['ciclo_actual']) ?>">
                    <td class="ps-4 text-start fw-bold">
                        <?= htmlspecialchars($doc['apellido'].', '.$doc['nombre']) ?>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($doc['email']) ?></small></td>
                    <td>
                        <?php if ($doc['ciclo_actual'] !== 'Sin asignar'): ?>
                            <span class="badge" style="background:var(--color-azul-claro);color:#fff">
                                <?= $doc['ciclo_actual'] ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $doc['nombre_materia'] ? htmlspecialchars($doc['nombre_materia']) : '<span class="text-muted">—</span>' ?></td>
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

                <div class="modal fade" id="modalEdit<?= $doc['id_usuario'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background:var(--color-azul-oscuro)">
                                <h5 class="modal-title text-white">
                                    <?= htmlspecialchars($doc['nombre'].' '.$doc['apellido']) ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action"     value="editar_asignacion">
                                <input type="hidden" name="id_docente" value="<?= $doc['id_usuario'] ?>">
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ciclo</label>
                                        <select class="form-select" name="ciclo">
                                            <option value="">— Solo cambiar estado —</option>
                                            <?php foreach ($ciclos as $nc => $gc): ?>
                                            <option value="<?= htmlspecialchars($nc) ?>"
                                                <?= ($doc['ciclo_actual']===$nc) ? 'selected' : '' ?>>
                                                <?= $nc ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Materia</label>
                                        <select class="form-select" name="materia">
                                            <option value="">— Sin materia —</option>
                                            <?php foreach ($materias as $m): ?>
                                            <option value="<?= $m['id_materia'] ?>"
                                                <?= ($doc['id_materia']===$m['id_materia']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($m['nombre_materia']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="activo" id="checkActivo<?= $doc['id_usuario'] ?>"
                                               <?= $doc['activo'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold text-success" for="checkActivo<?= $doc['id_usuario'] ?>">Docente activo en el sistema</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-2 text-end">
        <small class="text-muted">Total visible: <strong id="totalDocentes"><?= count($docentes) ?></strong> docentes</small>
    </div>

</div>

<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--color-azul-oscuro)">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Docente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="crear_docente">
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej: María" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo se permiten letras" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellido" placeholder="Ej: González" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo se permiten letras" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Correo <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="docente@cecc.edu.sv" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="pwd1" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwd1','eye1')">
                                    <i class="bi bi-eye" id="eye1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password2" id="pwd2" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwd2','eye2')">
                                    <i class="bi bi-eye" id="eye2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="fw-bold mb-2" style="color:var(--color-azul-oscuro)">
                        <i class="bi bi-book me-1"></i>Asignación
                        <span class="text-muted fw-normal small">(opcional)</span>
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Ciclo</label>
                            <select class="form-select" name="ciclo_nuevo">
                                <option value="">— Sin asignar —</option>
                                <?php foreach ($ciclos as $nc => $gc): ?>
                                <option value="<?= htmlspecialchars($nc) ?>"><?= $nc ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Materia</label>
                            <select class="form-select" name="materia_nuevo">
                                <option value="">— Sin asignar —</option>
                                <?php foreach ($materias as $m): ?>
                                <option value="<?= $m['id_materia'] ?>"><?= htmlspecialchars($m['nombre_materia']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-check-fill me-1"></i>Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <small style="color:var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar</small>
</footer>

<script>
// Toggle Password
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

// Filtrar Docentes por Ciclo
function filtrarDocentes() {
    const filtro = document.getElementById('filtroCicloDocente').value;
    const filas = document.querySelectorAll('.fila-docente');
    let visibles = 0;

    filas.forEach(fila => {
        const cicloFila = fila.getAttribute('data-ciclo');
        
        if (filtro === "" || cicloFila === filtro) {
            fila.style.display = "";
            visibles++;
        } else {
            fila.style.display = "none";
        }
    });

    document.getElementById('totalDocentes').textContent = visibles;
}

<?php if (!empty($errores) && ($_POST['action']??'')==='crear_docente'): ?>
document.addEventListener('DOMContentLoaded',()=>new bootstrap.Modal(document.getElementById('modalNuevo')).show());
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>