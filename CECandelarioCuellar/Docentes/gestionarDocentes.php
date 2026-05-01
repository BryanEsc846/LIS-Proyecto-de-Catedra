<?php
require_once '../config/conexion.php';

// Verificar si se envió el formulario para actualizar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_docente = $_POST['id_docente'];
    $grado = $_POST['grado'];
    $materia = $_POST['materia'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Actualizar la asignación del docente
    $query = "UPDATE asignacion_docente SET id_grado = ?, id_materia = ? WHERE id_usuario = ? AND año_lectivo = YEAR(CURDATE())";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$grado, $materia, $id_docente]);

    // Actualizar el estado del docente
    $query = "UPDATE usuario SET activo = ? WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$activo, $id_docente]);
}

// Obtener todos los docentes y sus asignaciones
$query = "
    SELECT u.id_usuario, u.nombre, u.activo, a.id_grado AS grado, m.nombre_materia AS materia
    FROM usuario u
    LEFT JOIN asignacion_docente a ON u.id_usuario = a.id_usuario AND a.año_lectivo = YEAR(CURDATE())
    LEFT JOIN materia m ON a.id_materia = m.id_materia
    WHERE u.rol = 'docente'
";
$stmt = $conexion->query($query);
$docentes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <a href="../dashboard/dashboardAdmin.php" class="btn btn-outline-light btn-sm">Volver</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4 text-center">
        <div class="col">
            <h2 class="fw-bold">Gestión de Docentes</h2>
            <p class="text-muted">Asigna grados, materias y estado a los docentes</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Materia</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                <tr>
                    <td><?= htmlspecialchars($docente['nombre']) ?></td>
                    <td><?= htmlspecialchars($docente['grado']) ?></td>
                    <td><?= htmlspecialchars($docente['materia']) ?></td>
                    <td><?= $docente['activo'] ? 'Sí' : 'No' ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarDocente<?= $docente['id_usuario'] ?>">Editar</button>
                    </td>
                </tr>

                <!-- Modal para editar docente -->
                <div class="modal fade" id="editarDocente<?= $docente['id_usuario'] ?>" tabindex="-1" aria-labelledby="editarDocenteLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarDocenteLabel">Editar Docente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="id_docente" value="<?= $docente['id_usuario'] ?>">
                                    <div class="mb-3">
                                        <label for="grado" class="form-label">Grado</label>
                                        <input type="text" class="form-control" name="grado" value="<?= htmlspecialchars($docente['grado']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="materia" class="form-label">Materia</label>
                                        <input type="text" class="form-control" name="materia" value="<?= htmlspecialchars($docente['materia']) ?>" required>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo<?= $docente['id_usuario'] ?>" <?= $docente['activo'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="activo<?= $docente['id_usuario'] ?>">
                                            Activo
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
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

<footer class="text-center mt-5 p-4">
    <small style="color: var(--color-azul-claro);">© 2026 C.E. Candelario Cuellar - Todos los derechos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>