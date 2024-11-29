<?php
session_start();

include_once __DIR__ . '/../app/models/Sesion.php';

$sesion = new Sesion();

// Verificar si la sesión está activa y el usuario es administrador
if (!$sesion->estaActiva() || !$sesion->esAdmin()) {
    header("Location: login.php");
    exit();
}

// Mostrar el nombre del administrador
echo "Bienvenido Administrador: " . htmlspecialchars($sesion->obtenerUsuario());
?>

<!-- Opciones del administrador -->
<a href="crear_conductor.php">Crear Conductor</a>
<a href="repartir_paquetes.php">Repartir Paquetes</a>
<a href="ver_reportes.php">Ver Reportes</a>
