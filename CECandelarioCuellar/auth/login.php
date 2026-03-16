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
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--color-azul-oscuro);">Usuario</label>
                            <input type="text" class="form-control" name="usuario" placeholder="Usuario" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="color: var(--color-azul-oscuro);">Contraseña</label>
                            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                        </div>
                        
                        <button class="btn btn-custom w-100 mb-3 shadow-sm">Ingresar al Sistema</button>
                        
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
<!--Logica de login (simulada) momentanea -->
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); 
        window.location.href = '../dashboard/dashboardProfesor.php'; 
    });
</script>
</body>
</html>