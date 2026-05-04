-- 1. LIMPIEZA TOTAL PARA EVITAR ERRORES DE LLAVES FORÁNEAS
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM detalle_calificacion;
DELETE FROM horario_detalle;
DELETE FROM horario WHERE año_lectivo = YEAR(CURDATE());
DELETE FROM asignacion_docente WHERE año_lectivo = YEAR(CURDATE());
DELETE FROM usuario WHERE rol = 'docente';
SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- 2. CREACIÓN DE LOS 36 DOCENTES CON CORREOS ESTANDARIZADOS
-- Formato: [3 letras nombre] + [3 letras apellido] @cecc.edu.sv
-- ====================================================================

-- MATEMÁTICAS (MAT)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Carlos', 'Martínez', 'carmar@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1), -- C1 P
('Ana', 'López', 'analop@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),       -- C1 S
('Luis', 'García', 'luigar@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),      -- C2 P
('María', 'Pérez', 'marper@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),       -- C2 S
('Jorge', 'Rodríguez', 'jorrod@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),-- C3 P
('Carmen', 'Hernández', 'carher@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);-- C3 S

-- LENGUAJE Y LITERATURA (LYL)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Roberto', 'Gómez', 'robgom@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Laura', 'Díaz', 'laudia@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Miguel', 'Sánchez', 'migsan@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Sofía', 'Romero', 'sofrom@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Fernando', 'Torres', 'fertor@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Teresa', 'Flores', 'terflo@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- CIENCIAS Y VIDA (CYV)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Ricardo', 'Ramírez', 'ricram@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Patricia', 'Cruz', 'patcru@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mario', 'Reyes', 'marrey@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Silvia', 'Morales', 'silmor@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Héctor', 'Ortiz', 'hecort@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Elena', 'Gutiérrez', 'elegut@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- CIENCIA Y TECNOLOGÍA (CYT)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Francisco', 'Chávez', 'fracha@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Gloria', 'Ramos', 'gloram@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Javier', 'Mendoza', 'javmen@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rosa', 'Ruiz', 'rosrui@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Eduardo', 'Castillo', 'educas@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Blanca', 'Álvarez', 'blaalv@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- INGLÉS (ING)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Andrés', 'Aguilar', 'andagu@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Julia', 'Vásquez', 'julvas@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Oscar', 'Jiménez', 'oscjim@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Silvia', 'Méndez', 'silmen@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rafael', 'Castro', 'rafcas@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mónica', 'Guzmán', 'monguz@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);

-- EDUCACIÓN FÍSICA (EDF)
INSERT INTO `usuario` (`nombre`, `apellido`, `email`, `password_hash`, `rol`, `activo`) VALUES
('Julio', 'Vargas', 'julvar@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Andrea', 'Soto', 'andsot@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Mauricio', 'Campos', 'maucam@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Daniela', 'Ríos', 'danrio@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Rubén', 'Navarro', 'rubnav@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1),
('Verónica', 'Silva', 'versil@cecc.edu.sv', '$2y$10$yjtqYUwth9d7b/pcgKb1JeaqgjNgxr9IfykMQYaEiVe5SRArMrd8u', 'docente', 1);


-- ====================================================================
-- 3. ASIGNACIONES (Blindaje Matemático por Ciclos y Offsets)
-- ====================================================================

-- MATEMÁTICAS
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'carmar@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'analop@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'luigar@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'marper@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'jorrod@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'MAT', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'carher@cecc.edu.sv';

-- LENGUAJE Y LITERATURA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'robgom@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'laudia@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'migsan@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'sofrom@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'fertor@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'LYL', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'terflo@cecc.edu.sv';

-- CIENCIAS Y VIDA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'ricram@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'patcru@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'marrey@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'silmor@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'hecort@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYV', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'elegut@cecc.edu.sv';

-- CIENCIA Y TECNOLOGÍA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'fracha@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'gloram@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'javmen@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'rosrui@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'educas@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'CYT', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'blaalv@cecc.edu.sv';

-- INGLÉS
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'andagu@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'julvas@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'oscjim@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'silmen@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'rafcas@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'ING', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'monguz@cecc.edu.sv';

-- EDUCACIÓN FÍSICA
INSERT INTO asignacion_docente (id_usuario, id_materia, id_grado, año_lectivo)
SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '1° "A"' as g UNION SELECT '1° "B"' UNION SELECT '2° "A"') t WHERE email = 'julvar@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '2° "B"' as g UNION SELECT '3° "A"' UNION SELECT '3° "B"') t WHERE email = 'andsot@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '4° "A"' as g UNION SELECT '4° "B"' UNION SELECT '5° "A"') t WHERE email = 'maucam@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '5° "B"' as g UNION SELECT '6° "A"' UNION SELECT '6° "B"') t WHERE email = 'danrio@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '7° "A"' as g UNION SELECT '7° "B"' UNION SELECT '8° "A"') t WHERE email = 'rubnav@cecc.edu.sv'
UNION ALL SELECT id_usuario, 'EDF', g, 2026 FROM usuario, (SELECT '8° "B"' as g UNION SELECT '9° "A"' UNION SELECT '9° "B"') t WHERE email = 'versil@cecc.edu.sv';