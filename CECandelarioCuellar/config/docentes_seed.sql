-- =======================================================
-- SEED COMPLETO: 33 docentes sin conflictos de horario
-- C.E. Candelario Cuellar — Año lectivo activo
--
-- Contraseña de todos: password
-- Estructura: cada docente cubre 3 grados (Sección A o B)
-- EDF: 1 docente cubre los 6 grados de su ciclo
--
-- INSTRUCCIONES:
--   phpMyAdmin → base de datos c.e.c.c → pestaña SQL
--   Pega este script completo y presiona "Continuar"
--   Luego ve a generarHorarios.php y genera los horarios
-- =======================================================

-- Limpiar asignaciones y horarios del año actual
DELETE FROM horario          WHERE año_lectivo = YEAR(CURDATE());
DELETE FROM asignacion_docente WHERE año_lectivo = YEAR(CURDATE());

-- =======================================================
-- PRIMER CICLO  (1° – 3°)
-- Secc. A: 1°"A", 2°"A", 3°"A"
-- Secc. B: 1°"B", 2°"B", 3°"B"
-- =======================================================

-- MAT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Carlos','Ramírez','mat.1ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.1ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','MAT',YEAR(CURDATE())),(@u,'2° "A"','MAT',YEAR(CURDATE())),(@u,'3° "A"','MAT',YEAR(CURDATE()));

-- MAT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Luis','Herrera','mat.1ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.1ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "B"','MAT',YEAR(CURDATE())),(@u,'2° "B"','MAT',YEAR(CURDATE())),(@u,'3° "B"','MAT',YEAR(CURDATE()));

-- LYL Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('María','López','lyl.1ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.1ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','LYL',YEAR(CURDATE())),(@u,'2° "A"','LYL',YEAR(CURDATE())),(@u,'3° "A"','LYL',YEAR(CURDATE()));

-- LYL Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Ana','Morales','lyl.1ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.1ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "B"','LYL',YEAR(CURDATE())),(@u,'2° "B"','LYL',YEAR(CURDATE())),(@u,'3° "B"','LYL',YEAR(CURDATE()));

-- CYV Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Rosa','García','cyv.1ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.1ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','CYV',YEAR(CURDATE())),(@u,'2° "A"','CYV',YEAR(CURDATE())),(@u,'3° "A"','CYV',YEAR(CURDATE()));

-- CYV Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Elena','Flores','cyv.1ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.1ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "B"','CYV',YEAR(CURDATE())),(@u,'2° "B"','CYV',YEAR(CURDATE())),(@u,'3° "B"','CYV',YEAR(CURDATE()));

-- CYT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Pedro','Martínez','cyt.1ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.1ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','CYT',YEAR(CURDATE())),(@u,'2° "A"','CYT',YEAR(CURDATE())),(@u,'3° "A"','CYT',YEAR(CURDATE()));

-- CYT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Jorge','Ramos','cyt.1ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.1ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "B"','CYT',YEAR(CURDATE())),(@u,'2° "B"','CYT',YEAR(CURDATE())),(@u,'3° "B"','CYT',YEAR(CURDATE()));

-- ING Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Laura','Castillo','ing.1ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.1ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','ING',YEAR(CURDATE())),(@u,'2° "A"','ING',YEAR(CURDATE())),(@u,'3° "A"','ING',YEAR(CURDATE()));

-- ING Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Sandra','Torres','ing.1ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.1ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "B"','ING',YEAR(CURDATE())),(@u,'2° "B"','ING',YEAR(CURDATE())),(@u,'3° "B"','ING',YEAR(CURDATE()));

-- EDF (ciclo completo, 6 grados × 1 bloque = 6 sesiones, sin conflicto)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Roberto','Vásquez','edf.1ciclo@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='edf.1ciclo@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','EDF',YEAR(CURDATE())),(@u,'1° "B"','EDF',YEAR(CURDATE())),
(@u,'2° "A"','EDF',YEAR(CURDATE())),(@u,'2° "B"','EDF',YEAR(CURDATE())),
(@u,'3° "A"','EDF',YEAR(CURDATE())),(@u,'3° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- SEGUNDO CICLO  (4° – 6°)
-- Secc. A: 4°"A", 5°"A", 6°"A"
-- Secc. B: 4°"B", 5°"B", 6°"B"
-- =======================================================

-- MAT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Miguel','Ángel','mat.2ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.2ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','MAT',YEAR(CURDATE())),(@u,'5° "A"','MAT',YEAR(CURDATE())),(@u,'6° "A"','MAT',YEAR(CURDATE()));

-- MAT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Juan','Pérez','mat.2ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.2ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "B"','MAT',YEAR(CURDATE())),(@u,'5° "B"','MAT',YEAR(CURDATE())),(@u,'6° "B"','MAT',YEAR(CURDATE()));

-- LYL Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Carmen','Guzmán','lyl.2ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.2ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','LYL',YEAR(CURDATE())),(@u,'5° "A"','LYL',YEAR(CURDATE())),(@u,'6° "A"','LYL',YEAR(CURDATE()));

-- LYL Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Patricia','Núñez','lyl.2ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.2ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "B"','LYL',YEAR(CURDATE())),(@u,'5° "B"','LYL',YEAR(CURDATE())),(@u,'6° "B"','LYL',YEAR(CURDATE()));

-- CYV Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Silvia','Reyes','cyv.2ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.2ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','CYV',YEAR(CURDATE())),(@u,'5° "A"','CYV',YEAR(CURDATE())),(@u,'6° "A"','CYV',YEAR(CURDATE()));

-- CYV Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Alejandra','Cruz','cyv.2ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.2ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "B"','CYV',YEAR(CURDATE())),(@u,'5° "B"','CYV',YEAR(CURDATE())),(@u,'6° "B"','CYV',YEAR(CURDATE()));

-- CYT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Francisco','Vega','cyt.2ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.2ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','CYT',YEAR(CURDATE())),(@u,'5° "A"','CYT',YEAR(CURDATE())),(@u,'6° "A"','CYT',YEAR(CURDATE()));

-- CYT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Héctor','Sánchez','cyt.2ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.2ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "B"','CYT',YEAR(CURDATE())),(@u,'5° "B"','CYT',YEAR(CURDATE())),(@u,'6° "B"','CYT',YEAR(CURDATE()));

-- ING Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Daniela','Mejía','ing.2ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.2ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','ING',YEAR(CURDATE())),(@u,'5° "A"','ING',YEAR(CURDATE())),(@u,'6° "A"','ING',YEAR(CURDATE()));

-- ING Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Karla','Ruiz','ing.2ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.2ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "B"','ING',YEAR(CURDATE())),(@u,'5° "B"','ING',YEAR(CURDATE())),(@u,'6° "B"','ING',YEAR(CURDATE()));

-- EDF
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Ernesto','Molina','edf.2ciclo@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='edf.2ciclo@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','EDF',YEAR(CURDATE())),(@u,'4° "B"','EDF',YEAR(CURDATE())),
(@u,'5° "A"','EDF',YEAR(CURDATE())),(@u,'5° "B"','EDF',YEAR(CURDATE())),
(@u,'6° "A"','EDF',YEAR(CURDATE())),(@u,'6° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- TERCER CICLO  (7° – 9°)
-- Secc. A: 7°"A", 8°"A", 9°"A"
-- Secc. B: 7°"B", 8°"B", 9°"B"
-- =======================================================

-- MAT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Arturo','Mendoza','mat.3ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.3ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','MAT',YEAR(CURDATE())),(@u,'8° "A"','MAT',YEAR(CURDATE())),(@u,'9° "A"','MAT',YEAR(CURDATE()));

-- MAT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Óscar','Aguilar','mat.3ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='mat.3ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "B"','MAT',YEAR(CURDATE())),(@u,'8° "B"','MAT',YEAR(CURDATE())),(@u,'9° "B"','MAT',YEAR(CURDATE()));

-- LYL Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Verónica','Salazar','lyl.3ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.3ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','LYL',YEAR(CURDATE())),(@u,'8° "A"','LYL',YEAR(CURDATE())),(@u,'9° "A"','LYL',YEAR(CURDATE()));

-- LYL Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Mónica','Leiva','lyl.3ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='lyl.3ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "B"','LYL',YEAR(CURDATE())),(@u,'8° "B"','LYL',YEAR(CURDATE())),(@u,'9° "B"','LYL',YEAR(CURDATE()));

-- CYV Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Gabriela','Orellana','cyv.3ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.3ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','CYV',YEAR(CURDATE())),(@u,'8° "A"','CYV',YEAR(CURDATE())),(@u,'9° "A"','CYV',YEAR(CURDATE()));

-- CYV Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Blanca','Henríquez','cyv.3ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyv.3ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "B"','CYV',YEAR(CURDATE())),(@u,'8° "B"','CYV',YEAR(CURDATE())),(@u,'9° "B"','CYV',YEAR(CURDATE()));

-- CYT Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Rodrigo','Portillo','cyt.3ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.3ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','CYT',YEAR(CURDATE())),(@u,'8° "A"','CYT',YEAR(CURDATE())),(@u,'9° "A"','CYT',YEAR(CURDATE()));

-- CYT Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Fernando','Escobar','cyt.3ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='cyt.3ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "B"','CYT',YEAR(CURDATE())),(@u,'8° "B"','CYT',YEAR(CURDATE())),(@u,'9° "B"','CYT',YEAR(CURDATE()));

-- ING Sección A
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Tatiana','Alvarado','ing.3ciclo.a@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.3ciclo.a@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','ING',YEAR(CURDATE())),(@u,'8° "A"','ING',YEAR(CURDATE())),(@u,'9° "A"','ING',YEAR(CURDATE()));

-- ING Sección B
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Nadia','Bonilla','ing.3ciclo.b@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='ing.3ciclo.b@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "B"','ING',YEAR(CURDATE())),(@u,'8° "B"','ING',YEAR(CURDATE())),(@u,'9° "B"','ING',YEAR(CURDATE()));

-- EDF
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Wilmer','Delgado','edf.3ciclo@cecc.edu.sv','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u = (SELECT id_usuario FROM usuario WHERE email='edf.3ciclo@cecc.edu.sv');
INSERT IGNORE INTO asignacion_docente (id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','EDF',YEAR(CURDATE())),(@u,'7° "B"','EDF',YEAR(CURDATE())),
(@u,'8° "A"','EDF',YEAR(CURDATE())),(@u,'8° "B"','EDF',YEAR(CURDATE())),
(@u,'9° "A"','EDF',YEAR(CURDATE())),(@u,'9° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- VERIFICACIÓN: debe mostrar 6 materias por cada grado
-- =======================================================
SELECT id_grado,
       GROUP_CONCAT(id_materia ORDER BY id_materia SEPARATOR ', ') AS materias,
       COUNT(*) AS total
FROM asignacion_docente
WHERE año_lectivo = YEAR(CURDATE())
GROUP BY id_grado
ORDER BY id_grado;
