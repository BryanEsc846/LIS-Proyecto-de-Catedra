-- 1. LIMPIEZA TOTAL PARA EVITAR ERRORES DE LLAVES FORÁNEAS
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM detalle_calificacion;
DELETE FROM asistencia;
DELETE FROM horario_detalle;
DELETE FROM horario WHERE año_lectivo = 2026;
DELETE FROM asignacion_docente WHERE año_lectivo = 2026;
DELETE FROM usuario WHERE rol = 'docente';
SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- 2. CREACIÓN DE LOS 36 DOCENTES CON NOMBRES REALES
-- ====================================================================

-- MATEMÁTICAS (MAT)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Carlos', 'Martínez', 'cmartinez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1), -- C1 P
('Ana', 'López', 'alopez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),       -- C1 S
('Luis', 'García', 'lgarcia@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),      -- C2 P
('María', 'Pérez', 'mperez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),       -- C2 S
('Jorge', 'Rodríguez', 'jrodriguez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),-- C3 P
('Carmen', 'Hernández', 'chernandez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);-- C3 S

-- LENGUAJE Y LITERATURA (LYL)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Roberto', 'Gómez', 'rgomez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Laura', 'Díaz', 'ldiaz@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Miguel', 'Sánchez', 'msanchez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Sofía', 'Romero', 'sromero@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Fernando', 'Torres', 'ftorres@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Teresa', 'Flores', 'tflores@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- CIENCIAS Y VIDA (CYV)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Ricardo', 'Ramírez', 'rramirez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Patricia', 'Cruz', 'pcruz@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mario', 'Reyes', 'mreyes@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Silvia', 'Morales', 'smorales@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Héctor', 'Ortiz', 'hortiz@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Elena', 'Gutiérrez', 'egutierrez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- CIENCIA Y TECNOLOGÍA (CYT)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Francisco', 'Chávez', 'fchavez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Gloria', 'Ramos', 'gramos@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Javier', 'Mendoza', 'jmendoza@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rosa', 'Ruiz', 'rruiz@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Eduardo', 'Castillo', 'ecastillo@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Blanca', 'Álvarez', 'balvarez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- INGLÉS (ING)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Andrés', 'Aguilar', 'aaguilar@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Julia', 'Vásquez', 'jvasquez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Oscar', 'Jiménez', 'ojimenez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Silvia', 'Méndez', 'smendez@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rafael', 'Castro', 'rcastro@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mónica', 'Guzmán', 'mguzman@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- EDUCACIÓN FÍSICA (EDF)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Julio', 'Vargas', 'jvargas@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Andrea', 'Soto', 'asoto@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mauricio', 'Campos', 'mcampos@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Daniela', 'Ríos', 'drios@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rubén', 'Navarro', 'rnavarro@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Verónica', 'Silva', 'vsilva@escuela.edu', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);


-- ====================================================================
-- 3. ASIGNACIONES (Blindaje Matemático por Ciclos y Offsets)
-- ====================================================================

-- MATEMÁTICAS
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'cmartinez@escuela.edu'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'alopez@escuela.edu'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'lgarcia@escuela.edu'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'mperez@escuela.edu'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'jrodriguez@escuela.edu'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'chernandez@escuela.edu';

-- LENGUAJE Y LITERATURA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'rgomez@escuela.edu'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'ldiaz@escuela.edu'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'msanchez@escuela.edu'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'sromero@escuela.edu'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'ftorres@escuela.edu'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'tflores@escuela.edu';

-- CIENCIAS Y VIDA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'rramirez@escuela.edu'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'pcruz@escuela.edu'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'mreyes@escuela.edu'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'smorales@escuela.edu'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'hortiz@escuela.edu'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'egutierrez@escuela.edu';

-- CIENCIA Y TECNOLOGÍA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'fchavez@escuela.edu'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'gramos@escuela.edu'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'jmendoza@escuela.edu'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'rruiz@escuela.edu'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'ecastillo@escuela.edu'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'balvarez@escuela.edu';

-- INGLÉS
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'aaguilar@escuela.edu'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'jvasquez@escuela.edu'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'ojimenez@escuela.edu'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'smendez@escuela.edu'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'rcastro@escuela.edu'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'mguzman@escuela.edu';

-- EDUCACIÓN FÍSICA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'jvargas@escuela.edu'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'asoto@escuela.edu'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'mcampos@escuela.edu'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'drios@escuela.edu'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'rnavarro@escuela.edu'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'vsilva@escuela.edu';