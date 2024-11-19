<?php
session_start();
include("Sesion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $sesion = new Sesion();

    if ($sesion->iniciarSesion($nombreUsuario, $contrasena)) {
        // Redirigir según el rol
        if ($sesion->esAdmin()) {
            header("Location: dashboard_admin.php");
        } elseif ($sesion->esConductor()) {
            header("Location: dashboard_conductor.php");
        } else {
            header("Location: dashboard_cliente.php");
        }
    } else {
        echo "Credenciales incorrectas";
    }
}
?>
<form method="POST">
    <label for="usuario">Usuario:</label>
    <input type="text" name="usuario" required>
    
    <label for="contrasena">Contraseña:</label>
    <input type="password" name="contrasena" required>

    <button type="submit">Iniciar sesión</button>
</form>
