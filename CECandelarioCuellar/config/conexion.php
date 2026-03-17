<?php
// config/conexion.php

try {
    // Creamos la variable $conexion
    $conexion = new PDO(
        "mysql:host=localhost;dbname=sigaa_web;charset=utf8mb4", // Asegúrate que el nombre de la BD sea 'sigaa_web' como en tu SQL
        "root",
        ""
    );
    
    // Configuramos errores y modo de fetch
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla la conexión, redirigimos a la página de error
    // Usamos una ruta absoluta relativa al dominio para evitar errores de '../'
    header("Location: /CECandelarioCuellar/errores/500.php"); 
    exit;
}
?>