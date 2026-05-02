-- =======================================================
-- PARCHE: MAT Primer Ciclo (Sección A y B)
-- Ejecuta esto en phpMyAdmin → pestaña SQL
-- =======================================================

-- Crear docente MAT Sección A si no existe
INSERT IGNORE INTO usuario (nombre, apellido, email, password_hash, rol, activo)
VALUES ('Carlos', 'Ramírez', 'mat.1ciclo.a@cecc.edu.sv',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i', 'docente', 1);

-- Capturar su ID
SET @id_mat1a = (SELECT id_usuario FROM usuario WHERE email = 'mat.1ciclo.a@cecc.edu.sv');

-- Asignar MAT Sección A (3 grados sin conflicto)
INSERT IGNORE INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo) VALUES
(@id_mat1a, '1° "A"', 'MAT', YEAR(CURDATE())),
(@id_mat1a, '2° "A"', 'MAT', YEAR(CURDATE())),
(@id_mat1a, '3° "A"', 'MAT', YEAR(CURDATE()));

-- Crear docente MAT Sección B si no existe
INSERT IGNORE INTO usuario (nombre, apellido, email, password_hash, rol, activo)
VALUES ('Luis', 'Herrera', 'mat.1ciclo.b@cecc.edu.sv',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i', 'docente', 1);

-- Capturar su ID
SET @id_mat1b = (SELECT id_usuario FROM usuario WHERE email = 'mat.1ciclo.b@cecc.edu.sv');

-- Asignar MAT Sección B (3 grados sin conflicto)
INSERT IGNORE INTO asignacion_docente (id_usuario, id_grado, id_materia, año_lectivo) VALUES
(@id_mat1b, '1° "B"', 'MAT', YEAR(CURDATE())),
(@id_mat1b, '2° "B"', 'MAT', YEAR(CURDATE())),
(@id_mat1b, '3° "B"', 'MAT', YEAR(CURDATE()));

-- Verificar que quedaron bien (debería mostrar 6 filas para MAT en grados 1°-3°)
SELECT u.nombre, u.apellido, a.id_grado, a.id_materia
FROM asignacion_docente a
JOIN usuario u ON a.id_usuario = u.id_usuario
WHERE a.id_materia = 'MAT'
  AND a.año_lectivo = YEAR(CURDATE())
  AND a.id_grado LIKE '%°%'
ORDER BY a.id_grado;
