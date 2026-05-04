<?php
// Archivo para registrar nuevos usuarios de rol docente
require_once '../config/conexion.php';

$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre && $apellido && $email && $password) {
        try {
            // Generar hash de la contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $query = "INSERT INTO usuario (nombre, apellido, email, password_hash, rol, activo) VALUES (:nombre, :apellido, :email, :password_hash, 'docente', 1)";
            $stmt = $conexion->prepare($query);

            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':email' => $email,
                ':password_hash' => $passwordHash
            ]);

            $mensaje_exito = "Usuario docente registrado exitosamente.";
        } catch (PDOException $e) {
            $mensaje_error = "Error al registrar el usuario: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $mensaje_error = "Por favor, complete todos los campos requeridos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Docente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/disenos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-center py-4 rounded-top-4" style="background-color: var(--color-azul-oscuro, #0d6efd);">
                <h4 class="mb-0 text-white fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Registrar Docente</h4>
                <small class="text-white-50">Centro Escolar Candelario Cuellar</small>
            </div>
            <div class="card-body p-4">
                
                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success shadow-sm small" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $mensaje_exito ?>
                    </div>
                <?php endif; ?>

                <?php if ($mensaje_error): ?>
                    <div class="alert alert-danger shadow-sm small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensaje_error ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre del Docente" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Apellido</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="apellido" class="form-control" placeholder="Apellido del Docente" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="docente@ejemplo.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="background-color: var(--color-azul-oscuro, #0d6efd); border:none;">
                        REGISTRAR
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none small text-muted">¿Ya tienes cuenta? Ingresa aquí.</a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white border-0 pb-3">
                <small class="text-muted">Solo usuarios con rol docente serán registrados.</small>
            </div>
        </div>
        <p class="text-center mt-4 text-muted small">&copy; 2026 C.E. Candelario Cuellar</p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>