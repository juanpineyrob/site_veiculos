<?php
// Función para verificar si el usuario está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Función para redirigir si no está logado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para generar hash de senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Función para verificar senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Función para upload de imagem
function uploadImage($file, $uploadDir = 'uploads/') {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

// Función para deletar imagem
function deleteImage($filename, $uploadDir = 'uploads/') {
    $filepath = $uploadDir . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// Función para formatar data
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Función para formatar preço
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}
?> 