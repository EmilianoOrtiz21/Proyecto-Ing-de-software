<?php
// Hash recién actualizado en la base de datos
$hashEnBD = '$nuevo_hash'; // Reemplaza con el hash que ingresaste en la base de datos

// Contraseña que quieres verificar
$contraseñaIngresada = 'test'; // La contraseña original

if (password_verify($contraseñaIngresada, $hashEnBD)) {
    echo "La contraseña coincide con el nuevo hash.<br>";
} else {
    echo "La contraseña NO coincide con el nuevo hash.<br>";
}
?>
