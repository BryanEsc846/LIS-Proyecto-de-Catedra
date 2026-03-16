<?php
// --- LÓGICA DE PROCESAMIENTO ---
$mensaje = "";
$tipo_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_alumno  = htmlspecialchars(trim($_POST['nombre_alumno']));
    $grado_seccion  = $_POST['grado_seccion'];
    $dui_padre      = trim($_POST['dui_padre']);
    $tel_padre      = trim($_POST['tel_padre']);
    $archivo        = $_FILES['partida_archivo'];

    $errores = [];

    // 1. Validar DUI (8 dígitos - 1 dígito)
    if (!preg_match('/^\d{8}-\d{1}$/', $dui_padre)) {
        $errores[] = "El formato del DUI es incorrecto (00000000-0).";
    }

    // 2. Validar Teléfono (4 dígitos - 4 dígitos)
    if (!preg_match('/^\d{4}-\d{4}$/', $tel_padre)) {
        $errores[] = "El formato del Teléfono es incorrecto (0000-0000).";
    }

    // 3. Validar Archivo y Nombre
    if (empty($nombre_alumno)) {
        $errores[] = "El nombre del alumno es obligatorio.";
    }
    
    if ($archivo['error'] !== 0) {
        $errores[] = "Debe subir la partida de nacimiento escaneada.";
    }

    // --- RESULTADO FINAL ---
    if (empty($errores)) {
        // AQUÍ: Lógica para mover archivo y guardar en Base de Datos
        $mensaje = "✅ ¡Registro exitoso! El alumno <strong>$nombre_alumno</strong> ha sido matriculado en <strong>$grado_seccion</strong>.";
        $tipo_alerta = "success";
    } else {
        $mensaje = "❌ " . implode("<br>", $errores);
        $tipo_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrícula - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <a href="../auth/login.php" class="btn btn-outline-light btn-sm">Salir</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-4">

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card card-custom p-4">
                <h2 class="text-center mb-4" style="color: var(--color-azul-oscuro);">Ficha de Matrícula</h2>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nombre Completo del Alumno</label>
                            <input type="text" name="nombre_alumno" class="form-control" placeholder="Nombres y Apellidos" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Grado y Sección</label>
                            <select name="grado_seccion" class="form-select" required>
                               
                                <?php 
                                    $grados = ["1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°", "9°"];
                                    foreach($grados as $g) {
                                        echo "<option value='$g A'>$g Grado - Sección A</option>";
                                        echo "<option value='$g B'>$g Grado - Sección B</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Partida (PDF/Imagen)</label>
                            <input type="file" name="partida_archivo" class="form-control" accept=".pdf,.jpg,.png" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DUI del Padre/Encargado</label>
                            <input type="text" name="dui_padre" id="dui_padre" class="form-control" 
                                   placeholder="00000000-0" maxlength="10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono de Contacto</label>
                            <input type="text" name="tel_padre" id="tel_padre" class="form-control" 
                                   placeholder="0000-0000" maxlength="9" required>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-4">
                         <!-- Botón principal: Finalizar -->
                        <button type="submit" class="btn btn-candelario btn-lg shadow-sm px-4 py-2 w-100 w-sm-auto">
                         <i class="bi bi-check-circle-fill me-2"></i>Finalizar Registro de Matrícula
                    </button>
    
    <!-- Botón secundario: Regresar -->
    <a href="../dashboard/dashboardProfesor.php" class="btn btn-outline-secondary btn-lg px-4 py-2 w-100 w-sm-auto text-center">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Máscara para DUI: 00000000-0
    document.getElementById('dui_padre').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) {
            value = value.substring(0, 8) + '-' + value.substring(8, 9);
        }
        e.target.value = value;
    });

    // Máscara para Teléfono: 0000-0000
    document.getElementById('tel_padre').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 4) {
            value = value.substring(0, 4) + '-' + value.substring(4, 8);
        }
        e.target.value = value;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>