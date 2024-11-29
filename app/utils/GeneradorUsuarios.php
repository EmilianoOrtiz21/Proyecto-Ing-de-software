<?php
// Función para generar un nombre de usuario
function generarUsuario($nombre) {
    $numero = rand(1000, 9999); // Número aleatorio de 4 dígitos
    return ucfirst($nombre) . $numero; // Combina el nombre con el número
}

// Función para generar una contraseña
function generarContrasena() {
    $randomNumber = rand(100, 999); // Número aleatorio de 3 dígitos
    $randomLetter = chr(rand(65, 90)); // Letra aleatoria (mayúscula)
    $specialChars = ['-', '_', '@', '#']; // Caracteres especiales posibles
    $specialChar = $specialChars[array_rand($specialChars)]; // Selecciona uno aleatorio
    return "Porte" . $randomNumber . $specialChar . rand(0, 9); // Combina todo
}

// Procesamiento cuando el botón es presionado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? 'Conductor'; // Nombre base para el usuario
    $usuario = generarUsuario($nombre);
    $contrasena = generarContrasena();
}
?>
