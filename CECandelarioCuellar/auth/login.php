<?php
// auth/login.php
session_start();
require_once '../config/conexion.php';

error_reporting(E_ALL);

$errores = [];
$email_input = ''; // Renombramos la variable para mayor claridad

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Obtener y limpiar datos
    $email_input = trim($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? '';
    
    // 2. Validaciones básicas
    if (empty($email_input)) {
        $errores[] = "El correo electrónico es requerido.";
    } elseif (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es requerida.";
    }
    
    // Si no hay errores de validación, procedemos a verificar en la BD
    if (empty($errores)) {
        try {
            // CORRECCIÓN PRINCIPAL: Buscamos por la columna 'email'
            $stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, password_hash, rol, activo FROM usuario WHERE email = :email");
            
            $stmt->execute([':email' => $email_input]);
            $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_db) {
                // Primero verificamos si la cuenta está activa
                if ($user_db['activo'] == 0) {
                    $errores[] = "Su cuenta está desactivada. Contacte al administrador.";
                } 
                // Luego verificamos la contraseña
                elseif (password_verify($password, $user_db['password_hash'])) {
                    
                    // ¡Éxito! Credenciales correctas y cuenta activa.
                    // Guardamos la información en la sesión
                    $_SESSION['id_usuario'] = $user_db['id_usuario'];
                    $_SESSION['nombre_completo'] = $user_db['nombre'] . ' ' . $user_db['apellido'];
                    $_SESSION['rol'] = $user_db['rol'];
                    $_SESSION['loggeado'] = true;

                    // Redireccionamos según el rol del usuario
                    if ($user_db['rol'] === 'administrador') {
                        header("Location: ../dashboard/dashboardAdmin.php");
                    } else {
                        header("Location: ../dashboard/dashboardProfesor.php");
                    }
                    exit; // Importante: salir después de redirigir

                } else {
                    $errores[] = "Contraseña incorrecta.";
                }
            } else {
                // Usuario no encontrado
                $errores[] = "No existe una cuenta asociada al correo '" . htmlspecialchars($email_input) . "'.";
            }

        } catch (PDOException $e) {
            // En producción, no muestres el error exacto de la BD al usuario
            $errores[] = "Error del sistema. Por favor, intente más tarde.";
            // Para depuración, puedes dejar esta línea:
            // $errores[] = "Error del sistema: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/disenos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-center py-4 rounded-top-4" style="background-color: var(--color-azul-oscuro, #0d6efd);">
                <h4 class="mb-0 text-white fw-bold"><i class="bi bi-shield-lock me-2"></i>Acceso Sistema</h4>
                <small class="text-white-50">Centro Escolar Candelario Cuellar</small>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger shadow-sm small" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="post" novalidate>
                    <!-- CAMBIO: El label y el placeholder ahora indican claramente que es un email -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="Ej: admin@gmail.com" value="<?= htmlspecialchars($email_input) ?>" required autofocus>
                        </div>
                        <small class="text-muted">Ingrese el correo asociado a su cuenta.</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="background-color: var(--color-azul-oscuro, #0d6efd); border:none;">
                        INGRESAR
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-white border-0 pb-3">
                <small class="text-muted">¿Olvidó su contraseña? Contacte a soporte.</small>
            </div>
        </div>
        <p class="text-center mt-4 text-muted small">&copy; 2026 C.E. Candelario Cuellar</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap/5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>