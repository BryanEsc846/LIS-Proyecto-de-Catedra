-- =======================================================
-- SEED: 19 docentes + 1 admin = 20 usuarios totales
-- C.E. Candelario Cuellar
-- Contraseña de todos: password
--
-- Estructura sin conflictos para MAT:
--   MAT Primer Ciclo  → cubre 1°A,1°B,2°A,2°B,3°A  (5 grados, 0 conflictos)
--   MAT Segundo Ciclo → cubre 4°A,4°B,5°A,5°B,6°A  (5 grados, 0 conflictos)
--   MAT Tercer Ciclo  → cubre 7°A,7°B,8°A,8°B,9°A  (5 grados, 0 conflictos)
--   MAT Especial (#19)→ cubre 3°B,6°B,9°B           (3 grados, 0 conflictos)
--   LYL,CYV,CYT,ING,EDF: 1 docente por ciclo (6 grados c/u)
--
-- INSTRUCCIONES:
--   phpMyAdmin → c.e.c.c → SQL → pegar → Continuar
--   Luego ir a generarHorarios.php y generar horarios
-- =======================================================

-- Limpiar todo
DELETE FROM horario            WHERE año_lectivo = YEAR(CURDATE());
DELETE FROM asignacion_docente WHERE año_lectivo = YEAR(CURDATE());
DELETE FROM usuario            WHERE rol = 'docente';

-- =======================================================
-- PRIMER CICLO (1° – 3°)
-- =======================================================

-- MAT: solo 5 grados (sin 3°B, que lo cubre el docente #19)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Carlos','Ramírez','mat.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='mat.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','MAT',YEAR(CURDATE())),(@u,'1° "B"','MAT',YEAR(CURDATE())),
(@u,'2° "A"','MAT',YEAR(CURDATE())),(@u,'2° "B"','MAT',YEAR(CURDATE())),
(@u,'3° "A"','MAT',YEAR(CURDATE()));

-- LYL: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('María','López','lyl.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='lyl.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','LYL',YEAR(CURDATE())),(@u,'1° "B"','LYL',YEAR(CURDATE())),
(@u,'2° "A"','LYL',YEAR(CURDATE())),(@u,'2° "B"','LYL',YEAR(CURDATE())),
(@u,'3° "A"','LYL',YEAR(CURDATE())),(@u,'3° "B"','LYL',YEAR(CURDATE()));

-- CYV: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Rosa','García','cyv.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyv.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','CYV',YEAR(CURDATE())),(@u,'1° "B"','CYV',YEAR(CURDATE())),
(@u,'2° "A"','CYV',YEAR(CURDATE())),(@u,'2° "B"','CYV',YEAR(CURDATE())),
(@u,'3° "A"','CYV',YEAR(CURDATE())),(@u,'3° "B"','CYV',YEAR(CURDATE()));

-- CYT: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Pedro','Martínez','cyt.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyt.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','CYT',YEAR(CURDATE())),(@u,'1° "B"','CYT',YEAR(CURDATE())),
(@u,'2° "A"','CYT',YEAR(CURDATE())),(@u,'2° "B"','CYT',YEAR(CURDATE())),
(@u,'3° "A"','CYT',YEAR(CURDATE())),(@u,'3° "B"','CYT',YEAR(CURDATE()));

-- ING: 6 grados (2 bloques/grado × 6 = 12 sesiones ≤ 15, sin conflicto)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Laura','Castillo','ing.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='ing.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','ING',YEAR(CURDATE())),(@u,'1° "B"','ING',YEAR(CURDATE())),
(@u,'2° "A"','ING',YEAR(CURDATE())),(@u,'2° "B"','ING',YEAR(CURDATE())),
(@u,'3° "A"','ING',YEAR(CURDATE())),(@u,'3° "B"','ING',YEAR(CURDATE()));

-- EDF: 6 grados (1 bloque/grado × 6 = 6 sesiones ≤ 15, sin conflicto)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Roberto','Vásquez','edf.primerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='edf.primerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'1° "A"','EDF',YEAR(CURDATE())),(@u,'1° "B"','EDF',YEAR(CURDATE())),
(@u,'2° "A"','EDF',YEAR(CURDATE())),(@u,'2° "B"','EDF',YEAR(CURDATE())),
(@u,'3° "A"','EDF',YEAR(CURDATE())),(@u,'3° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- SEGUNDO CICLO (4° – 6°)
-- =======================================================

-- MAT: solo 5 grados (sin 6°B)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Miguel','Ángel','mat.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='mat.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','MAT',YEAR(CURDATE())),(@u,'4° "B"','MAT',YEAR(CURDATE())),
(@u,'5° "A"','MAT',YEAR(CURDATE())),(@u,'5° "B"','MAT',YEAR(CURDATE())),
(@u,'6° "A"','MAT',YEAR(CURDATE()));

-- LYL: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Carmen','Guzmán','lyl.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='lyl.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','LYL',YEAR(CURDATE())),(@u,'4° "B"','LYL',YEAR(CURDATE())),
(@u,'5° "A"','LYL',YEAR(CURDATE())),(@u,'5° "B"','LYL',YEAR(CURDATE())),
(@u,'6° "A"','LYL',YEAR(CURDATE())),(@u,'6° "B"','LYL',YEAR(CURDATE()));

-- CYV: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Silvia','Reyes','cyv.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyv.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','CYV',YEAR(CURDATE())),(@u,'4° "B"','CYV',YEAR(CURDATE())),
(@u,'5° "A"','CYV',YEAR(CURDATE())),(@u,'5° "B"','CYV',YEAR(CURDATE())),
(@u,'6° "A"','CYV',YEAR(CURDATE())),(@u,'6° "B"','CYV',YEAR(CURDATE()));

-- CYT: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Francisco','Vega','cyt.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyt.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','CYT',YEAR(CURDATE())),(@u,'4° "B"','CYT',YEAR(CURDATE())),
(@u,'5° "A"','CYT',YEAR(CURDATE())),(@u,'5° "B"','CYT',YEAR(CURDATE())),
(@u,'6° "A"','CYT',YEAR(CURDATE())),(@u,'6° "B"','CYT',YEAR(CURDATE()));

-- ING: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Daniela','Mejía','ing.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='ing.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','ING',YEAR(CURDATE())),(@u,'4° "B"','ING',YEAR(CURDATE())),
(@u,'5° "A"','ING',YEAR(CURDATE())),(@u,'5° "B"','ING',YEAR(CURDATE())),
(@u,'6° "A"','ING',YEAR(CURDATE())),(@u,'6° "B"','ING',YEAR(CURDATE()));

-- EDF: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Ernesto','Molina','edf.segundociclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='edf.segundociclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'4° "A"','EDF',YEAR(CURDATE())),(@u,'4° "B"','EDF',YEAR(CURDATE())),
(@u,'5° "A"','EDF',YEAR(CURDATE())),(@u,'5° "B"','EDF',YEAR(CURDATE())),
(@u,'6° "A"','EDF',YEAR(CURDATE())),(@u,'6° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- TERCER CICLO (7° – 9°)
-- =======================================================

-- MAT: solo 5 grados (sin 9°B)
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Arturo','Mendoza','mat.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='mat.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','MAT',YEAR(CURDATE())),(@u,'7° "B"','MAT',YEAR(CURDATE())),
(@u,'8° "A"','MAT',YEAR(CURDATE())),(@u,'8° "B"','MAT',YEAR(CURDATE())),
(@u,'9° "A"','MAT',YEAR(CURDATE()));

-- LYL: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Verónica','Salazar','lyl.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='lyl.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','LYL',YEAR(CURDATE())),(@u,'7° "B"','LYL',YEAR(CURDATE())),
(@u,'8° "A"','LYL',YEAR(CURDATE())),(@u,'8° "B"','LYL',YEAR(CURDATE())),
(@u,'9° "A"','LYL',YEAR(CURDATE())),(@u,'9° "B"','LYL',YEAR(CURDATE()));

-- CYV: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Gabriela','Orellana','cyv.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyv.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','CYV',YEAR(CURDATE())),(@u,'7° "B"','CYV',YEAR(CURDATE())),
(@u,'8° "A"','CYV',YEAR(CURDATE())),(@u,'8° "B"','CYV',YEAR(CURDATE())),
(@u,'9° "A"','CYV',YEAR(CURDATE())),(@u,'9° "B"','CYV',YEAR(CURDATE()));

-- CYT: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Rodrigo','Portillo','cyt.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='cyt.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','CYT',YEAR(CURDATE())),(@u,'7° "B"','CYT',YEAR(CURDATE())),
(@u,'8° "A"','CYT',YEAR(CURDATE())),(@u,'8° "B"','CYT',YEAR(CURDATE())),
(@u,'9° "A"','CYT',YEAR(CURDATE())),(@u,'9° "B"','CYT',YEAR(CURDATE()));

-- ING: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Tatiana','Alvarado','ing.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='ing.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','ING',YEAR(CURDATE())),(@u,'7° "B"','ING',YEAR(CURDATE())),
(@u,'8° "A"','ING',YEAR(CURDATE())),(@u,'8° "B"','ING',YEAR(CURDATE())),
(@u,'9° "A"','ING',YEAR(CURDATE())),(@u,'9° "B"','ING',YEAR(CURDATE()));

-- EDF: 6 grados
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Wilmer','Delgado','edf.tercerciclo@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='edf.tercerciclo@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'7° "A"','EDF',YEAR(CURDATE())),(@u,'7° "B"','EDF',YEAR(CURDATE())),
(@u,'8° "A"','EDF',YEAR(CURDATE())),(@u,'8° "B"','EDF',YEAR(CURDATE())),
(@u,'9° "A"','EDF',YEAR(CURDATE())),(@u,'9° "B"','EDF',YEAR(CURDATE()));

-- =======================================================
-- DOCENTE #19 — MAT ESPECIAL
-- Cubre 3°B, 6°B y 9°B (los grados que quedan sin MAT)
-- 3 grados × 3 bloques = 9 sesiones, sin conflictos ✓
-- =======================================================
INSERT IGNORE INTO usuario (nombre,apellido,email,password_hash,rol,activo) VALUES
('Josefa','Hernández','mat.especial@cecc.edu.sv',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJFKN7i6i','docente',1);
SET @u=(SELECT id_usuario FROM usuario WHERE email='mat.especial@cecc.edu.sv');
INSERT INTO asignacion_docente(id_usuario,id_grado,id_materia,año_lectivo) VALUES
(@u,'3° "B"','MAT',YEAR(CURDATE())),
(@u,'6° "B"','MAT',YEAR(CURDATE())),
(@u,'9° "B"','MAT',YEAR(CURDATE()));

-- Verificación
SELECT COUNT(*) AS total_docentes FROM usuario WHERE rol = 'docente';
