<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../app/models/conexionBD.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/AdminPaquetes.php';

$adminPaquetes = new AdminPaquetes();
$paquetes = $adminPaquetes->verPaquetesAsignados($idConductor);

// Retornar la respuesta JSON
echo $paquetes;
?>
