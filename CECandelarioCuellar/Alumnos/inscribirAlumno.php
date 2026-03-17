<?php
session_start();
require_once '../config/conexion.php';

// Verificar sesión (seguridad básica)
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$mensaje = "";
$tipo_alerta = "";

// --- LÓGICA DE PROCESAMIENTO ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar datos
    $nombre_alumno  = htmlspecialchars(trim($_POST['nombre_alumno']));
    $apellido_alumno= htmlspecialchars(trim($_POST['apellido_alumno']));
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $nombre_padre   = htmlspecialchars(trim($_POST['nombre_padre']));
    $grado_seccion  = $_POST['grado_seccion']; 
    $dui_padre      = trim($_POST['dui_padre']);
    $tel_padre      = trim($_POST['tel_padre']);
    $archivo        = $_FILES['partida_archivo'];

    $errores = [];

    // Validaciones Existentes (Mantenidas)
    if (!preg_match('/^\d{8}-\d{1}$/', $dui_padre)) {
        $errores[] = "El formato del DUI es incorrecto (00000000-0).";
    }

    if (!preg_match('/^\d{4}-\d{4}$/', $tel_padre)) {
        $errores[] = "El formato del Teléfono es incorrecto (0000-0000).";
    }
    
    if (empty($nombre_alumno) || empty($apellido_alumno)) {
        $errores[] = "El nombre y apellido del alumno son obligatorios.";
    }

    if (empty($fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento es obligatoria.";
    }

    if (empty($nombre_padre)) {
        $errores[] = "El nombre del padre/madre/tutor es obligatorio.";
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debe subir la partida de nacimiento escaneada.";
    } else {
        // Validar extensión del archivo
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $errores[] = "El archivo debe ser PDF, JPG o PNG.";
        }
    }

    // --- RESULTADO FINAL ---
    if (empty($errores)) {
        // Mover Archivo
        $directorio = '../uploads/partidas_nacimiento/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        $nombre_archivo = 'PART_' . str_replace('-', '', $dui_padre) . '_' . time() . '.' . $ext;
        $ruta_destino = $directorio . $nombre_archivo;
        $ruta_bd = 'uploads/partidas_nacimiento/' . $nombre_archivo; // Ruta relativa para guardar en DB

        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            
            // Llamar al Procedimiento Almacenado
            try {
                $sql = "CALL sp_insertar_estudiante(
                    :nombre, :apellido, :fecha, :ruta, 
                    :padre, :telefono, :dui, 
                    @p_id_out, @p_msg_out
                )";
                
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':nombre'   => $nombre_alumno,
                    ':apellido' => $apellido_alumno,
                    ':fecha'    => $fecha_nacimiento,
                    ':ruta'     => $ruta_bd,
                    ':padre'    => $nombre_padre,
                    ':telefono' => $tel_padre,
                    ':dui'      => $dui_padre
                ]);

                // Obtener resultados de las variables OUT
                $res = $conexion->query("SELECT @p_id_out AS id, @p_msg_out AS mensaje")->fetch(PDO::FETCH_ASSOC);
                
                // Limpiar cursores (necesario en MySQL con procedimientos)
                $stmt->closeCursor(); 

                if ($res['id'] > 0) {
                    $mensaje = "✅ " . $res['mensaje'];
                    $tipo_alerta = "success";

                } else {
                    $errores[] = "Error BD: " . $res['mensaje'];
                }

            } catch (PDOException $e) {
                $errores[] = "Error crítico del sistema: " . $e->getMessage();
            }

        } else {
            $errores[] = "No se pudo guardar el archivo en el servidor.";
        }
    }

    if (!empty($errores)) {
        $mensaje = "❌ " . implode("<br>", $errores);
        $tipo_alerta = "danger";
    }
}

// Cargar grados desde la BD para el select
$grados_db = [];
try {
    $stmt_g = $conexion->query("SELECT id_grado FROM grado ORDER BY id_grado");
    $grados_db = $stmt_g->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    // Fallback si falla la carga
    $grados_db = ["1° \"A\"", "1° \"B\"", "2° \"A\""]; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrícula - C.E. Candelario Cuellar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/general.css">

</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">
            Centro Escolar Candelario Cuellar
        </span>
        <div class="d-flex align-items-center">
             <span class="text-white me-3 d-none d-md-block small">
                <?= htmlspecialchars($_SESSION['usuario'] ?? 'Admin') ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9 mt-4">

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card card-custom p-4 shadow-sm">
                <h2 class="text-center mb-4" style="color: var(--color-azul-oscuro, #0d47a1);">
                    <i class="bi bi-person-plus-fill me-2"></i>Ficha de Matrícula
                </h2>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <h5 class="text-primary border-bottom pb-2 mb-3">Datos del Estudiante</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombres</label>
                            <input type="text" name="nombre_alumno" class="form-control" placeholder="Nombres" required value="<?= $_POST['nombre_alumno'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Apellidos</label>
                            <input type="text" name="apellido_alumno" class="form-control" placeholder="Apellidos" required value="<?= $_POST['apellido_alumno'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required value="<?= $_POST['fecha_nacimiento'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Grado a Cursar</label>
                            <select name="grado_seccion" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($grados_db as $g): ?>
                                    <option value="<?= htmlspecialchars($g) ?>" <?= (($_POST['grado_seccion'] ?? '') == $g) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Partida (PDF/Img)</label>
                            <input type="file" name="partida_archivo" class="form-control" accept=".pdf,.jpg,.png" required>
                            <div class="form-text small">Max 5MB.</div>
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Datos del Padre/Madre/Tutor</h5>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nombre Completo del Tutor</label>
                            <input type="text" name="nombre_padre" class="form-control" placeholder="Nombres y Apellidos del encargado" required value="<?= $_POST['nombre_padre'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DUI del Encargado</label>
                            <input type="text" name="dui_padre" id="dui_padre" class="form-control" 
                                   placeholder="00000000-0" maxlength="10" required value="<?= $_POST['dui_padre'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono de Contacto</label>
                            <input type="text" name="tel_padre" id="tel_padre" class="form-control" 
                                   placeholder="0000-0000" maxlength="9" required value="<?= $_POST['tel_padre'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-4">
                        <!-- Botón Regresar -->
                        <a href="../dashboard/dashboardAdmin.php" class="btn btn-outline-secondary btn-lg px-4 py-2 w-100 w-sm-auto text-center">
                            <i class="bi bi-arrow-left me-2"></i>Regresar
                        </a>

                        <!-- Botón Finalizar -->
                        <button type="submit" class="btn btn-candelario btn-lg shadow-sm px-4 py-2 w-100 w-sm-auto">
                            <i class="bi bi-check-circle-fill me-2"></i>Finalizar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('dui_padre').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) {
            value = value.substring(0, 8) + '-' + value.substring(8, 9);
        }
        e.target.value = value;
    });

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