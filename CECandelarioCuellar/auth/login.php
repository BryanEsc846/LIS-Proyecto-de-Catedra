<?php
session_start();

// Configuración de errores (en producción desactivar display_errors)
error_reporting(E_ALL);
// ini_set('display_errors', 0);

$errores = [];
$usuario = '';

// Procesar formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitizar y validar entrada
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones básicas
    if (empty($usuario)) {
        $errores[] = "El usuario es requerido";
    } elseif (strlen($usuario) < 3) {
        $errores[] = "El usuario debe tener al menos 3 caracteres";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es requerida";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    
    if (empty($errores)) {
        
        // Credenciales de prueba: usuario: admin / contraseña: admin123
        if ($usuario === 'admin' && $password === 'admin123') {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['loggeado'] = true;
            header("Location: ../dashboard/dashboardProfesor.php");
            exit;
        } else {
            $errores[] = "Credenciales incorrectas";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/disenos.css">
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    INICIAR SESIÓN
                </div>
                <div class="card-body p-4">
                    
                    <!-- Mostrar errores de validación -->
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 small">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--color-azul-oscuro);">Usuario</label>
                            <input 
                                type="text" 
                                class="form-control <?= !empty($errores) && empty($_POST['usuario']) ? 'is-invalid' : '' ?>" 
                                name="usuario" 
                                placeholder="Usuario" 
                                value="<?= htmlspecialchars($usuario) ?>"
                                required
                                autocomplete="username"
                            >
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="color: var(--color-azul-oscuro);">Contraseña</label>
                            <input 
                                type="password" 
                                class="form-control <?= !empty($errores) && empty($_POST['password']) ? 'is-invalid' : '' ?>" 
                                name="password" 
                                placeholder="••••••••" 
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-custom w-100 mb-3 shadow-sm">Ingresar al Sistema</button>
                        
                        <div class="text-center">
                            <a href="#" class="text-secondary-custom">¿Problemas para entrar?</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4" style="color: var(--color-azul-claro); font-size: 0.8rem;">
                &copy; 2026 C.E. Candelario Cuellar - Todos los derechos reservados.<span style="color: var(--color-verde); font-weight: bold;"></span>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>