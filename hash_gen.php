<?php
$contraseñaOriginal = 'test'; // La contraseña original que quieres usar
$hashGenerado = password_hash($contraseñaOriginal, PASSWORD_BCRYPT);

echo "Nuevo hash generado: " . $hashGenerado . "<br>";
?>
