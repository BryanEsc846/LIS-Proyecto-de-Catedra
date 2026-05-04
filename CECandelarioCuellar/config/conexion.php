<?php
// config/conexion.php

$host = 'sql110.infinityfree.com';        // ← Tu Database Hostname
$dbname = 'if0_41822307_c_e_c_c';         // ← Full Database Name
$user = 'if0_41822307';                   // ← Database Username
$pass = 'flFzIPNa0ct0Rph';                 // ← Database Password

try {
    $conexion = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass
    );
    
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En hosting gratuito es mejor no mostrar errores internos
    error_log("DB Error: " . $e->getMessage());
    header("Location: ../errores/500.php");
    exit;
}
?>