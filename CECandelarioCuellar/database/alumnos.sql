-- ====================================================================
-- 1. INSERTAR 90 ESTUDIANTES DE PRUEBA (5 POR GRADO)
-- ====================================================================
INSERT INTO `estudiante` (`nombre`, `apellido`, `fecha_nacimiento`, `nombre_padre_madre`, `telefono_padre_madre`, `dui_padre_madre`, `activo`) VALUES
-- 1° "A" (IDs 1-5)
('Ana', 'López', '2019-03-15', 'Marta López', '7777-0001', '01234567-1', 1),
('Carlos', 'Martínez', '2019-05-20', 'José Martínez', '7777-0002', '01234567-2', 1),
('Beatriz', 'Pérez', '2019-08-10', 'Elena Pérez', '7777-0003', '01234567-3', 1),
('Daniel', 'Gómez', '2019-11-25', 'Ricardo Gómez', '7777-0004', '01234567-4', 1),
('Elena', 'Flores', '2019-01-30', 'Carmen Flores', '7777-0005', '01234567-5', 1),

-- 1° "B" (IDs 6-10)
('Fernando', 'Rivera', '2019-02-14', 'Luis Rivera', '7777-0006', '01234567-6', 1),
('Gabriela', 'Reyes', '2019-06-18', 'Ana Reyes', '7777-0007', '01234567-7', 1),
('Héctor', 'Cruz', '2019-09-22', 'Mario Cruz', '7777-0008', '01234567-8', 1),
('Isabel', 'Díaz', '2019-12-05', 'Juana Díaz', '7777-0009', '01234567-9', 1),
('Javier', 'Ortiz', '2019-04-11', 'Pedro Ortiz', '7777-0010', '01234568-0', 1),

-- 2° "A" (IDs 11-15)
('Karla', 'Méndez', '2018-03-12', 'Sofía Méndez', '7777-0011', '01234568-1', 1),
('Luis', 'Alvarado', '2018-07-19', 'Carlos Alvarado', '7777-0012', '01234568-2', 1),
('María', 'García', '2018-10-21', 'Rosa García', '7777-0013', '01234568-3', 1),
('Néstor', 'Rodríguez', '2018-01-08', 'Jorge Rodríguez', '7777-0014', '01234568-4', 1),
('Olga', 'Hernández', '2018-05-15', 'Silvia Hernández', '7777-0015', '01234568-5', 1),

-- 2° "B" (IDs 16-20)
('Pablo', 'Sánchez', '2018-08-25', 'Julio Sánchez', '7777-0016', '01234568-6', 1),
('Quintín', 'Ramírez', '2018-11-30', 'Teresa Ramírez', '7777-0017', '01234568-7', 1),
('Rosa', 'Chávez', '2018-02-17', 'Victor Chávez', '7777-0018', '01234568-8', 1),
('Sergio', 'Mejía', '2018-06-22', 'Diana Mejía', '7777-0019', '01234568-9', 1),
('Teresa', 'Castillo', '2018-09-14', 'Manuel Castillo', '7777-0020', '01234569-0', 1),

-- 3° "A" (IDs 21-25)
('Ulises', 'Morales', '2017-04-05', 'Oscar Morales', '7777-0021', '01234569-1', 1),
('Verónica', 'Aguilar', '2017-08-11', 'Gladys Aguilar', '7777-0022', '01234569-2', 1),
('William', 'Navarro', '2017-12-20', 'Rubén Navarro', '7777-0023', '01234569-3', 1),
('Ximena', 'Escobar', '2017-01-28', 'Sonia Escobar', '7777-0024', '01234569-4', 1),
('Yolanda', 'Romero', '2017-05-09', 'Héctor Romero', '7777-0025', '01234569-5', 1),

-- 3° "B" (IDs 26-30)
('Zacarías', 'Guzmán', '2017-10-15', 'Evelyn Guzmán', '7777-0026', '01234569-6', 1),
('Andrea', 'Orellana', '2017-02-23', 'Hugo Orellana', '7777-0027', '01234569-7', 1),
('Benjamín', 'Portillo', '2017-07-07', 'Lorena Portillo', '7777-0028', '01234569-8', 1),
('Camila', 'Vásquez', '2017-11-18', 'Rafael Vásquez', '7777-0029', '01234569-9', 1),
('Diego', 'Zelaya', '2017-03-30', 'Beatriz Zelaya', '7777-0030', '01234570-0', 1),

-- 4° "A" (IDs 31-35)
('Eduardo', 'Molina', '2016-06-12', 'Fátima Molina', '7777-0031', '01234570-1', 1),
('Fátima', 'Blanco', '2016-09-25', 'Mauricio Blanco', '7777-0032', '01234570-2', 1),
('Gerardo', 'Pineda', '2016-01-04', 'Lilian Pineda', '7777-0033', '01234570-3', 1),
('Hilda', 'Rivas', '2016-05-17', 'Nelson Rivas', '7777-0034', '01234570-4', 1),
('Ignacio', 'Cortez', '2016-10-29', 'Alicia Cortez', '7777-0035', '01234570-5', 1),

-- 4° "B" (IDs 36-40)
('Julia', 'Marroquín', '2016-02-08', 'Ernesto Marroquín', '7777-0036', '01234570-6', 1),
('Kevin', 'Soto', '2016-07-21', 'Nuria Soto', '7777-0037', '01234570-7', 1),
('Laura', 'Miranda', '2016-12-02', 'Gerardo Miranda', '7777-0038', '01234570-8', 1),
('Mario', 'Coreas', '2016-04-14', 'Paty Coreas', '7777-0039', '01234570-9', 1),
('Nidia', 'Lara', '2016-08-27', 'Roberto Lara', '7777-0040', '01234571-0', 1),

-- 5° "A" (IDs 41-45)
('Omar', 'Arias', '2015-01-10', 'Cecilia Arias', '7777-0041', '01234571-1', 1),
('Patricia', 'Segovia', '2015-05-23', 'Gilberto Segovia', '7777-0042', '01234571-2', 1),
('Raúl', 'Campos', '2015-09-06', 'Maritza Campos', '7777-0043', '01234571-3', 1),
('Silvia', 'Peña', '2015-12-19', 'Rogelio Peña', '7777-0044', '01234571-4', 1),
('Tomás', 'Valle', '2015-03-01', 'Karina Valle', '7777-0045', '01234571-5', 1),

-- 5° "B" (IDs 46-50)
('Ursula', 'Cabrera', '2015-07-13', 'Armando Cabrera', '7777-0046', '01234571-6', 1),
('Víctor', 'Rosales', '2015-10-26', 'Edith Rosales', '7777-0047', '01234571-7', 1),
('Wendy', 'Salazar', '2015-02-07', 'Rodrigo Salazar', '7777-0048', '01234571-8', 1),
('René', 'Fuentes', '2015-06-20', 'Roxana Fuentes', '7777-0049', '01234571-9', 1),
('Yaritza', 'Ramos', '2015-11-03', 'Alfonso Ramos', '7777-0050', '01234572-0', 1),

-- 6° "A" (IDs 51-55)
('Antonio', 'Santos', '2014-04-16', 'Berta Santos', '7777-0051', '01234572-1', 1),
('Blanca', 'Paz', '2014-08-29', 'César Paz', '7777-0052', '01234572-2', 1),
('Cristian', 'Carranza', '2014-01-11', 'Dinora Carranza', '7777-0053', '01234572-3', 1),
('Diana', 'Medina', '2014-05-24', 'Eliseo Medina', '7777-0054', '01234572-4', 1),
('Esteban', 'Urrutia', '2014-09-06', 'Flor Urrutia', '7777-0055', '01234572-5', 1),

-- 6° "B" (IDs 56-60)
('Fabiola', 'Linares', '2014-12-18', 'Guillermo Linares', '7777-0056', '01234572-6', 1),
('Gustavo', 'Zepeda', '2014-03-02', 'Hilda Zepeda', '7777-0057', '01234572-7', 1),
('Helena', 'Ochoa', '2014-07-15', 'Iván Ochoa', '7777-0058', '01234572-8', 1),
('Iván', 'Meléndez', '2014-10-27', 'Josefina Meléndez', '7777-0059', '01234572-9', 1),
('Jazmín', 'Bermúdez', '2014-02-09', 'Kevin Bermúdez', '7777-0060', '01234573-0', 1),

-- 7° "A" (IDs 61-65)
('Kenia', 'Luna', '2013-06-22', 'Lázaro Luna', '7777-0061', '01234573-1', 1),
('Leonel', 'Cáceres', '2013-11-04', 'Mirna Cáceres', '7777-0062', '01234573-2', 1),
('Milena', 'Sosa', '2013-04-17', 'Noel Sosa', '7777-0063', '01234573-3', 1),
('Nadia', 'Guillén', '2013-08-30', 'Oswaldo Guillén', '7777-0064', '01234573-4', 1),
('Oscar', 'Rendón', '2013-01-12', 'Paola Rendón', '7777-0065', '01234573-5', 1),

-- 7° "B" (IDs 66-70)
('Pamela', 'Maldonado', '2013-05-25', 'Quirino Maldonado', '7777-0066', '01234573-6', 1),
('Quinto', 'Funes', '2013-09-07', 'Rebeca Funes', '7777-0067', '01234573-7', 1),
('Raquel', 'Pacheco', '2013-12-20', 'Samuel Pacheco', '7777-0068', '01234573-8', 1),
('Saúl', 'Navas', '2013-03-05', 'Tania Navas', '7777-0069', '01234573-9', 1),
('Tatiana', 'Vargas', '2013-07-18', 'Urbano Vargas', '7777-0070', '01234574-0', 1),

-- 8° "A" (IDs 71-75)
('Uriel', 'Cervantes', '2012-10-31', 'Valeria Cervantes', '7777-0071', '01234574-1', 1),
('Vanessa', 'Montoya', '2012-02-13', 'Waldo Montoya', '7777-0072', '01234574-2', 1),
('Wilfredo', 'Mora', '2012-06-26', 'Xenia Mora', '7777-0073', '01234574-3', 1),
('Xenia', 'Aguirre', '2012-11-08', 'Yamil Aguirre', '7777-0074', '01234574-4', 1),
('Yamil', 'Ponce', '2012-04-21', 'Zuleima Ponce', '7777-0075', '01234574-5', 1),

-- 8° "B" (IDs 76-80)
('Zuleima', 'Gálvez', '2012-09-03', 'Arturo Gálvez', '7777-0076', '01234574-6', 1),
('Arturo', 'Salinas', '2012-12-16', 'Brenda Salinas', '7777-0077', '01234574-7', 1),
('Brenda', 'Munguía', '2012-03-08', 'Cristóbal Munguía', '7777-0078', '01234574-8', 1),
('Cristóbal', 'Arévalo', '2012-07-21', 'Doris Arévalo', '7777-0079', '01234574-9', 1),
('Doris', 'Quintanilla', '2012-10-02', 'Enrique Quintanilla', '7777-0080', '01234575-0', 1),

-- 9° "A" (IDs 81-85)
('Enrique', 'Cárcamo', '2011-01-14', 'Fidelia Cárcamo', '7777-0081', '01234575-1', 1),
('Fidelia', 'Villalobos', '2011-05-27', 'Gonzalo Villalobos', '7777-0082', '01234575-2', 1),
('Gonzalo', 'Escalante', '2011-09-09', 'Hortensia Escalante', '7777-0083', '01234575-3', 1),
('Hortensia', 'Lazo', '2011-12-22', 'Isidro Lazo', '7777-0084', '01234575-4', 1),
('Isidro', 'Bonilla', '2011-03-10', 'Julia Bonilla', '7777-0085', '01234575-5', 1),

-- 9° "B" (IDs 86-90)
('Jorge', 'Menéndez', '2011-07-23', 'Karla Menéndez', '7777-0086', '01234575-6', 1),
('Karla', 'Portillo', '2011-11-05', 'Leonardo Portillo', '7777-0087', '01234575-7', 1),
('Leonardo', 'Guevara', '2011-04-18', 'Mónica Guevara', '7777-0088', '01234575-8', 1),
('Mónica', 'Ayala', '2011-08-31', 'Narciso Ayala', '7777-0089', '01234575-9', 1),
('Narciso', 'Valdés', '2011-01-02', 'Olivia Valdés', '7777-0090', '01234576-0', 1);


-- ====================================================================
-- 2. MATRICULAR A LOS 90 ESTUDIANTES EN SU GRADO CORRESPONDIENTE
-- ====================================================================
-- Se asume que el año lectivo es 2026 y el usuario administrador que 
-- registra tiene el id_usuario = 1.
INSERT INTO `matricula` (`id_estudiante`, `id_grado`, `año_lectivo`, `estado`, `fecha_matricula`, `id_usuario_registra`) VALUES
-- 1° "A"
(1, '1° "A"', 2026, 'activa', CURDATE(), 1),
(2, '1° "A"', 2026, 'activa', CURDATE(), 1),
(3, '1° "A"', 2026, 'activa', CURDATE(), 1),
(4, '1° "A"', 2026, 'activa', CURDATE(), 1),
(5, '1° "A"', 2026, 'activa', CURDATE(), 1),

-- 1° "B"
(6, '1° "B"', 2026, 'activa', CURDATE(), 1),
(7, '1° "B"', 2026, 'activa', CURDATE(), 1),
(8, '1° "B"', 2026, 'activa', CURDATE(), 1),
(9, '1° "B"', 2026, 'activa', CURDATE(), 1),
(10, '1° "B"', 2026, 'activa', CURDATE(), 1),

-- 2° "A"
(11, '2° "A"', 2026, 'activa', CURDATE(), 1),
(12, '2° "A"', 2026, 'activa', CURDATE(), 1),
(13, '2° "A"', 2026, 'activa', CURDATE(), 1),
(14, '2° "A"', 2026, 'activa', CURDATE(), 1),
(15, '2° "A"', 2026, 'activa', CURDATE(), 1),

-- 2° "B"
(16, '2° "B"', 2026, 'activa', CURDATE(), 1),
(17, '2° "B"', 2026, 'activa', CURDATE(), 1),
(18, '2° "B"', 2026, 'activa', CURDATE(), 1),
(19, '2° "B"', 2026, 'activa', CURDATE(), 1),
(20, '2° "B"', 2026, 'activa', CURDATE(), 1),

-- 3° "A"
(21, '3° "A"', 2026, 'activa', CURDATE(), 1),
(22, '3° "A"', 2026, 'activa', CURDATE(), 1),
(23, '3° "A"', 2026, 'activa', CURDATE(), 1),
(24, '3° "A"', 2026, 'activa', CURDATE(), 1),
(25, '3° "A"', 2026, 'activa', CURDATE(), 1),

-- 3° "B"
(26, '3° "B"', 2026, 'activa', CURDATE(), 1),
(27, '3° "B"', 2026, 'activa', CURDATE(), 1),
(28, '3° "B"', 2026, 'activa', CURDATE(), 1),
(29, '3° "B"', 2026, 'activa', CURDATE(), 1),
(30, '3° "B"', 2026, 'activa', CURDATE(), 1),

-- 4° "A"
(31, '4° "A"', 2026, 'activa', CURDATE(), 1),
(32, '4° "A"', 2026, 'activa', CURDATE(), 1),
(33, '4° "A"', 2026, 'activa', CURDATE(), 1),
(34, '4° "A"', 2026, 'activa', CURDATE(), 1),
(35, '4° "A"', 2026, 'activa', CURDATE(), 1),

-- 4° "B"
(36, '4° "B"', 2026, 'activa', CURDATE(), 1),
(37, '4° "B"', 2026, 'activa', CURDATE(), 1),
(38, '4° "B"', 2026, 'activa', CURDATE(), 1),
(39, '4° "B"', 2026, 'activa', CURDATE(), 1),
(40, '4° "B"', 2026, 'activa', CURDATE(), 1),

-- 5° "A"
(41, '5° "A"', 2026, 'activa', CURDATE(), 1),
(42, '5° "A"', 2026, 'activa', CURDATE(), 1),
(43, '5° "A"', 2026, 'activa', CURDATE(), 1),
(44, '5° "A"', 2026, 'activa', CURDATE(), 1),
(45, '5° "A"', 2026, 'activa', CURDATE(), 1),

-- 5° "B"
(46, '5° "B"', 2026, 'activa', CURDATE(), 1),
(47, '5° "B"', 2026, 'activa', CURDATE(), 1),
(48, '5° "B"', 2026, 'activa', CURDATE(), 1),
(49, '5° "B"', 2026, 'activa', CURDATE(), 1),
(50, '5° "B"', 2026, 'activa', CURDATE(), 1),

-- 6° "A"
(51, '6° "A"', 2026, 'activa', CURDATE(), 1),
(52, '6° "A"', 2026, 'activa', CURDATE(), 1),
(53, '6° "A"', 2026, 'activa', CURDATE(), 1),
(54, '6° "A"', 2026, 'activa', CURDATE(), 1),
(55, '6° "A"', 2026, 'activa', CURDATE(), 1),

-- 6° "B"
(56, '6° "B"', 2026, 'activa', CURDATE(), 1),
(57, '6° "B"', 2026, 'activa', CURDATE(), 1),
(58, '6° "B"', 2026, 'activa', CURDATE(), 1),
(59, '6° "B"', 2026, 'activa', CURDATE(), 1),
(60, '6° "B"', 2026, 'activa', CURDATE(), 1),

-- 7° "A"
(61, '7° "A"', 2026, 'activa', CURDATE(), 1),
(62, '7° "A"', 2026, 'activa', CURDATE(), 1),
(63, '7° "A"', 2026, 'activa', CURDATE(), 1),
(64, '7° "A"', 2026, 'activa', CURDATE(), 1),
(65, '7° "A"', 2026, 'activa', CURDATE(), 1),

-- 7° "B"
(66, '7° "B"', 2026, 'activa', CURDATE(), 1),
(67, '7° "B"', 2026, 'activa', CURDATE(), 1),
(68, '7° "B"', 2026, 'activa', CURDATE(), 1),
(69, '7° "B"', 2026, 'activa', CURDATE(), 1),
(70, '7° "B"', 2026, 'activa', CURDATE(), 1),

-- 8° "A"
(71, '8° "A"', 2026, 'activa', CURDATE(), 1),
(72, '8° "A"', 2026, 'activa', CURDATE(), 1),
(73, '8° "A"', 2026, 'activa', CURDATE(), 1),
(74, '8° "A"', 2026, 'activa', CURDATE(), 1),
(75, '8° "A"', 2026, 'activa', CURDATE(), 1),

-- 8° "B"
(76, '8° "B"', 2026, 'activa', CURDATE(), 1),
(77, '8° "B"', 2026, 'activa', CURDATE(), 1),
(78, '8° "B"', 2026, 'activa', CURDATE(), 1),
(79, '8° "B"', 2026, 'activa', CURDATE(), 1),
(80, '8° "B"', 2026, 'activa', CURDATE(), 1),

-- 9° "A"
(81, '9° "A"', 2026, 'activa', CURDATE(), 1),
(82, '9° "A"', 2026, 'activa', CURDATE(), 1),
(83, '9° "A"', 2026, 'activa', CURDATE(), 1),
(84, '9° "A"', 2026, 'activa', CURDATE(), 1),
(85, '9° "A"', 2026, 'activa', CURDATE(), 1),

-- 9° "B"
(86, '9° "B"', 2026, 'activa', CURDATE(), 1),
(87, '9° "B"', 2026, 'activa', CURDATE(), 1),
(88, '9° "B"', 2026, 'activa', CURDATE(), 1),
(89, '9° "B"', 2026, 'activa', CURDATE(), 1),
(90, '9° "B"', 2026, 'activa', CURDATE(), 1);