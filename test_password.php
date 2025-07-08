<?php
// Script de prueba para verificar la contraseña del administrador

$password = 'admin123';
$stored_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Contraseña: " . $password . "\n";
echo "Hash almacenado: " . $stored_hash . "\n";
echo "Verificación: " . (password_verify($password, $stored_hash) ? "CORRECTO" : "INCORRECTO") . "\n";

// Generar un nuevo hash para admin123
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "Nuevo hash para admin123: " . $new_hash . "\n";
echo "Verificación con nuevo hash: " . (password_verify($password, $new_hash) ? "CORRECTO" : "INCORRECTO") . "\n";
?> 