CREATE DATABASE IF NOT EXISTS `sigaa_web` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sigaa_web`;

-- ------------------------------------------------------
-- Tabla: estudiante
-- ------------------------------------------------------
CREATE TABLE `estudiante` (
  `id_estudiante` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `ruta_partida_nacimiento` varchar(255) DEFAULT NULL,
  `nombre_padre_madre` varchar(200) NOT NULL,
  `telefono_padre_madre` varchar(20) NOT NULL,
  `dui_padre_madre` varchar(10) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: usuario
-- ------------------------------------------------------
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('administrador','docente') NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: grado
-- ------------------------------------------------------
CREATE TABLE `grado` (
  `id_grado` varchar(10) NOT NULL,
  PRIMARY KEY (`id_grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `grado` (`id_grado`) VALUES 
('1° "A"'), ('1° "B"'), ('2° "A"'), ('2° "B"'), 
('3° "A"'), ('3° "B"'), ('4° "A"'), ('4° "B"'), 
('5° "A"'), ('5° "B"'), ('6° "A"'), ('6° "B"'), 
('7° "A"'), ('7° "B"'), ('8° "A"'), ('8° "B"'), 
('9° "A"'), ('9° "B"');

-- ------------------------------------------------------
-- Tabla: materia 
-- ------------------------------------------------------
CREATE TABLE `materia` (
  `id_materia` varchar(20) NOT NULL,
  `nombre_materia` varchar(100) NOT NULL,
  PRIMARY KEY (`id_materia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `materia` (`id_materia`, `nombre_materia`) VALUES 
('MAT', 'Matematica y Datos'),
('CYT', 'Ciencia y Tecnologia'),
('CYV', 'Ciudadania y Valores'),
('LYL', 'Lenguaje y Literatura'),
('ING', 'Ingles'),
('EDF', 'Educacion Fisica');

-- ------------------------------------------------------
-- Tabla: asignacion_docente
-- ------------------------------------------------------
CREATE TABLE `asignacion_docente` (
  `id_asignacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_materia` varchar(20) NOT NULL,
  `id_grado` varchar(10) NOT NULL,
  `año_lectivo` year NOT NULL,
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_materia`,`id_grado`,`año_lectivo`),
  CONSTRAINT `fk_asig_grado` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`id_grado`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_asig_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_asig_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: matricula
-- ------------------------------------------------------
CREATE TABLE `matricula` (
  `id_matricula` int NOT NULL AUTO_INCREMENT,
  `id_estudiante` int NOT NULL,
  `id_grado` varchar(10) NOT NULL,
  `año_lectivo` year NOT NULL,
  `estado` enum('activa','retirada','graduada') DEFAULT 'activa',
  `fecha_matricula` date NOT NULL,
  `id_usuario_registra` int NOT NULL,
  PRIMARY KEY (`id_matricula`),
  CONSTRAINT `fk_mat_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mat_grado` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`id_grado`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mat_usuario` FOREIGN KEY (`id_usuario_registra`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: horario
-- ------------------------------------------------------
CREATE TABLE `horario` (
  `id_horario` int NOT NULL AUTO_INCREMENT,
  `id_grado` varchar(10) NOT NULL,
  `año_lectivo` year NOT NULL,
  `generado_auto` tinyint(1) DEFAULT '1',
  `fecha_generacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_horario`),
  CONSTRAINT `fk_horario_grado` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`id_grado`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: horario_detalle
-- ------------------------------------------------------
CREATE TABLE `horario_detalle` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_horario` int NOT NULL,
  `id_materia` varchar(20) NOT NULL,
  `id_usuario` int NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miercoles','Jueves','Viernes') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_detalle`),
  CONSTRAINT `fk_hdet_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hdet_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_hdet_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: asistencia
-- ------------------------------------------------------
CREATE TABLE `asistencia` (
  `id_asistencia` int NOT NULL AUTO_INCREMENT,
  `id_matricula` int NOT NULL,
  `id_detalle` int NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','ausente','tardanza','justificado') NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_asistencia`),
  CONSTRAINT `fk_asis_detalle` FOREIGN KEY (`id_detalle`) REFERENCES `horario_detalle` (`id_detalle`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asis_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- Tabla: calificacion
-- ------------------------------------------------------
CREATE TABLE `calificacion` (
  `id_calificacion` int NOT NULL AUTO_INCREMENT,
  `id_matricula` int NOT NULL,
  `id_materia` varchar(20) NOT NULL,
  `id_usuario` int NOT NULL,
  `periodo` tinyint NOT NULL COMMENT '1, 2 o 3',
  `nota` decimal(4,2) NOT NULL,
  `fecha_registro` date NOT NULL,
  PRIMARY KEY (`id_calificacion`),
  CONSTRAINT `fk_cal_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_cal_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cal_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;