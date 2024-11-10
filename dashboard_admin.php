<?php
session_start();
include("Sesion.php");

$sesion = new Sesion();

if (!$sesion->estaActiva() || !$sesion->esAdmin()) {
    header("Location: login.php");
    exit();
}

echo "Bienvenido Administrador: " . $sesion->obtenerUsuario();
?>
<a href="crear_conductor.php">Crear Conductor</a>
<a href="repartir_paquetes.php">Repartir Paquetes</a>
<a href="ver_reportes.php">Ver Reportes</a>
