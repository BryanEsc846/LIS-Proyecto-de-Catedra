Ingresar estos datos en la tabla usuario para poder hacer login como administrador, la password_hash es admin123
INSERT INTO usuario (
    nombre, 
    apellido, 
    email, 
    password_hash, 
    rol, 
    activo
) VALUES (
    'admin', 
    'admin', 
    'admin@gmail.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',         
    'administrador', 
    1
);